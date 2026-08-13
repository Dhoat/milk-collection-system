import 'package:flutter/material.dart';

import '../../../core/theme/app_theme.dart';
import '../models/delivery_model.dart';

class DeliveryStatusTimeline extends StatelessWidget {
  final DeliveryModel delivery;

  const DeliveryStatusTimeline({super.key, required this.delivery});

  int _getCurrentStepIndex(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return 0;
      case 'assigned':
        return 1;
      case 'out_for_delivery':
        return 2;
      case 'delivered':
        return 3;
      case 'failed':
      case 'cancelled':
        return 3;
      default:
        return 0;
    }
  }

  @override
  Widget build(BuildContext context) {
    final currentStep = _getCurrentStepIndex(delivery.status);
    final isFailedOrCancelled = delivery.status == 'failed' || delivery.status == 'cancelled';

    final steps = [
      {
        'title': 'Dispatch Registered',
        'subtitle': delivery.createdAt != null ? 'Date: ${delivery.deliveryDate}' : 'Date: ${delivery.deliveryDate}',
        'icon': Icons.inventory_2_outlined,
      },
      {
        'title': 'Driver Assigned',
        'subtitle': delivery.assignedStaff != null
            ? delivery.assignedStaff!.name
            : (currentStep >= 1 ? 'Staff assigned' : 'Awaiting driver assignment'),
        'icon': Icons.badge_outlined,
      },
      {
        'title': 'Out for Delivery',
        'subtitle': delivery.dispatchedAt != null
            ? 'Dispatched: ${delivery.dispatchedAt}'
            : (currentStep >= 2 ? 'In transit to shop' : 'Vehicle preparing'),
        'icon': Icons.local_shipping_outlined,
      },
      {
        'title': isFailedOrCancelled
            ? (delivery.status == 'failed' ? 'Delivery Failed' : 'Cancelled')
            : 'Delivered to Shop',
        'subtitle': delivery.deliveredAt != null
            ? 'Delivered: ${delivery.deliveredAt}'
            : (delivery.status == 'delivered'
                ? 'Handed over successfully'
                : (isFailedOrCancelled ? 'Order non-deliverable' : 'Pending final dropoff')),
        'icon': isFailedOrCancelled
            ? (delivery.status == 'failed' ? Icons.error_outline : Icons.cancel_outlined)
            : Icons.check_circle_outline,
      },
    ];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: List.generate(steps.length, (index) {
        final isPassed = index <= currentStep && !isFailedOrCancelled;
        final isCurrent = index == currentStep;
        final isLast = index == steps.length - 1;

        Color dotColor = AppTheme.textSecondary.withValues(alpha: 0.3);
        if (isPassed || (isCurrent && !isFailedOrCancelled)) {
          dotColor = AppTheme.primaryColor;
        } else if (isCurrent && isFailedOrCancelled) {
          dotColor = delivery.status == 'failed' ? Colors.red : Colors.grey;
        }

        return Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Column(
              children: [
                CircleAvatar(
                  radius: 16,
                  backgroundColor: dotColor.withValues(alpha: 0.15),
                  child: Icon(
                    steps[index]['icon'] as IconData,
                    size: 16,
                    color: dotColor,
                  ),
                ),
                if (!isLast)
                  Container(
                    width: 2,
                    height: 32,
                    color: isPassed ? AppTheme.primaryColor : Colors.grey.shade300,
                  ),
              ],
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.only(top: 4.0, bottom: 16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      steps[index]['title'] as String,
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: isCurrent ? FontWeight.bold : FontWeight.w600,
                        color: isCurrent ? AppTheme.textPrimary : AppTheme.textSecondary,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      steps[index]['subtitle'] as String,
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppTheme.textSecondary,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        );
      }),
    );
  }
}
