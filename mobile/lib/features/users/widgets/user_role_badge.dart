import 'package:flutter/material.dart';

class UserRoleBadge extends StatelessWidget {
  final String role;

  const UserRoleBadge({super.key, required this.role});

  @override
  Widget build(BuildContext context) {
    Color bg;
    Color fg;
    String label;

    switch (role) {
      case 'super_admin':
        bg = const Color(0xFFF3E8FF); // Purple 100
        fg = const Color(0xFF7E22CE); // Purple 700
        label = 'Super Admin';
        break;
      case 'manager':
        bg = const Color(0xFFDBEAFE); // Blue 100
        fg = const Color(0xFF1D4ED8); // Blue 700
        label = 'Manager';
        break;
      case 'collection_staff':
        bg = const Color(0xFFFEF3C7); // Amber 100
        fg = const Color(0xFFB45309); // Amber 700
        label = 'Collection Staff';
        break;
      case 'center_staff':
        bg = const Color(0xFFCCFBF1); // Teal 100
        fg = const Color(0xFF0F766E); // Teal 700
        label = 'Center Staff';
        break;
      default:
        bg = Colors.grey.shade200;
        fg = Colors.grey.shade800;
        label = role;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: fg,
          fontSize: 12,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }
}
