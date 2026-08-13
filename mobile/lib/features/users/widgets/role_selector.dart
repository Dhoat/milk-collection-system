import 'package:flutter/material.dart';

import '../models/user_management_model.dart';

class RoleSelector extends StatelessWidget {
  final String? selectedRole;
  final ValueChanged<String?> onChanged;
  final String? errorText;

  const RoleSelector({
    super.key,
    required this.selectedRole,
    required this.onChanged,
    this.errorText,
  });

  @override
  Widget build(BuildContext context) {
    return DropdownButtonFormField<String>(
      initialValue: selectedRole,
      decoration: InputDecoration(
        labelText: 'Role',
        prefixIcon: const Icon(Icons.badge_outlined),
        errorText: errorText,
      ),
      items: SystemRole.availableRoles.map((role) {
        return DropdownMenuItem<String>(
          value: role.key,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                role.displayName,
                style: const TextStyle(fontWeight: FontWeight.bold),
              ),
            ],
          ),
        );
      }).toList(),
      onChanged: onChanged,
    );
  }
}
