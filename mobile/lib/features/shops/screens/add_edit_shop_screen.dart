import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_button.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/widgets/error_banner.dart';
import '../../villages/providers/village_provider.dart';
import '../models/shop_model.dart';
import '../providers/shop_provider.dart';

class AddEditShopScreen extends StatefulWidget {
  final ShopModel? shop;

  const AddEditShopScreen({super.key, this.shop});

  @override
  State<AddEditShopScreen> createState() => _AddEditShopScreenState();
}

class _AddEditShopScreenState extends State<AddEditShopScreen> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _codeController;
  late TextEditingController _nameController;
  late TextEditingController _ownerNameController;
  late TextEditingController _phoneController;
  late TextEditingController _emailController;
  int? _selectedVillageId;
  late TextEditingController _areaController;
  late TextEditingController _addressController;
  late TextEditingController _creditLimitController;
  late TextEditingController _notesController;
  bool _status = true;

  @override
  void initState() {
    super.initState();
    final s = widget.shop;

    _codeController = TextEditingController(text: s?.shopCode ?? '');
    _nameController = TextEditingController(text: s?.name ?? '');
    _ownerNameController = TextEditingController(text: s?.ownerName ?? '');
    _phoneController = TextEditingController(text: s?.phone ?? '');
    _emailController = TextEditingController(text: s?.email ?? '');
    _selectedVillageId = s?.villageId;
    _areaController = TextEditingController(text: s?.area ?? '');
    _addressController = TextEditingController(text: s?.address ?? '');
    _creditLimitController = TextEditingController(
      text: s != null && s.creditLimit > 0 ? s.creditLimit.toString() : '',
    );
    _notesController = TextEditingController(text: s?.notes ?? '');
    _status = s?.status ?? true;

    if (s == null) {
      // Auto-generate code prefix if creating new shop
      _codeController.text = 'SHP-${DateTime.now().millisecondsSinceEpoch.toString().substring(7)}';
    }

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final villageProvider = Provider.of<VillageProvider>(context, listen: false);
      if (villageProvider.allVillages.isEmpty) {
        villageProvider.fetchVillages();
      }
    });
  }

  @override
  void dispose() {
    _codeController.dispose();
    _nameController.dispose();
    _ownerNameController.dispose();
    _phoneController.dispose();
    _emailController.dispose();
    _areaController.dispose();
    _addressController.dispose();
    _creditLimitController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  void _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;

    final shopProvider = Provider.of<ShopProvider>(context, listen: false);

    final creditLimit = double.tryParse(_creditLimitController.text.trim()) ?? 0.0;

    final payload = <String, dynamic>{
      'shop_code': _codeController.text.trim(),
      'name': _nameController.text.trim(),
      'owner_name': _ownerNameController.text.trim(),
      'phone': _phoneController.text.trim(),
      'email': _emailController.text.trim().isNotEmpty ? _emailController.text.trim() : null,
      'village_id': _selectedVillageId,
      'area': _areaController.text.trim().isNotEmpty ? _areaController.text.trim() : null,
      'address': _addressController.text.trim().isNotEmpty ? _addressController.text.trim() : null,
      'status': _status,
      'credit_limit': creditLimit,
      'notes': _notesController.text.trim().isNotEmpty ? _notesController.text.trim() : null,
    };

    bool success = false;
    if (widget.shop == null) {
      success = await shopProvider.createShop(payload);
    } else {
      success = await shopProvider.updateShop(widget.shop!.id, payload);
    }

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.shop == null ? 'Shop created successfully!' : 'Shop updated successfully!',
          ),
          backgroundColor: AppTheme.primaryColor,
        ),
      );
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.shop != null;
    final shopProvider = Provider.of<ShopProvider>(context);
    final villageProvider = Provider.of<VillageProvider>(context);
    final villages = villageProvider.allVillages;

    return Scaffold(
      appBar: AppBar(
        title: Text(isEditing ? 'Edit Shop' : 'Add New Shop'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (shopProvider.errorMessage != null) ...[
                  ErrorBanner(
                    message: shopProvider.errorMessage!,
                    onDismiss: () => shopProvider.clearMessages(),
                  ),
                  const SizedBox(height: 16),
                ],

                // Shop Code Field
                CustomTextField(
                  controller: _codeController,
                  label: 'Shop Code *',
                  hint: 'e.g. SHP-001',
                  prefixIcon: const Icon(Icons.qr_code, size: 20),
                  errorText: shopProvider.validationErrors?['shop_code']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) return 'Shop code is required';
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                // Shop Name Field
                CustomTextField(
                  controller: _nameController,
                  label: 'Shop Name *',
                  hint: 'e.g. Apex Dairy Store',
                  prefixIcon: const Icon(Icons.storefront, size: 20),
                  errorText: shopProvider.validationErrors?['name']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) return 'Shop name is required';
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                // Owner Name Field
                CustomTextField(
                  controller: _ownerNameController,
                  label: 'Owner Name *',
                  hint: 'e.g. Rajesh Kumar',
                  prefixIcon: const Icon(Icons.person_outline, size: 20),
                  errorText: shopProvider.validationErrors?['owner_name']?.first,
                  validator: (val) {
                    if (val == null || val.trim().isEmpty) return 'Owner name is required';
                    return null;
                  },
                ),
                const SizedBox(height: 16),

                // Phone & Email Row
                Row(
                  children: [
                    Expanded(
                      child: CustomTextField(
                        controller: _phoneController,
                        label: 'Phone Number *',
                        hint: '9876543210',
                        keyboardType: TextInputType.phone,
                        prefixIcon: const Icon(Icons.phone_outlined, size: 18),
                        errorText: shopProvider.validationErrors?['phone']?.first,
                        validator: (val) {
                          if (val == null || val.trim().isEmpty) return 'Phone is required';
                          return null;
                        },
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: CustomTextField(
                        controller: _emailController,
                        label: 'Email (Optional)',
                        hint: 'shop@email.com',
                        keyboardType: TextInputType.emailAddress,
                        prefixIcon: const Icon(Icons.email_outlined, size: 18),
                        errorText: shopProvider.validationErrors?['email']?.first,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Village Dropdown Field
                DropdownButtonFormField<int>(
                  initialValue: _selectedVillageId,
                  decoration: InputDecoration(
                    labelText: 'Village (Optional)',
                    prefixIcon: const Icon(Icons.location_city, size: 20),
                    errorText: shopProvider.validationErrors?['village_id']?.first,
                  ),
                  items: villages
                      .map((v) => DropdownMenuItem<int>(
                            value: v.id,
                            child: Text('${v.name} (${v.code})'),
                          ))
                      .toList(),
                  onChanged: (val) => setState(() => _selectedVillageId = val),
                ),
                const SizedBox(height: 16),

                // Area & Credit Limit Row
                Row(
                  children: [
                    Expanded(
                      child: CustomTextField(
                        controller: _areaController,
                        label: 'Area / Sector',
                        hint: 'e.g. Sector 14',
                        prefixIcon: const Icon(Icons.map_outlined, size: 18),
                        errorText: shopProvider.validationErrors?['area']?.first,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: CustomTextField(
                        controller: _creditLimitController,
                        label: 'Credit Limit (₹)',
                        hint: 'e.g. 10000',
                        keyboardType: const TextInputType.numberWithOptions(decimal: true),
                        prefixIcon: const Icon(Icons.credit_card, size: 18),
                        errorText: shopProvider.validationErrors?['credit_limit']?.first,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Address Field
                CustomTextField(
                  controller: _addressController,
                  label: 'Full Address',
                  hint: 'Street, landmark and pincode',
                  maxLines: 2,
                  prefixIcon: const Icon(Icons.home_outlined, size: 20),
                  errorText: shopProvider.validationErrors?['address']?.first,
                ),
                const SizedBox(height: 16),

                // Active Status Switch
                SwitchListTile(
                  title: const Text('Active Shop Status'),
                  subtitle: const Text('Allow this shop to place orders and receive deliveries'),
                  value: _status,
                  activeThumbColor: AppTheme.primaryColor,
                  onChanged: (val) => setState(() => _status = val),
                ),
                const SizedBox(height: 16),

                // Notes Field
                CustomTextField(
                  controller: _notesController,
                  label: 'Remarks / Notes',
                  hint: 'Additional details about shop operations',
                  maxLines: 2,
                  prefixIcon: const Icon(Icons.note_alt_outlined, size: 20),
                  errorText: shopProvider.validationErrors?['notes']?.first,
                ),
                const SizedBox(height: 28),

                CustomButton(
                  text: isEditing ? 'Update Shop Details' : 'Register Shop',
                  icon: isEditing ? Icons.save_outlined : Icons.check_circle_outline,
                  isLoading: shopProvider.isSaving,
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
