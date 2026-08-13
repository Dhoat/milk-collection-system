import 'package:flutter/material.dart';
import '../models/dashboard_model.dart';

class RecentActivityItem extends StatelessWidget {
  final RecentActivityItemModel item;

  const RecentActivityItem({
    super.key,
    required this.item,
  });

  IconData _getIcon(String iconType) {
    switch (iconType) {
      case 'collection':
        return Icons.water_drop_outlined;
      case 'farmer':
        return Icons.person_add_alt_1_outlined;
      case 'village':
        return Icons.location_city_outlined;
      default:
        return Icons.notifications_none_outlined;
    }
  }

  Color _getIconColor(String iconType) {
    switch (iconType) {
      case 'collection':
        return const Color(0xFF059669); // Emerald
      case 'farmer':
        return const Color(0xFF0284C7); // Sky blue
      case 'village':
        return const Color(0xFF7C3AED); // Purple
      default:
        return const Color(0xFF6B7280); // Gray
    }
  }

  @override
  Widget build(BuildContext context) {
    final iconColor = _getIconColor(item.icon);
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: iconColor.withValues(alpha: 0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(_getIcon(item.icon), color: iconColor, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.message,
                  style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFF111827),
                  ),
                ),
                if (item.detail.isNotEmpty) ...[
                  const SizedBox(height: 2),
                  Text(
                    item.detail,
                    style: const TextStyle(
                      fontSize: 12,
                      color: Color(0xFF6B7280),
                    ),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }
}
