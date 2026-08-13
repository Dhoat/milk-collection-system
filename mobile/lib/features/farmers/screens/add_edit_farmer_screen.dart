import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_button.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/widgets/error_banner.dart';
import '../../villages/providers/village_provider.dart';
import '../models/farmer_model.dart';
import '../providers/farmer_provider.dart';

class AddEditFarmerScreen extends StatefulWidget {
  final FarmerModel? farmer;

  const AddEditFarmerScreen({super.key, this.farmer});

  @override
  State<AddEditFarmerScreen> createState() => _AddEditFarmerScreenState();
}

class _AddEditFarmerScreenState extends State<AddEditFarmerScreen> {
  final _formKey = GlobalKey<FormState>();

  int? _selectedVillageId;
  late TextEditingController _farmerCodeController;
  late TextEditingController _nameController;
  late TextEditingController _fatherNameController;
  late TextEditingController _mobileController;
  late TextEditingController _alternateMobileController;
  late TextEditingController _addressController;
  String _gender = 'male';
  late TextEditingController _joiningDateController;
  late TextEditingController _bankNameController;
  late TextEditingController _accountNumberController;
  late TextEditingController _ifscCodeController;
  bool _status = true;

  @override
  void initState() {
    super.initState();
    final f = widget.farmer;
    _selectedVillageId = f?.villageId;
    _farmerCodeController = TextEditingController(text: f?.farmerCode ?? '');
    _nameController = TextEditingController(text: f?.name ?? '');
    _fatherNameController = TextEditingController(text: f?.fatherName ?? '');
    _mobileController = TextEditingController(text: f?.mobile ?? '');
    _alternateMobileController = TextEditingController(text: f?.alternateMobile ?? '');
    _addressController = TextEditingController(text: f?.address ?? '');
    _gender = f?.gender ?? 'male';
    _joiningDateController = TextEditingController(text: f?.joiningDate ?? DateTime.now().toString().substring(0, 10));
    _bankNameController = TextEditingController(text: f?.bankName ?? '');
    _accountNumberController = TextEditingController(text: f?.accountNumber ?? '');
    _ifscCodeController = TextEditingController(text: f?.ifscCode ?? '');
    _status = f?.status ?? true;

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final villageProvider = Provider.of<VillageProvider>(context, listen: false);
      if (villageProvider.allVillages.isEmpty) {
        villageProvider.fetchVillages();
      }
    });
  }

  @override
  void dispose() {
    _farmerCodeController.dispose();
    _nameController.dispose();
    _fatherNameController.dispose();
    _mobileController.dispose();
    _alternateMobileController.dispose();
    _addressController.dispose();
    _joiningDateController.dispose();
    _bankNameController.dispose();
    _accountNumberController.dispose();
    _ifscCodeController.dispose();
    super.dispose();
  }

  void _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedVillageId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please select a village for the farmer.'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final farmerProvider = Provider.of<FarmerProvider>(context, listen: false);

    final payload = <String, dynamic>{
      'village_id': _selectedVillageId,
      'farmer_code': _farmerCodeController.text.trim(),
      'name': _nameController.text.trim(),
      'father_name': _fatherNameController.text.trim().isNotEmpty ? _fatherNameController.text.trim() : null,
      'mobile': _mobileController.text.trim(),
      'alternate_mobile': _alternateMobileController.text.trim().isNotEmpty ? _alternateMobileController.text.trim() : null,
      'address': _addressController.text.trim().isNotEmpty ? _addressController.text.trim() : null,
      'gender': _gender,
      'joining_date': _joiningDateController.text.trim().isNotEmpty ? _joiningDateController.text.trim() : null,
      'bank_name': _bankNameController.text.trim().isNotEmpty ? _bankNameController.text.trim() : null,
      'account_number': _accountNumberController.text.trim().isNotEmpty ? _accountNumberController.text.trim() : null,
      'ifsc_code': _ifscCodeController.text.trim().isNotEmpty ? _ifscCodeController.text.trim() : null,
      'status': _status,
    };

    bool success = false;
    if (widget.farmer == null) {
      success = await farmerProvider.createFarmer(payload);
    } else {
      success = await farmerProvider.updateFarmer(widget.farmer!.id, payload);
    }

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.farmer == null
                ? 'Farmer registered successfully!'
                : 'Farmer details updated successfully!',
          ),
          backgroundColor: AppTheme.primaryColor,
        ),
      );
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.farmer != null;
    final farmerProvider = Provider.of<FarmerProvider>(context);
    final villageProvider = Provider.of<VillageProvider>(context);
    final villages = villageProvider.allVillages;

    return Scaffold(
      appBar: AppBar(
        title: Text(isEditing ? 'Edit Farmer Details' : 'Register New Farmer'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (farmerProvider.errorMessage != null) ...[
                  ErrorBanner(
                    message: farmerProvider.errorMessage!,
                    onDismiss: () => farmerProvider.clearMessages(),
                  ),
                  const SizedBox(height: 16),
                ],

                // Village Selection Dropdown
                DropdownButtonFormField<int>(
                  initialValue: _selectedVillageId,
                  decoration: InputDecoration(
                    labelText: 'Select Village *',
                    prefixIcon: const Icon(Icons.location_city, size: 20),
                    errorText: farmerProvider.validationErrors?['village_id']?.first,
                  ),
                  items: villages
                      .map((v) => DropdownMenuItem<int>(
                            value: v.id,
                            child: Text('${v.name} (${v.code})'),
                          ))
                      .toList(),
                  onChanged: (val) => setState(() => _selectedVillageId = val),
                  validator: (val) {
                    if (val == null) return 'Please select a village';
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                CustomTextField(
                  controller: _farmerCodeController,
                  label: 'Farmer Code *',
                  hint: 'e.g., FRM-VIL-001-01',
                  prefixIcon: const Icon(Icons.qr_code, size: 20),
                  errorText: farmerProvider.validationErrors?['farmer_code']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) {
                      return 'Farmer code is required';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                CustomTextField(
                  controller: _nameController,
                  label: 'Farmer Full Name *',
                  hint: 'e.g., Gurdeep Singh',
                  prefixIcon: const Icon(Icons.person, size: 20),
                  errorText: farmerProvider.validationErrors?['name']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) {
                      return 'Farmer full name is required';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                CustomTextField(
                  controller: _fatherNameController,
                  label: "Father's Name",
                  hint: 'e.g., Harbans Singh',
                  prefixIcon: const Icon(Icons.person_outline, size: 20),
                  errorText: farmerProvider.validationErrors?['father_name']?.first,
                ),
                const SizedBox(height: 16),

                CustomTextField(
                  controller: _mobileController,
                  label: 'Primary Mobile Number *',
                  hint: '10-digit mobile number',
                  keyboardType: TextInputType.phone,
                  prefixIcon: const Icon(Icons.phone, size: 20),
                  errorText: farmerProvider.validationErrors?['mobile']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) {
                      return 'Mobile number is required';
                    }
                    if (val.trim().length < 10) {
                      return 'Enter at least 10 digits';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                CustomTextField(
                  controller: _alternateMobileController,
                  label: 'Alternate Mobile Number',
                  keyboardType: TextInputType.phone,
                  prefixIcon: const Icon(Icons.phone_android, size: 20),
                  errorText: farmerProvider.validationErrors?['alternate_mobile']?.first,
                ),
                const SizedBox(height: 16),

                CustomTextField(
                  controller: _addressController,
                  label: 'Residential Address',
                  maxLines: 2,
                  prefixIcon: const Icon(Icons.home_outlined, size: 20),
                  errorText: farmerProvider.validationErrors?['address']?.first,
                ),
                const SizedBox(height: 16),

                // Gender Selection
                DropdownButtonFormField<String>(
                  initialValue: _gender,
                  decoration: const InputDecoration(
                    labelText: 'Gender',
                    prefixIcon: Icon(Icons.people_outline, size: 20),
                  ),
                  items: const [
                    DropdownMenuItem(value: 'male', child: Text('Male')),
                    DropdownMenuItem(value: 'female', child: Text('Female')),
                    DropdownMenuItem(value: 'other', child: Text('Other')),
                  ],
                  onChanged: (val) {
                    if (val != null) setState(() => _gender = val);
                  },
                ),
                const SizedBox(height: 16),

                // Bank Account Information Card
                Card(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Bank Account Details (Optional)',
                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                        ),
                        const SizedBox(height: 12),
                        CustomTextField(
                          controller: _bankNameController,
                          label: 'Bank Name',
                          hint: 'e.g., State Bank of India',
                          prefixIcon: const Icon(Icons.account_balance, size: 20),
                          errorText: farmerProvider.validationErrors?['bank_name']?.first,
                        ),
                        const SizedBox(height: 12),
                        CustomTextField(
                          controller: _accountNumberController,
                          label: 'Account Number',
                          keyboardType: TextInputType.number,
                          prefixIcon: const Icon(Icons.numbers, size: 20),
                          errorText: farmerProvider.validationErrors?['account_number']?.first,
                        ),
                        const SizedBox(height: 12),
                        CustomTextField(
                          controller: _ifscCodeController,
                          label: 'IFSC Code',
                          hint: 'e.g., SBIN0000590',
                          prefixIcon: const Icon(Icons.code, size: 20),
                          errorText: farmerProvider.validationErrors?['ifsc_code']?.first,
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                Card(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: SwitchListTile(
                    activeThumbColor: AppTheme.primaryColor,
                    title: const Text(
                      'Farmer Active Status',
                      style: TextStyle(fontWeight: FontWeight.w600),
                    ),
                    subtitle: Text(
                      _status
                          ? 'Active (Farmer eligible for daily milk collection)'
                          : 'Inactive (Milk collection temporarily suspended)',
                      style: const TextStyle(fontSize: 12),
                    ),
                    value: _status,
                    onChanged: (val) => setState(() => _status = val),
                  ),
                ),
                const SizedBox(height: 28),

                CustomButton(
                  text: isEditing ? 'Update Farmer Details' : 'Register Farmer',
                  icon: isEditing ? Icons.save_outlined : Icons.person_add_alt_1,
                  isLoading: farmerProvider.isSaving,
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
