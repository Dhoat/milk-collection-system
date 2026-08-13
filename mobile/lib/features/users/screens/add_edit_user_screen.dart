import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../auth/models/user_model.dart';
import '../providers/user_provider.dart';
import '../widgets/role_selector.dart';

class AddEditUserScreen extends StatefulWidget {
  final UserModel? user;

  const AddEditUserScreen({super.key, this.user});

  @override
  State<AddEditUserScreen> createState() => _AddEditUserScreenState();
}

class _AddEditUserScreenState extends State<AddEditUserScreen> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _nameController;
  late TextEditingController _emailController;
  late TextEditingController _passwordController;
  late TextEditingController _passwordConfirmController;

  String _selectedRole = 'collection_staff';
  bool _status = true;
  bool _obscurePassword = true;
  bool _obscureConfirm = true;

  bool get isEdit => widget.user != null;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.user?.name ?? '');
    _emailController = TextEditingController(text: widget.user?.email ?? '');
    _passwordController = TextEditingController();
    _passwordConfirmController = TextEditingController();

    if (widget.user != null) {
      _selectedRole = widget.user!.role;
      _status = widget.user!.status;
    }

    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<UserProvider>(context, listen: false).clearFieldErrors();
    });
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _passwordConfirmController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<UserProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: Text(isEdit ? 'Edit User Account' : 'Add New User'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (provider.errorMessage != null)
                ErrorBanner(message: provider.errorMessage!),

              // Name Field
              TextFormField(
                controller: _nameController,
                decoration: InputDecoration(
                  labelText: 'Full Name *',
                  prefixIcon: const Icon(Icons.person_outline),
                  errorText: provider.fieldErrors['name']?.first,
                ),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'Please enter user full name';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),

              // Email Field
              TextFormField(
                controller: _emailController,
                keyboardType: TextInputType.emailAddress,
                decoration: InputDecoration(
                  labelText: 'Email Address *',
                  prefixIcon: const Icon(Icons.email_outlined),
                  errorText: provider.fieldErrors['email']?.first,
                ),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'Please enter email address';
                  }
                  if (!val.contains('@') || !val.contains('.')) {
                    return 'Please enter a valid email address';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),

              // Role Selector
              RoleSelector(
                selectedRole: _selectedRole,
                errorText: provider.fieldErrors['role']?.first,
                onChanged: (role) {
                  if (role != null) {
                    setState(() => _selectedRole = role);
                  }
                },
              ),
              const SizedBox(height: 16),

              // Account Status Switch Card
              Card(
                elevation: 1,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                child: SwitchListTile(
                  title: const Text(
                    'Active Account Status',
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                  subtitle: Text(
                    _status ? 'User can log in to system' : 'User account disabled from logging in',
                    style: const TextStyle(fontSize: 12),
                  ),
                  value: _status,
                  activeTrackColor: AppTheme.primaryColor,
                  onChanged: (val) => setState(() => _status = val),
                ),
              ),
              const SizedBox(height: 20),

              // Password Section Header
              Text(
                isEdit ? 'Change Password (Optional)' : 'Security Password *',
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: AppTheme.textPrimary,
                ),
              ),
              const SizedBox(height: 12),

              // Password Field
              TextFormField(
                controller: _passwordController,
                obscureText: _obscurePassword,
                decoration: InputDecoration(
                  labelText: isEdit ? 'New Password' : 'Password *',
                  prefixIcon: const Icon(Icons.lock_outline),
                  suffixIcon: IconButton(
                    icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility),
                    onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                  ),
                  errorText: provider.fieldErrors['password']?.first,
                ),
                validator: (val) {
                  if (!isEdit && (val == null || val.isEmpty)) {
                    return 'Please enter a password';
                  }
                  if (val != null && val.isNotEmpty && val.length < 8) {
                    return 'Password must be at least 8 characters';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),

              // Confirm Password Field
              TextFormField(
                controller: _passwordConfirmController,
                obscureText: _obscureConfirm,
                decoration: InputDecoration(
                  labelText: isEdit ? 'Confirm New Password' : 'Confirm Password *',
                  prefixIcon: const Icon(Icons.lock_reset_outlined),
                  suffixIcon: IconButton(
                    icon: Icon(_obscureConfirm ? Icons.visibility_off : Icons.visibility),
                    onPressed: () => setState(() => _obscureConfirm = !_obscureConfirm),
                  ),
                  errorText: provider.fieldErrors['password_confirmation']?.first,
                ),
                validator: (val) {
                  if (!isEdit && (val == null || val.isEmpty)) {
                    return 'Please confirm password';
                  }
                  if (_passwordController.text.isNotEmpty && val != _passwordController.text) {
                    return 'Passwords do not match';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 28),

              // Submit Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: provider.isSubmitting ? null : _submitForm,
                  icon: provider.isSubmitting
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                        )
                      : Icon(isEdit ? Icons.save : Icons.person_add),
                  label: Text(isEdit ? 'Save Changes' : 'Create User Account'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _submitForm() async {
    if (!_formKey.currentState!.validate()) return;

    final provider = Provider.of<UserProvider>(context, listen: false);

    final payload = <String, dynamic>{
      'name': _nameController.text.trim(),
      'email': _emailController.text.trim(),
      'role': _selectedRole,
      'status': _status,
    };

    if (_passwordController.text.isNotEmpty) {
      payload['password'] = _passwordController.text;
      payload['password_confirmation'] = _passwordConfirmController.text;
    }

    bool success;
    if (isEdit) {
      success = await provider.updateUser(widget.user!.id, payload);
    } else {
      success = await provider.createUser(payload);
    }

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(provider.successMessage ?? 'User saved successfully'),
          backgroundColor: const Color(0xFF059669),
        ),
      );
      Navigator.of(context).pop(true);
    }
  }
}
