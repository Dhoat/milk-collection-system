import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../auth/providers/auth_provider.dart';
import '../models/delivery_model.dart';
import '../providers/delivery_provider.dart';
import '../widgets/delivery_status_badge.dart';
import '../widgets/delivery_status_timeline.dart';

class DeliveryDetailScreen extends StatelessWidget {
  final DeliveryModel delivery;

  const DeliveryDetailScreen({super.key, required this.delivery});

  void _confirmDelete(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Delivery Record?'),
        content: Text('Are you sure you want to delete ${delivery.deliveryNumber}?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.of(ctx).pop();
              final provider = Provider.of<DeliveryProvider>(context, listen: false);
              final success = await provider.deleteDelivery(delivery.id);
              if (success && context.mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Delivery record deleted.'),
                    backgroundColor: Colors.red,
                  ),
                );
                Navigator.of(context).pop();
              }
            },
            child: const Text('Delete', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final deliveryProvider = Provider.of<DeliveryProvider>(context);
    final userRole = authProvider.user?.role ?? '';

    final canUpdate = userRole == 'super_admin' || userRole == 'manager' || userRole == 'center_staff';
    final canDelete = userRole == 'super_admin' || userRole == 'manager';

    // Locate updated delivery model if status changed
    final currentDelivery = deliveryProvider.allDeliveries.firstWhere(
      (d) => d.id == delivery.id,
      orElse: () => delivery,
    );

    final shopName = currentDelivery.shop?.name ?? 'Shop #${currentDelivery.shopId}';
    final orderNumber = currentDelivery.shopOrder?.orderNumber ?? 'Order #${currentDelivery.shopOrderId}';
    final driverName = currentDelivery.assignedStaff?.name ?? 'Unassigned Driver';

    return Scaffold(
      appBar: AppBar(
        title: Text(currentDelivery.deliveryNumber),
        actions: [
          if (canDelete)
            IconButton(
              icon: const Icon(Icons.delete_outline, color: Colors.red),
              tooltip: 'Delete Delivery',
              onPressed: () => _confirmDelete(context),
            ),
        ],
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (deliveryProvider.errorMessage != null) ...[
                ErrorBanner(
                  message: deliveryProvider.errorMessage!,
                  onDismiss: () => deliveryProvider.clearMessages(),
                ),
                const SizedBox(height: 16),
              ],

              // Delivery Header Card
              Card(
                elevation: 3,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                child: Padding(
                  padding: const EdgeInsets.all(20.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                currentDelivery.deliveryNumber,
                                style: const TextStyle(
                                  fontSize: 20,
                                  fontWeight: FontWeight.bold,
                                  color: AppTheme.textPrimary,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                'Dispatch Date: ${currentDelivery.deliveryDate}',
                                style: const TextStyle(
                                  fontSize: 13,
                                  color: AppTheme.textSecondary,
                                ),
                              ),
                            ],
                          ),
                          DeliveryStatusBadge(status: currentDelivery.status),
                        ],
                      ),
                      const Divider(height: 24),

                      // Destination Shop Info
                      ListTile(
                        contentPadding: EdgeInsets.zero,
                        leading: CircleAvatar(
                          backgroundColor: AppTheme.primaryColor.withValues(alpha: 0.1),
                          child: const Icon(Icons.storefront, color: AppTheme.primaryColor),
                        ),
                        title: Text(
                          shopName,
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                        subtitle: Text(
                          'Address: ${currentDelivery.deliveryAddress ?? "Main Shop Premises"}\nPhone: ${currentDelivery.contactPhone ?? "N/A"} (${currentDelivery.contactPerson ?? "Manager"})',
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Driver & Staff Assignment Card
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Row(
                    children: [
                      CircleAvatar(
                        backgroundColor: currentDelivery.assignedStaff != null
                            ? const Color(0xFFE0F2FE)
                            : const Color(0xFFFEF3C7),
                        child: Icon(
                          Icons.badge_outlined,
                          color: currentDelivery.assignedStaff != null
                              ? const Color(0xFF0284C7)
                              : const Color(0xFFD97706),
                        ),
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Assigned Delivery Staff',
                              style: TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                            ),
                            Text(
                              driverName,
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            if (currentDelivery.assignedStaff?.email != null)
                              Text(
                                'Email: ${currentDelivery.assignedStaff!.email}',
                                style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                              ),
                          ],
                        ),
                      ),
                      if (canUpdate && !currentDelivery.isTerminalState)
                        IconButton(
                          icon: const Icon(Icons.edit_outlined, color: AppTheme.primaryColor),
                          tooltip: 'Update Driver / Status',
                          onPressed: () {
                            Navigator.of(context).pushNamed(
                              AppRoutes.updateDeliveryStatus,
                              arguments: currentDelivery,
                            );
                          },
                        ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Linked Shop Order Details Card
              if (currentDelivery.shopOrder != null) ...[
                Card(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  child: Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'Linked Shop Order ($orderNumber)',
                              style: const TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            Text(
                              '₹${currentDelivery.shopOrder!.totalAmount.toStringAsFixed(2)}',
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.primaryColor,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 10),
                        if (currentDelivery.shopOrder!.items.isNotEmpty) ...[
                          ListView.separated(
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            itemCount: currentDelivery.shopOrder!.items.length,
                            separatorBuilder: (ctx, i) => const Divider(height: 12),
                            itemBuilder: (context, index) {
                              final item = currentDelivery.shopOrder!.items[index];
                              return Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    '${item.productName} x ${item.quantity} ${item.unit}',
                                    style: const TextStyle(fontSize: 13, color: AppTheme.textPrimary),
                                  ),
                                  Text(
                                    '₹${item.lineTotal.toStringAsFixed(2)}',
                                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                                  ),
                                ],
                              );
                            },
                          ),
                        ],
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 20),
              ],

              // Delivery Timeline Card
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Dispatch Progression Timeline',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 16),
                      DeliveryStatusTimeline(delivery: currentDelivery),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Action Buttons
              if (canUpdate && !currentDelivery.isTerminalState) ...[
                SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton.icon(
                    onPressed: () {
                      Navigator.of(context).pushNamed(
                        AppRoutes.updateDeliveryStatus,
                        arguments: currentDelivery,
                      );
                    },
                    icon: const Icon(Icons.sync),
                    label: const Text('Update Delivery Status'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.primaryColor,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
