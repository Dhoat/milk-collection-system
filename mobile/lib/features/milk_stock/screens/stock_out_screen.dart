import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_button.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/widgets/error_banner.dart';
import '../providers/milk_stock_provider.dart';

class StockOutScreen extends StatefulWidget {
  const StockOutScreen({super.key});

  @override
  State<StockOutScreen> createState() => _StockOutScreenState();
}

class _StockOutScreenState extends State<StockOutScreen> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _dateController;
  late TextEditingController _quantityController;
  late TextEditingController _reasonController;
  late TextEditingController _fatController;
  late TextEditingController _snfController;
  late TextEditingController _notesController;

  double _requestedQuantity = 0.0;

  @override
  void initState() {
    super.initState();
    _dateController = TextEditingController(
      text: DateTime.now().toString().substring(0, 10),
    );
    _quantityController = TextEditingController();
    _reasonController = TextEditingController();
    _fatController = TextEditingController();
    _snfController = TextEditingController();
    _notesController = TextEditingController();

    _quantityController.addListener(_onQuantityChanged);
  }

  @override
  void dispose() {
    _quantityController.removeListener(_onQuantityChanged);
    _dateController.dispose();
    _quantityController.dispose();
    _reasonController.dispose();
    _fatController.dispose();
    _snfController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  void _onQuantityChanged() {
    final qty = double.tryParse(_quantityController.text.trim()) ?? 0.0;
    setState(() {
      _requestedQuantity = qty;
    });
  }

  void _selectDate() async {
    final now = DateTime.now();
    final initialDate = DateTime.tryParse(_dateController.text) ?? now;
    final picked = await showDatePicker(
      context: context,
      initialDate: initialDate,
      firstDate: DateTime(now.year - 1),
      lastDate: DateTime(now.year + 1),
    );
    if (picked != null) {
      setState(() {
        _dateController.text = picked.toString().substring(0, 10);
      });
    }
  }

  void _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;

    final stockProvider = Provider.of<MilkStockProvider>(context, listen: false);
    final available = stockProvider.availableStock;

    if (_requestedQuantity > available) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Stock OUT quantity (${_requestedQuantity.toStringAsFixed(1)}L) cannot exceed available stock (${available.toStringAsFixed(1)}L).',
          ),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final fat = double.tryParse(_fatController.text.trim());
    final snf = double.tryParse(_snfController.text.trim());

    final payload = <String, dynamic>{
      'transaction_date': _dateController.text.trim(),
      'quantity': _requestedQuantity,
      'source_or_reason': _reasonController.text.trim(),
      'fat': fat,
      'snf': snf,
      'notes': _notesController.text.trim().isNotEmpty ? _notesController.text.trim() : null,
    };

    final success = await stockProvider.recordStockOut(payload);

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Stock OUT transaction recorded successfully!'),
          backgroundColor: Color(0xFF059669),
        ),
      );
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final stockProvider = Provider.of<MilkStockProvider>(context);
    final availableStock = stockProvider.availableStock;
    final remainingStock = availableStock - _requestedQuantity;
    final isOverStock = _requestedQuantity > availableStock;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Record Stock OUT'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (stockProvider.errorMessage != null) ...[
                  ErrorBanner(
                    message: stockProvider.errorMessage!,
                    onDismiss: () => stockProvider.clearMessages(),
                  ),
                  const SizedBox(height: 16),
                ],

                // Live Stock Balance Calculation Card
                Card(
                  color: isOverStock ? const Color(0xFFFEE2E2) : const Color(0xFFECFDF5),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                    side: BorderSide(
                      color: isOverStock ? const Color(0xFFFECACA) : const Color(0xFFA7F3D0),
                    ),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Icon(
                              isOverStock ? Icons.warning_amber_rounded : Icons.calculate_outlined,
                              color: isOverStock ? const Color(0xFFDC2626) : const Color(0xFF059669),
                            ),
                            const SizedBox(width: 8),
                            Text(
                              isOverStock ? 'Insufficient Available Stock' : 'Stock Balance Preview',
                              style: TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                                color: isOverStock ? const Color(0xFF991B1B) : const Color(0xFF065F46),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            _buildPreviewStat('Current Stock', '${availableStock.toStringAsFixed(1)} L'),
                            _buildPreviewStat(
                              'Stock Out Qty',
                              '-${_requestedQuantity.toStringAsFixed(1)} L',
                              color: const Color(0xFFDC2626),
                            ),
                            _buildPreviewStat(
                              'Remaining Stock',
                              '${remainingStock.toStringAsFixed(1)} L',
                              isBold: true,
                              color: remainingStock < 0 ? const Color(0xFFDC2626) : const Color(0xFF059669),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 20),

                // Date Picker Field
                InkWell(
                  onTap: _selectDate,
                  child: IgnorePointer(
                    child: CustomTextField(
                      controller: _dateController,
                      label: 'Transaction Date *',
                      prefixIcon: const Icon(Icons.calendar_today, size: 20),
                      errorText: stockProvider.validationErrors?['transaction_date']?.first,
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                // Stock Out Quantity Field
                CustomTextField(
                  controller: _quantityController,
                  label: 'Stock OUT Quantity (Litres) *',
                  hint: 'e.g., 50.0',
                  keyboardType: const TextInputType.numberWithOptions(decimal: true),
                  prefixIcon: const Icon(Icons.water_drop, size: 20),
                  errorText: stockProvider.validationErrors?['quantity']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) {
                      return 'Stock OUT quantity is required';
                    }
                    final numVal = double.tryParse(val.trim());
                    if (numVal == null || numVal <= 0) {
                      return 'Quantity must be greater than zero';
                    }
                    if (numVal > availableStock) {
                      return 'Exceeds current available stock (${availableStock.toStringAsFixed(1)}L)';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                // Source or Reason Field
                CustomTextField(
                  controller: _reasonController,
                  label: 'Destination / Reason *',
                  hint: 'e.g., Processing to Butter, Distribution to Shop #2, Spill Loss',
                  prefixIcon: const Icon(Icons.description_outlined, size: 20),
                  errorText: stockProvider.validationErrors?['source_or_reason']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) {
                      return 'Destination or reason is required';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                // Fat & SNF Fields in a Row
                Row(
                  children: [
                    Expanded(
                      child: CustomTextField(
                        controller: _fatController,
                        label: 'Fat (%) (Optional)',
                        hint: 'e.g., 4.5',
                        keyboardType: const TextInputType.numberWithOptions(decimal: true),
                        prefixIcon: const Icon(Icons.percent, size: 18),
                        errorText: stockProvider.validationErrors?['fat']?.first,
                        validator: (val) {
                          if (val != null && val.trim().isNotEmpty) {
                            final numVal = double.tryParse(val.trim());
                            if (numVal == null || numVal < 0 || numVal > 100) {
                              return 'Fat 0-100%';
                            }
                          }
                          return null;
                        },
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: CustomTextField(
                        controller: _snfController,
                        label: 'SNF (%) (Optional)',
                        hint: 'e.g., 8.5',
                        keyboardType: const TextInputType.numberWithOptions(decimal: true),
                        prefixIcon: const Icon(Icons.pie_chart_outline, size: 18),
                        errorText: stockProvider.validationErrors?['snf']?.first,
                        validator: (val) {
                          if (val != null && val.trim().isNotEmpty) {
                            final numVal = double.tryParse(val.trim());
                            if (numVal == null || numVal < 0 || numVal > 100) {
                              return 'SNF 0-100%';
                            }
                          }
                          return null;
                        },
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Remarks / Notes Field
                CustomTextField(
                  controller: _notesController,
                  label: 'Remarks / Notes',
                  hint: 'Additional comments regarding stock reduction',
                  maxLines: 2,
                  prefixIcon: const Icon(Icons.note_alt_outlined, size: 20),
                  errorText: stockProvider.validationErrors?['notes']?.first,
                ),
                const SizedBox(height: 28),

                CustomButton(
                  text: 'Submit Stock OUT Transaction',
                  icon: Icons.check_circle_outline,
                  backgroundColor: const Color(0xFFDC2626),
                  isLoading: stockProvider.isSaving,
                  onPressed: isOverStock ? null : _handleSubmit,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildPreviewStat(String label, String value, {bool isBold = false, Color? color}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 11,
            color: AppTheme.textSecondary,
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 2),
        Text(
          value,
          style: TextStyle(
            fontSize: 14,
            fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
            color: color ?? AppTheme.textPrimary,
          ),
        ),
      ],
    );
  }
}
