import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_button.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../../../core/widgets/role_bottom_nav.dart';
import '../../auth/providers/auth_provider.dart';
import '../providers/profile_provider.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final _profileFormKey = GlobalKey<FormState>();
  final _passwordFormKey = GlobalKey<FormState>();

  final _nameController = TextEditingController();
  final _emailController = TextEditingController();

  final _currentPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();

  bool _obscureCurrent = true;
  bool _obscureNew = true;
  bool _obscureConfirm = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final profileProvider = Provider.of<ProfileProvider>(context, listen: false);
      profileProvider.fetchProfile().then((_) {
        if (profileProvider.user != null) {
          _nameController.text = profileProvider.user!.name;
          _emailController.text = profileProvider.user!.email;
        }
      });
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    _nameController.dispose();
    _emailController.dispose();
    _currentPasswordController.dispose();
    _newPasswordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  void _confirmLogout(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Logout?'),
        content: const Text('Are you sure you want to log out of Dairy Management?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.of(ctx).pop();
              final authProvider = Provider.of<AuthProvider>(context, listen: false);
              await authProvider.logout();
              if (context.mounted) {
                Navigator.of(context).pushNamedAndRemoveUntil(AppRoutes.login, (route) => false);
              }
            },
            child: const Text('Logout', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  void _handleSaveProfile() async {
    final profileProvider = Provider.of<ProfileProvider>(context, listen: false);
    final authProvider = Provider.of<AuthProvider>(context, listen: false);

    if (!_profileFormKey.currentState!.validate()) return;

    final success = await profileProvider.updateProfile(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
    );

    if (success && mounted && profileProvider.user != null) {
      authProvider.initialize(); // Refresh auth user state
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Profile information updated successfully!'),
          backgroundColor: AppTheme.primaryColor,
        ),
      );
    }
  }

  void _handleChangePassword() async {
    final profileProvider = Provider.of<ProfileProvider>(context, listen: false);

    if (!_passwordFormKey.currentState!.validate()) return;

    final success = await profileProvider.updatePassword(
      currentPassword: _currentPasswordController.text,
      newPassword: _newPasswordController.text,
      newPasswordConfirmation: _confirmPasswordController.text,
    );

    if (success && mounted) {
      _currentPasswordController.clear();
      _newPasswordController.clear();
      _confirmPasswordController.clear();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Password changed successfully!'),
          backgroundColor: AppTheme.primaryColor,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final profileProvider = Provider.of<ProfileProvider>(context);
    final user = profileProvider.user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('My Profile'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout, color: Colors.white),
            tooltip: 'Logout',
            onPressed: () => _confirmLogout(context),
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.white,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          tabs: const [
            Tab(text: 'General Info'),
            Tab(text: 'Security'),
          ],
        ),
      ),
      body: profileProvider.isLoading
          ? const LoadingIndicator(message: 'Loading profile...')
          : SafeArea(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Header User Card
                    Card(
                      elevation: 2,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(20.0),
                        child: Row(
                          children: [
                            CircleAvatar(
                              radius: 32,
                              backgroundColor: AppTheme.primaryColor.withValues(alpha: 0.1),
                              child: const Icon(
                                Icons.person,
                                size: 36,
                                color: AppTheme.primaryColor,
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    user?.name ?? 'User',
                                    style: const TextStyle(
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                      color: AppTheme.textPrimary,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    user?.email ?? '',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: AppTheme.textSecondary,
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 10,
                                      vertical: 4,
                                    ),
                                    decoration: BoxDecoration(
                                      color: AppTheme.primaryColor.withValues(alpha: 0.1),
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                    child: Text(
                                      user?.roleDisplayName ?? 'Staff',
                                      style: const TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.w600,
                                        color: AppTheme.primaryColor,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),

                    // Notification Banner
                    if (profileProvider.errorMessage != null)
                      ErrorBanner(
                        message: profileProvider.errorMessage!,
                        onDismiss: () => profileProvider.clearMessages(),
                      ),

                    SizedBox(
                      height: 420,
                      child: TabBarView(
                        controller: _tabController,
                        children: [
                          // Tab 1: Edit Profile
                          Card(
                            child: Padding(
                              padding: const EdgeInsets.all(20.0),
                              child: Form(
                                key: _profileFormKey,
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.stretch,
                                  children: [
                                    const Text(
                                      'Edit Profile Details',
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                        color: AppTheme.textPrimary,
                                      ),
                                    ),
                                    const SizedBox(height: 16),
                                    CustomTextField(
                                      controller: _nameController,
                                      label: 'Full Name',
                                      prefixIcon: const Icon(Icons.person_outline, size: 20),
                                      validator: (val) {
                                        if (val == null || val.trim().isEmpty) {
                                          return 'Full name is required';
                                        }
                                        return null;
                                      },
                                    ),
                                    const SizedBox(height: 16),
                                    CustomTextField(
                                      controller: _emailController,
                                      label: 'Email Address',
                                      keyboardType: TextInputType.emailAddress,
                                      prefixIcon: const Icon(Icons.email_outlined, size: 20),
                                      validator: (val) {
                                        if (val == null || val.trim().isEmpty) {
                                          return 'Email address is required';
                                        }
                                        final emailRegex = RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$');
                                        if (!emailRegex.hasMatch(val.trim())) {
                                          return 'Please enter a valid email address';
                                        }
                                        return null;
                                      },
                                    ),
                                    const Spacer(),
                                    CustomButton(
                                      text: 'Save Profile Changes',
                                      icon: Icons.check_circle_outline,
                                      isLoading: profileProvider.isSavingProfile,
                                      onPressed: _handleSaveProfile,
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),

                          // Tab 2: Security & Password
                          Card(
                            child: Padding(
                              padding: const EdgeInsets.all(20.0),
                              child: Form(
                                key: _passwordFormKey,
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.stretch,
                                  children: [
                                    const Text(
                                      'Update Password',
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                        color: AppTheme.textPrimary,
                                      ),
                                    ),
                                    const SizedBox(height: 14),
                                    CustomTextField(
                                      controller: _currentPasswordController,
                                      label: 'Current Password',
                                      obscureText: _obscureCurrent,
                                      prefixIcon: const Icon(Icons.lock_outline, size: 20),
                                      suffixIcon: IconButton(
                                        icon: Icon(_obscureCurrent ? Icons.visibility_outlined : Icons.visibility_off_outlined, size: 20),
                                        onPressed: () => setState(() => _obscureCurrent = !_obscureCurrent),
                                      ),
                                      validator: (val) {
                                        if (val == null || val.isEmpty) {
                                          return 'Current password is required';
                                        }
                                        return null;
                                      },
                                    ),
                                    const SizedBox(height: 12),
                                    CustomTextField(
                                      controller: _newPasswordController,
                                      label: 'New Password',
                                      obscureText: _obscureNew,
                                      prefixIcon: const Icon(Icons.lock_reset, size: 20),
                                      suffixIcon: IconButton(
                                        icon: Icon(_obscureNew ? Icons.visibility_outlined : Icons.visibility_off_outlined, size: 20),
                                        onPressed: () => setState(() => _obscureNew = !_obscureNew),
                                      ),
                                      validator: (val) {
                                        if (val == null || val.isEmpty) {
                                          return 'New password is required';
                                        }
                                        if (val.length < 8) {
                                          return 'Password must be at least 8 characters';
                                        }
                                        return null;
                                      },
                                    ),
                                    const SizedBox(height: 12),
                                    CustomTextField(
                                      controller: _confirmPasswordController,
                                      label: 'Confirm New Password',
                                      obscureText: _obscureConfirm,
                                      prefixIcon: const Icon(Icons.lock_clock_outlined, size: 20),
                                      suffixIcon: IconButton(
                                        icon: Icon(_obscureConfirm ? Icons.visibility_outlined : Icons.visibility_off_outlined, size: 20),
                                        onPressed: () => setState(() => _obscureConfirm = !_obscureConfirm),
                                      ),
                                      validator: (val) {
                                        if (val == null || val.isEmpty) {
                                          return 'Please confirm your new password';
                                        }
                                        if (val != _newPasswordController.text) {
                                          return 'Passwords do not match';
                                        }
                                        return null;
                                      },
                                    ),
                                    const SizedBox(height: 16),
                                    CustomButton(
                                      text: 'Update Password',
                                      icon: Icons.security,
                                      isLoading: profileProvider.isSavingPassword,
                                      onPressed: _handleChangePassword,
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),

                    // Prominent Logout Card/Button
                    ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.red.shade50,
                        foregroundColor: Colors.red.shade700,
                        elevation: 0,
                        side: BorderSide(color: Colors.red.shade200),
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      onPressed: () => _confirmLogout(context),
                      icon: const Icon(Icons.logout, size: 22),
                      label: const Text(
                        'Log Out of Application',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
      bottomNavigationBar: Consumer<AuthProvider>(
        builder: (context, auth, _) => RoleBottomNav(
          currentRoute: AppRoutes.profile,
          userRole: auth.user?.role,
        ),
      ),
    );
  }
}
