import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_button.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/widgets/error_banner.dart';
import '../../villages/providers/village_provider.dart';
import '../models/milk_receiving_model.dart';
import '../providers/milk_receiving_provider.dart';

class AddEditMilkReceivingScreen extends StatefulWidget {
  final MilkReceivingModel? receiving;

  const AddEditMilkReceivingScreen({super.key, this.receiving});

  @override
  State<AddEditMilkReceivingScreen> createState() => _AddEditMilkReceivingScreenState();
}

class _AddEditMilkReceivingScreenState extends State<AddEditMilkReceivingScreen> {
  final _formKey = GlobalKey<FormState>();

  int? _selectedVillageId;
  late TextEditingController _dateController;
  String _shift = 'morning';
  late TextEditingController _receivedQuantityController;
  late TextEditingController _receivedFatController;
  late TextEditingController _receivedSnfController;
  late TextEditingController _notesController;

  @override
  void initState() {
    super.initState();
    final r = widget.receiving;

    _selectedVillageId = r?.villageId;
    _dateController = TextEditingController(
      text: r?.receivingDate ?? DateTime.now().toString().substring(0, 10),
    );
    _shift = r?.shift ?? 'morning';
    _receivedQuantityController = TextEditingController(
      text: r != null ? r.receivedQuantity.toString() : '',
    );
    _receivedFatController = TextEditingController(
      text: r?.receivedFat != null ? r!.receivedFat.toString() : '',
    );
    _receivedSnfController = TextEditingController(
      text: r?.receivedSnf != null ? r!.receivedSnf.toString() : '',
    );
    _notesController = TextEditingController(text: r?.notes ?? '');

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final villageProvider = Provider.of<VillageProvider>(context, listen: false);
      if (villageProvider.allVillages.isEmpty) {
        villageProvider.fetchVillages();
      }
      _checkCollectionSummary();
    });
  }

  @override
  void dispose() {
    _dateController.dispose();
    _receivedQuantityController.dispose();
    _receivedFatController.dispose();
    _receivedSnfController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  void _checkCollectionSummary() {
    if (_selectedVillageId != null && _dateController.text.isNotEmpty) {
      final receivingProvider = Provider.of<MilkReceivingProvider>(context, listen: false);
      receivingProvider.fetchCollectionSummary(
        villageId: _selectedVillageId!,
        date: _dateController.text.trim(),
        shift: _shift,
      );
    }
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
      _checkCollectionSummary();
    }
  }

  void _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedVillageId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please select a village for milk receiving.'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final receivingProvider = Provider.of<MilkReceivingProvider>(context, listen: false);

    final receivedQty = double.tryParse(_receivedQuantityController.text.trim()) ?? 0.0;
    final receivedFat = double.tryParse(_receivedFatController.text.trim());
    final receivedSnf = double.tryParse(_receivedSnfController.text.trim());

    final payload = <String, dynamic>{
      'village_id': _selectedVillageId,
      'receiving_date': _dateController.text.trim(),
      'shift': _shift,
      'received_quantity': receivedQty,
      'received_fat': receivedFat,
      'received_snf': receivedSnf,
      'notes': _notesController.text.trim().isNotEmpty ? _notesController.text.trim() : null,
    };

    bool success = false;
    if (widget.receiving == null) {
      success = await receivingProvider.createMilkReceiving(payload);
    } else {
      success = await receivingProvider.updateMilkReceiving(widget.receiving!.id, payload);
    }

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.receiving == null
                ? 'Milk receiving record created successfully!'
                : 'Milk receiving record updated successfully!',
          ),
          backgroundColor: AppTheme.primaryColor,
        ),
      );
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.receiving != null;
    final receivingProvider = Provider.of<MilkReceivingProvider>(context);
    final villageProvider = Provider.of<VillageProvider>(context);
    final villages = villageProvider.allVillages;
    final summary = receivingProvider.currentCollectionSummary;

    return Scaffold(
      appBar: AppBar(
        title: Text(isEditing ? 'Edit Milk Receiving' : 'New Milk Receiving'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (receivingProvider.errorMessage != null) ...[
                  ErrorBanner(
                    message: receivingProvider.errorMessage!,
                    onDismiss: () => receivingProvider.clearMessages(),
                  ),
                  const SizedBox(height: 16),
                ],

                // 1. Village Selection Dropdown
                DropdownButtonFormField<int>(
                  initialValue: _selectedVillageId,
                  decoration: InputDecoration(
                    labelText: 'Select Source Village *',
                    prefixIcon: const Icon(Icons.location_city, size: 20),
                    errorText: receivingProvider.validationErrors?['village_id']?.first,
                  ),
                  items: villages
                      .map((v) => DropdownMenuItem<int>(
                            value: v.id,
                            child: Text('${v.name} (${v.code})'),
                          ))
                      .toList(),
                  onChanged: (val) {
                    setState(() => _selectedVillageId = val);
                    _checkCollectionSummary();
                  },
                  validator: (val) {
                    if (val == null) return 'Please select a village';
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                // 2. Shift Selection Segment Choice
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Receiving Shift *',
                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: Color(0xFF374151)),
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        Expanded(
                          child: ChoiceChip(
                            avatar: const Icon(Icons.wb_sunny_outlined, size: 18),
                            label: const Center(child: Text('Morning Shift')),
                            selected: _shift == 'morning',
                            selectedColor: const Color(0xFFFFEDD5),
                            labelStyle: TextStyle(
                              color: _shift == 'morning' ? const Color(0xFFEA580C) : AppTheme.textPrimary,
                              fontWeight: FontWeight.bold,
                            ),
                            onSelected: (selected) {
                              if (selected) {
                                setState(() => _shift = 'morning');
                                _checkCollectionSummary();
                              }
                            },
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: ChoiceChip(
                            avatar: const Icon(Icons.nights_stay_outlined, size: 18),
                            label: const Center(child: Text('Evening Shift')),
                            selected: _shift == 'evening',
                            selectedColor: const Color(0xFFE0F2FE),
                            labelStyle: TextStyle(
                              color: _shift == 'evening' ? const Color(0xFF0284C7) : AppTheme.textPrimary,
                              fontWeight: FontWeight.bold,
                            ),
                            onSelected: (selected) {
                              if (selected) {
                                setState(() => _shift = 'evening');
                                _checkCollectionSummary();
                              }
                            },
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // 3. Date Selection Field
                InkWell(
                  onTap: _selectDate,
                  child: IgnorePointer(
                    child: CustomTextField(
                      controller: _dateController,
                      label: 'Receiving Date *',
                      prefixIcon: const Icon(Icons.calendar_today, size: 20),
                      errorText: receivingProvider.validationErrors?['receiving_date']?.first,
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                // 4. Expected Collection Summary Card (Auto-calculated from farmer collections)
                if (receivingProvider.isFetchingSummary)
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 8.0),
                    child: LinearProgressIndicator(),
                  )
                else if (summary != null)
                  Card(
                    color: const Color(0xFFEFF6FF),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                      side: const BorderSide(color: Color(0xFFBFDBFE)),
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(14.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Row(
                            children: [
                              Icon(Icons.analytics_outlined, color: Color(0xFF1D4ED8), size: 18),
                              SizedBox(width: 6),
                              Text(
                                'Expected Batch Metrics (Farmer Collections)',
                                style: TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.bold,
                                  color: Color(0xFF1E40AF),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                'Expected Qty: ${summary['expected_quantity'] ?? 0} L',
                                style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                              ),
                              Text(
                                'Farmers: ${summary['farmer_count'] ?? 0}',
                                style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                              ),
                            ],
                          ),
                          if (summary['expected_fat'] != null || summary['expected_snf'] != null) ...[
                            const SizedBox(height: 4),
                            Text(
                              'Expected Quality: Fat ${summary['expected_fat'] ?? '-'}% | SNF ${summary['expected_snf'] ?? '-'}%',
                              style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                            ),
                          ],
                        ],
                      ),
                    ),
                  ),
                const SizedBox(height: 16),

                // 5. Received Quantity Field
                CustomTextField(
                  controller: _receivedQuantityController,
                  label: 'Received Quantity (Litres) *',
                  hint: 'e.g., 150.0',
                  keyboardType: const TextInputType.numberWithOptions(decimal: true),
                  prefixIcon: const Icon(Icons.water_drop, size: 20),
                  errorText: receivingProvider.validationErrors?['received_quantity']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) {
                      return 'Received quantity is required';
                    }
                    final numVal = double.tryParse(val.trim());
                    if (numVal == null || numVal < 0) {
                      return 'Quantity must be zero or greater';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                // 6 & 7. Received Fat & SNF Fields in a Row
                Row(
                  children: [
                    Expanded(
                      child: CustomTextField(
                        controller: _receivedFatController,
                        label: 'Received Fat (%)',
                        hint: 'e.g., 4.5',
                        keyboardType: const TextInputType.numberWithOptions(decimal: true),
                        prefixIcon: const Icon(Icons.percent, size: 18),
                        errorText: receivingProvider.validationErrors?['received_fat']?.first,
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
                        controller: _receivedSnfController,
                        label: 'Received SNF (%)',
                        hint: 'e.g., 8.5',
                        keyboardType: const TextInputType.numberWithOptions(decimal: true),
                        prefixIcon: const Icon(Icons.pie_chart_outline, size: 18),
                        errorText: receivingProvider.validationErrors?['received_snf']?.first,
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

                // 8. Remarks / Notes Field
                CustomTextField(
                  controller: _notesController,
                  label: 'Remarks / Notes',
                  hint: 'e.g., Tanker arrived on time, quality verified',
                  maxLines: 2,
                  prefixIcon: const Icon(Icons.note_alt_outlined, size: 20),
                  errorText: receivingProvider.validationErrors?['notes']?.first,
                ),
                const SizedBox(height: 28),

                CustomButton(
                  text: isEditing ? 'Update Receiving Record' : 'Save Receiving Batch',
                  icon: isEditing ? Icons.save_outlined : Icons.check_circle_outline,
                  isLoading: receivingProvider.isSaving,
                  onPressed: _handleSubmit,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
