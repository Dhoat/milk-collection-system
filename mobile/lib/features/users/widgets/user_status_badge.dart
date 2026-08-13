import 'package:flutter/material.dart';

class UserStatusBadge extends StatelessWidget {
  final bool status;

  const UserStatusBadge({super.key, required this.status});

  @override
  Widget build(BuildContext context) {
    final bg = status ? const Color(0xFFD1FAE5) : const Color(0xFFFEE2E2);
    final fg = status ? const Color(0xFF047857) : const Color(0xFFB91C1C);
    final label = status ? 'Active' : 'Inactive';

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 6,
            height: 6,
            decoration: BoxDecoration(
              color: fg,
              shape: BoxShape.circle,
            ),
          ),
          const SizedBox(width: 6),
          Text(
            label,
            style: TextStyle(
              color: fg,
              fontSize: 12,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }
}
