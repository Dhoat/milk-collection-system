import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_button.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/widgets/error_banner.dart';
import '../models/village_model.dart';
import '../providers/village_provider.dart';

class AddEditVillageScreen extends StatefulWidget {
  final VillageModel? village;

  const AddEditVillageScreen({super.key, this.village});

  @override
  State<AddEditVillageScreen> createState() => _AddEditVillageScreenState();
}

class _AddEditVillageScreenState extends State<AddEditVillageScreen> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _nameController;
  late TextEditingController _codeController;
  late TextEditingController _addressController;
  bool _status = true;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.village?.name ?? '');
    _codeController = TextEditingController(text: widget.village?.code ?? '');
    _addressController = TextEditingController(text: widget.village?.address ?? '');
    _status = widget.village?.status ?? true;
  }

  @override
  void dispose() {
    _nameController.dispose();
    _codeController.dispose();
    _addressController.dispose();
    super.dispose();
  }

  void _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;

    final villageProvider = Provider.of<VillageProvider>(context, listen: false);
    final name = _nameController.text.trim();
    final code = _codeController.text.trim();
    final address = _addressController.text.trim();

    bool success = false;
    if (widget.village == null) {
      success = await villageProvider.createVillage(
        name: name,
        code: code,
        address: address.isNotEmpty ? address : null,
        status: _status,
      );
    } else {
      success = await villageProvider.updateVillage(
        id: widget.village!.id,
        name: name,
        code: code,
        address: address.isNotEmpty ? address : null,
        status: _status,
      );
    }

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.village == null
                ? 'Village "$name" created successfully!'
                : 'Village "$name" updated successfully!',
          ),
          backgroundColor: AppTheme.primaryColor,
        ),
      );
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.village != null;
    final villageProvider = Provider.of<VillageProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: Text(isEditing ? 'Edit Village' : 'Add New Village'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (villageProvider.errorMessage != null) ...[
                  ErrorBanner(
                    message: villageProvider.errorMessage!,
                    onDismiss: () => villageProvider.clearMessages(),
                  ),
                  const SizedBox(height: 16),
                ],

                CustomTextField(
                  controller: _nameController,
                  label: 'Village Name *',
                  hint: 'e.g., Binjoki Kalan',
                  prefixIcon: const Icon(Icons.location_city, size: 20),
                  errorText: villageProvider.validationErrors?['name']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) {
                      return 'Village name is required';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                CustomTextField(
                  controller: _codeController,
                  label: 'Village Code *',
                  hint: 'e.g., VIL-001',
                  prefixIcon: const Icon(Icons.qr_code, size: 20),
                  errorText: villageProvider.validationErrors?['code']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) {
                      return 'Village code is required';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                CustomTextField(
                  controller: _addressController,
                  label: 'Address / Location Details',
                  hint: 'e.g., Tehsil Ahmedgarh, District Malerkotla',
                  maxLines: 3,
                  prefixIcon: const Icon(Icons.map_outlined, size: 20),
                  errorText: villageProvider.validationErrors?['address']?.first,
                ),
                const SizedBox(height: 16),

                Card(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: SwitchListTile(
                    activeThumbColor: AppTheme.primaryColor,
                    title: const Text(
                      'Village Active Status',
                      style: TextStyle(fontWeight: FontWeight.w600),
                    ),
                    subtitle: Text(
                      _status
                          ? 'Active (Farmers can collect milk in this village)'
                          : 'Inactive (Village operations temporarily suspended)',
                      style: const TextStyle(fontSize: 12),
                    ),
                    value: _status,
                    onChanged: (val) => setState(() => _status = val),
                  ),
                ),
                const SizedBox(height: 28),

                CustomButton(
                  text: isEditing ? 'Update Village' : 'Create Village',
                  icon: isEditing ? Icons.save_outlined : Icons.add_circle_outline,
                  isLoading: villageProvider.isSaving,
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
