import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../../auth/models/user_model.dart';
import '../providers/user_provider.dart';
import '../widgets/user_card.dart';

class UserListScreen extends StatefulWidget {
  const UserListScreen({super.key});

  @override
  State<UserListScreen> createState() => _UserListScreenState();
}

class _UserListScreenState extends State<UserListScreen> {
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<UserProvider>(context, listen: false);
      provider.fetchUsers(refresh: true);
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<UserProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('User Management'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => provider.fetchUsers(refresh: true),
            tooltip: 'Refresh',
          ),
        ],
      ),
      floatingActionButton: provider.status != UserProviderStateStatus.forbidden
          ? FloatingActionButton.extended(
              onPressed: () async {
                final result = await Navigator.of(context).pushNamed(AppRoutes.userCreate);
                if (result == true) {
                  provider.fetchUsers(refresh: true);
                }
              },
              icon: const Icon(Icons.person_add),
              label: const Text('Add User'),
            )
          : null,
      body: Column(
        children: [
          // Filter Bar
          Container(
            padding: const EdgeInsets.all(16.0),
            color: AppTheme.surfaceColor,
            child: Column(
              children: [
                TextField(
                  controller: _searchController,
                  decoration: InputDecoration(
                    hintText: 'Search by name or email...',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon: _searchController.text.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.clear),
                            onPressed: () {
                              _searchController.clear();
                              provider.setSearchQuery('');
                            },
                          )
                        : null,
                  ),
                  onSubmitted: (value) => provider.setSearchQuery(value),
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: DropdownButtonFormField<String?>(
                        isExpanded: true,
                        initialValue: provider.selectedRoleFilter,
                        decoration: const InputDecoration(
                          labelText: 'Role',
                          contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                        ),
                        items: const [
                          DropdownMenuItem(value: null, child: Text('All Roles', overflow: TextOverflow.ellipsis)),
                          DropdownMenuItem(value: 'super_admin', child: Text('Super Admin', overflow: TextOverflow.ellipsis)),
                          DropdownMenuItem(value: 'manager', child: Text('Manager', overflow: TextOverflow.ellipsis)),
                          DropdownMenuItem(value: 'collection_staff', child: Text('Collection Staff', overflow: TextOverflow.ellipsis)),
                          DropdownMenuItem(value: 'center_staff', child: Text('Center Staff', overflow: TextOverflow.ellipsis)),
                        ],
                        onChanged: (role) => provider.setRoleFilter(role),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: DropdownButtonFormField<String?>(
                        isExpanded: true,
                        initialValue: provider.selectedStatusFilter,
                        decoration: const InputDecoration(
                          labelText: 'Status',
                          contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                        ),
                        items: const [
                          DropdownMenuItem(value: null, child: Text('All Status', overflow: TextOverflow.ellipsis)),
                          DropdownMenuItem(value: '1', child: Text('Active', overflow: TextOverflow.ellipsis)),
                          DropdownMenuItem(value: '0', child: Text('Inactive', overflow: TextOverflow.ellipsis)),
                        ],
                        onChanged: (status) => provider.setStatusFilter(status),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const Divider(height: 1),

          // User List Body
          Expanded(
            child: _buildUserListBody(provider),
          ),
        ],
      ),
    );
  }

  Widget _buildUserListBody(UserProvider provider) {
    if (provider.status == UserProviderStateStatus.loading && provider.users.isEmpty) {
      return const LoadingIndicator(message: 'Loading user accounts...');
    }

    if (provider.status == UserProviderStateStatus.forbidden) {
      return Padding(
        padding: const EdgeInsets.all(24.0),
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.amber.shade50,
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.security, size: 64, color: Colors.amber.shade800),
              ),
              const SizedBox(height: 20),
              const Text(
                'Access Restricted',
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                  color: AppTheme.textPrimary,
                ),
              ),
              const SizedBox(height: 10),
              Text(
                provider.errorMessage ?? 'User management is restricted to Super Admins only.',
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 14, color: AppTheme.textSecondary),
              ),
            ],
          ),
        ),
      );
    }

    if (provider.status == UserProviderStateStatus.error && provider.users.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              ErrorBanner(message: provider.errorMessage ?? 'Failed to load user accounts'),
              const SizedBox(height: 16),
              ElevatedButton.icon(
                onPressed: () => provider.fetchUsers(refresh: true),
                icon: const Icon(Icons.refresh),
                label: const Text('Retry'),
              ),
            ],
          ),
        ),
      );
    }

    if (provider.users.isEmpty) {
      return RefreshIndicator(
        onRefresh: () => provider.fetchUsers(refresh: true),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: SizedBox(
            height: MediaQuery.of(context).size.height * 0.5,
            child: const Center(
              child: Text(
                'No user accounts found.',
                style: TextStyle(fontSize: 16, color: AppTheme.textSecondary),
              ),
            ),
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: () => provider.fetchUsers(refresh: true),
      child: Column(
        children: [
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.all(16.0),
              itemCount: provider.users.length,
              itemBuilder: (context, index) {
                final user = provider.users[index];
                return UserCard(
                  user: user,
                  onTap: () async {
                    final result = await Navigator.of(context).pushNamed(
                      AppRoutes.userDetail,
                      arguments: user,
                    );
                    if (result == true) {
                      provider.fetchUsers(refresh: true);
                    }
                  },
                  onToggleStatus: () => _confirmToggleStatus(user),
                );
              },
            ),
          ),

          // Pagination Controls
          if (provider.lastPage > 1)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              color: AppTheme.surfaceColor,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Page ${provider.currentPage} of ${provider.lastPage} (${provider.totalUsers} Users)',
                    style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                  ),
                  Row(
                    children: [
                      IconButton(
                        icon: const Icon(Icons.chevron_left),
                        onPressed: provider.currentPage > 1
                            ? () => provider.fetchUsers(page: provider.currentPage - 1)
                            : null,
                      ),
                      IconButton(
                        icon: const Icon(Icons.chevron_right),
                        onPressed: provider.currentPage < provider.lastPage
                            ? () => provider.fetchUsers(page: provider.currentPage + 1)
                            : null,
                      ),
                    ],
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }

  void _confirmToggleStatus(UserModel user) async {
    final actionText = user.status ? 'deactivate' : 'activate';
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('${actionText.toUpperCase()} User Account'),
        content: Text('Are you sure you want to $actionText "${user.name}"?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(false),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.of(ctx).pop(true),
            style: ElevatedButton.styleFrom(
              backgroundColor: user.status ? Colors.red : AppTheme.primaryColor,
            ),
            child: Text(user.status ? 'Deactivate' : 'Activate'),
          ),
        ],
      ),
    );

    if (confirmed == true && mounted) {
      final provider = Provider.of<UserProvider>(context, listen: false);
      final success = await provider.toggleUserStatus(user.id);
      if (mounted) {
        final message = success ? provider.successMessage : provider.errorMessage;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(message ?? 'Status update complete'),
            backgroundColor: success ? const Color(0xFF059669) : const Color(0xFFDC2626),
          ),
        );
      }
    }
  }
}
