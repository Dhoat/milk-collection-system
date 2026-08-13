import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_button.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/widgets/error_banner.dart';
import '../../farmers/models/farmer_model.dart';
import '../../farmers/providers/farmer_provider.dart';
import '../models/milk_collection_model.dart';
import '../providers/milk_collection_provider.dart';
import '../widgets/milk_rate_calculator.dart';

class AddEditMilkCollectionScreen extends StatefulWidget {
  final MilkCollectionModel? collection;
  final FarmerModel? preSelectedFarmer;

  const AddEditMilkCollectionScreen({
    super.key,
    this.collection,
    this.preSelectedFarmer,
  });

  @override
  State<AddEditMilkCollectionScreen> createState() => _AddEditMilkCollectionScreenState();
}

class _AddEditMilkCollectionScreenState extends State<AddEditMilkCollectionScreen> {
  final _formKey = GlobalKey<FormState>();

  int? _selectedFarmerId;
  late TextEditingController _dateController;
  String _shift = 'morning';
  late TextEditingController _quantityController;
  late TextEditingController _fatController;
  late TextEditingController _snfController;
  late TextEditingController _rateController;
  late TextEditingController _notesController;

  @override
  void initState() {
    super.initState();
    final c = widget.collection;

    _selectedFarmerId = c?.farmerId ?? widget.preSelectedFarmer?.id;
    _dateController = TextEditingController(
      text: c?.collectionDate ?? DateTime.now().toString().substring(0, 10),
    );
    _shift = c?.shift ?? 'morning';
    _quantityController = TextEditingController(
      text: c != null ? c.milkQuantity.toString() : '',
    );
    _fatController = TextEditingController(
      text: c?.fat != null ? c!.fat.toString() : '',
    );
    _snfController = TextEditingController(
      text: c?.snf != null ? c!.snf.toString() : '',
    );
    _rateController = TextEditingController(
      text: c != null ? c.rate.toString() : '40.0', // Default initial base rate
    );
    _notesController = TextEditingController(text: c?.notes ?? '');

    _quantityController.addListener(_updateState);
    _fatController.addListener(_updateState);
    _snfController.addListener(_updateState);
    _rateController.addListener(_updateState);

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final farmerProvider = Provider.of<FarmerProvider>(context, listen: false);
      if (farmerProvider.allFarmers.isEmpty) {
        farmerProvider.fetchFarmers();
      }
    });
  }

  void _updateState() {
    setState(() {});
  }

  @override
  void dispose() {
    _quantityController.removeListener(_updateState);
    _fatController.removeListener(_updateState);
    _snfController.removeListener(_updateState);
    _rateController.removeListener(_updateState);

    _dateController.dispose();
    _quantityController.dispose();
    _fatController.dispose();
    _snfController.dispose();
    _rateController.dispose();
    _notesController.dispose();
    super.dispose();
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
    if (_selectedFarmerId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please select an active farmer for milk collection.'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final collectionProvider = Provider.of<MilkCollectionProvider>(context, listen: false);

    final quantity = double.tryParse(_quantityController.text.trim()) ?? 0.0;
    final fat = double.tryParse(_fatController.text.trim());
    final snf = double.tryParse(_snfController.text.trim());
    final rate = double.tryParse(_rateController.text.trim()) ?? 0.0;

    final payload = <String, dynamic>{
      'farmer_id': _selectedFarmerId,
      'collection_date': _dateController.text.trim(),
      'shift': _shift,
      'milk_quantity': quantity,
      'fat': fat,
      'snf': snf,
      'rate': rate,
      'notes': _notesController.text.trim().isNotEmpty ? _notesController.text.trim() : null,
    };

    bool success = false;
    if (widget.collection == null) {
      success = await collectionProvider.createMilkCollection(payload);
    } else {
      success = await collectionProvider.updateMilkCollection(widget.collection!.id, payload);
    }

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.collection == null
                ? 'Milk collection entry recorded successfully!'
                : 'Milk collection entry updated successfully!',
          ),
          backgroundColor: AppTheme.primaryColor,
        ),
      );
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.collection != null;
    final collectionProvider = Provider.of<MilkCollectionProvider>(context);
    final farmerProvider = Provider.of<FarmerProvider>(context);
    final farmers = farmerProvider.allFarmers;

    final currentQuantity = double.tryParse(_quantityController.text.trim()) ?? 0.0;
    final currentFat = double.tryParse(_fatController.text.trim());
    final currentSnf = double.tryParse(_snfController.text.trim());
    final currentRate = double.tryParse(_rateController.text.trim()) ?? 0.0;

    return Scaffold(
      appBar: AppBar(
        title: Text(isEditing ? 'Edit Milk Collection' : 'New Milk Collection'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (collectionProvider.errorMessage != null) ...[
                  ErrorBanner(
                    message: collectionProvider.errorMessage!,
                    onDismiss: () => collectionProvider.clearMessages(),
                  ),
                  const SizedBox(height: 16),
                ],

                // 1. Farmer Selection Dropdown
                DropdownButtonFormField<int>(
                  initialValue: _selectedFarmerId,
                  decoration: InputDecoration(
                    labelText: 'Select Farmer *',
                    prefixIcon: const Icon(Icons.person, size: 20),
                    errorText: collectionProvider.validationErrors?['farmer_id']?.first,
                  ),
                  items: farmers
                      .map((f) => DropdownMenuItem<int>(
                            value: f.id,
                            child: Text(
                              '${f.name} (${f.farmerCode}) - ${f.village?.name ?? ""}',
                              overflow: TextOverflow.ellipsis,
                            ),
                          ))
                      .toList(),
                  onChanged: (val) => setState(() => _selectedFarmerId = val),
                  validator: (val) {
                    if (val == null) return 'Please select a farmer';
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                // 2. Shift Selection Segment Button
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Collection Shift *',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w500,
                        color: Color(0xFF374151),
                      ),
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
                              if (selected) setState(() => _shift = 'morning');
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
                              if (selected) setState(() => _shift = 'evening');
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
                      label: 'Collection Date *',
                      prefixIcon: const Icon(Icons.calendar_today, size: 20),
                      errorText: collectionProvider.validationErrors?['collection_date']?.first,
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                // 4. Milk Quantity Field
                CustomTextField(
                  controller: _quantityController,
                  label: 'Milk Quantity (Litres) *',
                  hint: 'e.g., 25.5',
                  keyboardType: const TextInputType.numberWithOptions(decimal: true),
                  prefixIcon: const Icon(Icons.water_drop_outlined, size: 20),
                  errorText: collectionProvider.validationErrors?['milk_quantity']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) {
                      return 'Milk quantity is required';
                    }
                    final numVal = double.tryParse(val.trim());
                    if (numVal == null || numVal <= 0) {
                      return 'Quantity must be greater than 0';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                // 5 & 6. Fat & SNF Fields in a Row
                Row(
                  children: [
                    Expanded(
                      child: CustomTextField(
                        controller: _fatController,
                        label: 'Fat (%)',
                        hint: 'e.g., 4.5',
                        keyboardType: const TextInputType.numberWithOptions(decimal: true),
                        prefixIcon: const Icon(Icons.percent, size: 18),
                        errorText: collectionProvider.validationErrors?['fat']?.first,
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
                        label: 'SNF (%)',
                        hint: 'e.g., 8.5',
                        keyboardType: const TextInputType.numberWithOptions(decimal: true),
                        prefixIcon: const Icon(Icons.pie_chart_outline, size: 18),
                        errorText: collectionProvider.validationErrors?['snf']?.first,
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

                // 7. Rate Field
                CustomTextField(
                  controller: _rateController,
                  label: 'Rate per Litre (₹) *',
                  hint: 'e.g., 42.0',
                  keyboardType: const TextInputType.numberWithOptions(decimal: true),
                  prefixIcon: const Icon(Icons.currency_rupee, size: 20),
                  errorText: collectionProvider.validationErrors?['rate']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) {
                      return 'Rate is required';
                    }
                    final numVal = double.tryParse(val.trim());
                    if (numVal == null || numVal < 0) {
                      return 'Rate must be 0 or greater';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                // 8. Real-time Rate Calculator Card Preview
                MilkRateCalculator(
                  quantity: currentQuantity,
                  fat: currentFat,
                  snf: currentSnf,
                  rate: currentRate,
                  isFinal: false,
                ),
                const SizedBox(height: 16),

                // Notes Field
                CustomTextField(
                  controller: _notesController,
                  label: 'Remarks / Notes',
                  hint: 'e.g., Good quality fresh morning milk',
                  maxLines: 2,
                  prefixIcon: const Icon(Icons.note_alt_outlined, size: 20),
                  errorText: collectionProvider.validationErrors?['notes']?.first,
                ),
                const SizedBox(height: 28),

                CustomButton(
                  text: isEditing ? 'Update Milk Collection' : 'Save Milk Collection',
                  icon: isEditing ? Icons.save_outlined : Icons.check_circle_outline,
                  isLoading: collectionProvider.isSaving,
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
