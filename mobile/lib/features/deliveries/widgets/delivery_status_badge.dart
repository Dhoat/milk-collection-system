import 'package:flutter/material.dart';

import '../../../core/theme/app_theme.dart';

class DeliveryStatusBadge extends StatelessWidget {
  final String status;

  const DeliveryStatusBadge({super.key, required this.status});

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return const Color(0xFFD97706); // Amber / Orange
      case 'assigned':
        return const Color(0xFF0284C7); // Sky Blue
      case 'out_for_delivery':
        return const Color(0xFF7C3AED); // Indigo / Purple
      case 'delivered':
        return const Color(0xFF059669); // Emerald / Green
      case 'failed':
        return const Color(0xFFDC2626); // Red
      case 'cancelled':
        return const Color(0xFF6B7280); // Neutral Gray
      default:
        return AppTheme.textSecondary;
    }
  }

  Color _getStatusBgColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return const Color(0xFFFEF3C7);
      case 'assigned':
        return const Color(0xFFE0F2FE);
      case 'out_for_delivery':
        return const Color(0xFFEDE9FE);
      case 'delivered':
        return const Color(0xFFD1FAE5);
      case 'failed':
        return const Color(0xFFFEE2E2);
      case 'cancelled':
        return const Color(0xFFF3F4F6);
      default:
        return const Color(0xFFF3F4F6);
    }
  }

  String _getDisplayName(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return 'Pending';
      case 'assigned':
        return 'Assigned';
      case 'out_for_delivery':
        return 'Out for Delivery';
      case 'delivered':
        return 'Delivered';
      case 'failed':
        return 'Failed';
      case 'cancelled':
        return 'Cancelled';
      default:
        return status;
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return Icons.hourglass_top_rounded;
      case 'assigned':
        return Icons.person_pin_circle_outlined;
      case 'out_for_delivery':
        return Icons.local_shipping_outlined;
      case 'delivered':
        return Icons.check_circle_outline;
      case 'failed':
        return Icons.error_outline;
      case 'cancelled':
        return Icons.cancel_outlined;
      default:
        return Icons.info_outline;
    }
  }

  @override
  Widget build(BuildContext context) {
    final color = _getStatusColor(status);
    final bgColor = _getStatusBgColor(status);
    final label = _getDisplayName(status);
    final icon = _getStatusIcon(status);

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withValues(alpha: 0.3)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: color),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
        ],
      ),
    );
  }
}
