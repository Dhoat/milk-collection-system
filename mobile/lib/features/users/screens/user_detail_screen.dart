import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../auth/models/user_model.dart';
import '../../auth/providers/auth_provider.dart';
import '../providers/user_provider.dart';
import '../widgets/user_role_badge.dart';
import '../widgets/user_status_badge.dart';

class UserDetailScreen extends StatefulWidget {
  final UserModel user;

  const UserDetailScreen({super.key, required this.user});

  @override
  State<UserDetailScreen> createState() => _UserDetailScreenState();
}

class _UserDetailScreenState extends State<UserDetailScreen> {
  late UserModel _currentUser;

  @override
  void initState() {
    super.initState();
    _currentUser = widget.user;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<UserProvider>(context, listen: false);
      provider.fetchUserDetail(_currentUser.id);
    });
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<UserProvider>(context);
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final isSelf = authProvider.user?.id == _currentUser.id;

    if (provider.selectedUser != null && provider.selectedUser!.id == _currentUser.id) {
      _currentUser = provider.selectedUser!;
    }

    return Scaffold(
      appBar: AppBar(
        title: Text(_currentUser.name),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit_outlined),
            onPressed: () async {
              final result = await Navigator.of(context).pushNamed(
                AppRoutes.userEdit,
                arguments: _currentUser,
              );
              if (result == true) {
                provider.fetchUserDetail(_currentUser.id);
              }
            },
            tooltip: 'Edit User',
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline, color: Colors.red),
            onPressed: isSelf ? null : () => _confirmDelete(context, provider),
            tooltip: isSelf ? 'Cannot delete logged-in account' : 'Delete User',
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (provider.errorMessage != null)
              ErrorBanner(message: provider.errorMessage!),

            if (isSelf)
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(12),
                margin: const EdgeInsets.only(bottom: 16),
                decoration: BoxDecoration(
                  color: Colors.amber.shade50,
                  border: Border.all(color: Colors.amber.shade300),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  children: [
                    Icon(Icons.info_outline, color: Colors.amber.shade900),
                    const SizedBox(width: 10),
                    const Expanded(
                      child: Text(
                        'This is your logged-in account. Backend rules prohibit self-deactivation and self-deletion.',
                        style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                      ),
                    ),
                  ],
                ),
              ),

            // Profile Card Header
            Card(
              elevation: 2,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  children: [
                    CircleAvatar(
                      radius: 36,
                      backgroundColor: AppTheme.primaryColor.withValues(alpha: 0.12),
                      child: Text(
                        _currentUser.name.isNotEmpty ? _currentUser.name[0].toUpperCase() : 'U',
                        style: const TextStyle(
                          fontSize: 32,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.primaryColor,
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    Text(
                      _currentUser.name,
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                        color: AppTheme.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      _currentUser.email,
                      style: const TextStyle(
                        fontSize: 14,
                        color: AppTheme.textSecondary,
                      ),
                    ),
                    const SizedBox(height: 14),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        UserRoleBadge(role: _currentUser.role),
                        const SizedBox(width: 10),
                        UserStatusBadge(status: _currentUser.status),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 20),

            // Account Details Card
            Card(
              elevation: 2,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Account Details',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: AppTheme.textPrimary,
                      ),
                    ),
                    const Divider(height: 20),
                    _buildDetailRow('User ID', '#${_currentUser.id}'),
                    _buildDetailRow('Full Name', _currentUser.name),
                    _buildDetailRow('Email Address', _currentUser.email),
                    _buildDetailRow('Role', _currentUser.roleDisplayName),
                    _buildDetailRow('Status', _currentUser.status ? 'Active' : 'Inactive'),
                    if (_currentUser.createdAt != null)
                      _buildDetailRow('Created At', _currentUser.createdAt!.toLocal().toString().split('.')[0]),
                    if (_currentUser.updatedAt != null)
                      _buildDetailRow('Last Updated', _currentUser.updatedAt!.toLocal().toString().split('.')[0]),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),

            // Action Buttons
            Row(
              children: [
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () async {
                      final result = await Navigator.of(context).pushNamed(
                        AppRoutes.userEdit,
                        arguments: _currentUser,
                      );
                      if (result == true) {
                        provider.fetchUserDetail(_currentUser.id);
                      }
                    },
                    icon: const Icon(Icons.edit),
                    label: const Text('Edit Account'),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: isSelf ? null : () => _confirmToggleStatus(context, provider),
                    icon: Icon(_currentUser.status ? Icons.block : Icons.check_circle),
                    label: Text(_currentUser.status ? 'Deactivate' : 'Activate'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: _currentUser.status ? Colors.red : AppTheme.primaryColor,
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(fontSize: 13, color: AppTheme.textSecondary),
          ),
          Text(
            value,
            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppTheme.textPrimary),
          ),
        ],
      ),
    );
  }

  void _confirmToggleStatus(BuildContext context, UserProvider provider) async {
    final actionText = _currentUser.status ? 'deactivate' : 'activate';
    final scaffoldMessenger = ScaffoldMessenger.of(context);

    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('${actionText.toUpperCase()} Account'),
        content: Text('Are you sure you want to $actionText "${_currentUser.name}"?'),
        actions: [
          TextButton(onPressed: () => Navigator.of(ctx).pop(false), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () => Navigator.of(ctx).pop(true),
            style: ElevatedButton.styleFrom(
              backgroundColor: _currentUser.status ? Colors.red : AppTheme.primaryColor,
            ),
            child: Text(actionText.toUpperCase()),
          ),
        ],
      ),
    );

    if (confirmed == true && mounted) {
      final success = await provider.toggleUserStatus(_currentUser.id);
      if (mounted) {
        final msg = success ? provider.successMessage : provider.errorMessage;
        scaffoldMessenger.showSnackBar(
          SnackBar(
            content: Text(msg ?? 'Status update complete'),
            backgroundColor: success ? const Color(0xFF059669) : const Color(0xFFDC2626),
          ),
        );
      }
    }
  }

  void _confirmDelete(BuildContext context, UserProvider provider) async {
    final scaffoldMessenger = ScaffoldMessenger.of(context);
    final navigator = Navigator.of(context);

    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('DELETE User Account'),
        content: Text('Are you sure you want to PERMANENTLY delete "${_currentUser.name}"? This action cannot be undone.'),
        actions: [
          TextButton(onPressed: () => Navigator.of(ctx).pop(false), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () => Navigator.of(ctx).pop(true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Delete Account'),
          ),
        ],
      ),
    );

    if (confirmed == true && mounted) {
      final success = await provider.deleteUser(_currentUser.id);
      if (mounted) {
        final msg = success ? provider.successMessage : provider.errorMessage;
        scaffoldMessenger.showSnackBar(
          SnackBar(
            content: Text(msg ?? 'Deletion complete'),
            backgroundColor: success ? const Color(0xFF059669) : const Color(0xFFDC2626),
          ),
        );
        if (success) {
          navigator.pop(true);
        }
      }
    }
  }
}
