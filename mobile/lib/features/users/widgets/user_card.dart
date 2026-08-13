import 'package:flutter/material.dart';

import '../../../core/theme/app_theme.dart';
import '../../auth/models/user_model.dart';
import 'user_role_badge.dart';
import 'user_status_badge.dart';

class UserCard extends StatelessWidget {
  final UserModel user;
  final VoidCallback onTap;
  final VoidCallback onToggleStatus;

  const UserCard({
    super.key,
    required this.user,
    required this.onTap,
    required this.onToggleStatus,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      margin: const EdgeInsets.only(bottom: 12),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Row(
            children: [
              CircleAvatar(
                radius: 24,
                backgroundColor: AppTheme.primaryColor.withValues(alpha: 0.12),
                child: Text(
                  user.name.isNotEmpty ? user.name[0].toUpperCase() : 'U',
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: AppTheme.primaryColor,
                  ),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      user.name,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: AppTheme.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      user.email,
                      style: const TextStyle(
                        fontSize: 13,
                        color: AppTheme.textSecondary,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Wrap(
                      spacing: 8,
                      runSpacing: 4,
                      children: [
                        UserRoleBadge(role: user.role),
                        UserStatusBadge(status: user.status),
                      ],
                    ),
                  ],
                ),
              ),
              IconButton(
                icon: Icon(
                  user.status ? Icons.block : Icons.check_circle_outline,
                  color: user.status ? Colors.red.shade600 : Colors.green.shade600,
                ),
                tooltip: user.status ? 'Deactivate User' : 'Activate User',
                onPressed: onToggleStatus,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
