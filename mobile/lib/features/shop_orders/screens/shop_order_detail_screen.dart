import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../models/shop_order_model.dart';
import '../providers/shop_order_provider.dart';

class ShopOrderDetailScreen extends StatelessWidget {
  final ShopOrderModel order;

  const ShopOrderDetailScreen({super.key, required this.order});

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return const Color(0xFFD97706);
      case 'confirmed':
        return const Color(0xFF0284C7);
      case 'preparing':
        return const Color(0xFF7C3AED);
      case 'dispatched':
        return const Color(0xFF2563EB);
      case 'delivered':
        return const Color(0xFF059669);
      case 'cancelled':
        return const Color(0xFFDC2626);
      default:
        return AppTheme.textSecondary;
    }
  }

  Color _getStatusBgColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return const Color(0xFFFEF3C7);
      case 'confirmed':
        return const Color(0xFFE0F2FE);
      case 'preparing':
        return const Color(0xFFEDE9FE);
      case 'dispatched':
        return const Color(0xFFDBEAFE);
      case 'delivered':
        return const Color(0xFFD1FAE5);
      case 'cancelled':
        return const Color(0xFFFEE2E2);
      default:
        return const Color(0xFFF3F4F6);
    }
  }

  void _confirmDelete(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Shop Order?'),
        content: Text('Are you sure you want to delete ${order.orderNumber}?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.of(ctx).pop();
              final provider = Provider.of<ShopOrderProvider>(context, listen: false);
              final success = await provider.deleteShopOrder(order.id);
              if (success && context.mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Shop order deleted.'),
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
    final orderProvider = Provider.of<ShopOrderProvider>(context);
    // Locate updated order if status changed
    final currentOrder = orderProvider.allOrders.firstWhere(
      (o) => o.id == order.id,
      orElse: () => order,
    );

    final shopName = currentOrder.shop?.name ?? 'Shop #${currentOrder.shopId}';
    final statusColor = _getStatusColor(currentOrder.status);
    final statusBgColor = _getStatusBgColor(currentOrder.status);

    return Scaffold(
      appBar: AppBar(
        title: Text(currentOrder.orderNumber),
        actions: [
          IconButton(
            icon: const Icon(Icons.delete_outline, color: Colors.red),
            tooltip: 'Delete Order',
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
              if (orderProvider.errorMessage != null) ...[
                ErrorBanner(
                  message: orderProvider.errorMessage!,
                  onDismiss: () => orderProvider.clearMessages(),
                ),
                const SizedBox(height: 16),
              ],

              // Order Summary Header Card
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
                                currentOrder.orderNumber,
                                style: const TextStyle(
                                  fontSize: 20,
                                  fontWeight: FontWeight.bold,
                                  color: AppTheme.textPrimary,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                'Date: ${currentOrder.orderDate}',
                                style: const TextStyle(
                                  fontSize: 13,
                                  color: AppTheme.textSecondary,
                                ),
                              ),
                            ],
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            decoration: BoxDecoration(
                              color: statusBgColor,
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(
                              currentOrder.statusDisplayName,
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.bold,
                                color: statusColor,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const Divider(height: 24),

                      // Shop Details
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
                          'Phone: ${currentOrder.shop?.phone ?? "N/A"}  ·  Village: ${currentOrder.shop?.village?.name ?? "N/A"}',
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Status Change Actions Card
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Change Order Status',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 12),
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        children: [
                          _buildStatusButton(context, currentOrder, 'pending', 'Pending', Colors.orange),
                          _buildStatusButton(context, currentOrder, 'confirmed', 'Confirmed', Colors.lightBlue),
                          _buildStatusButton(context, currentOrder, 'preparing', 'Preparing', Colors.purple),
                          _buildStatusButton(context, currentOrder, 'dispatched', 'Dispatched', Colors.blue),
                          _buildStatusButton(context, currentOrder, 'delivered', 'Delivered', Colors.green),
                          _buildStatusButton(context, currentOrder, 'cancelled', 'Cancelled', Colors.red),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Items Table Card
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Ordered Items',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 12),
                      ListView.separated(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: currentOrder.items.length,
                        separatorBuilder: (ctx, i) => const Divider(height: 16),
                        itemBuilder: (context, index) {
                          final item = currentOrder.items[index];
                          return Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      item.productName,
                                      style: const TextStyle(
                                        fontSize: 14,
                                        fontWeight: FontWeight.w600,
                                        color: AppTheme.textPrimary,
                                      ),
                                    ),
                                    Text(
                                      '${item.quantity} ${item.unit} @ ₹${item.unitPrice.toStringAsFixed(2)}',
                                      style: const TextStyle(
                                        fontSize: 12,
                                        color: AppTheme.textSecondary,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              Text(
                                '₹${item.lineTotal.toStringAsFixed(2)}',
                                style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.bold,
                                  color: AppTheme.primaryColor,
                                ),
                              ),
                            ],
                          );
                        },
                      ),
                      const Divider(height: 24),

                      // Order Totals Summary
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Subtotal', style: TextStyle(color: AppTheme.textSecondary)),
                          Text('₹${currentOrder.subtotal.toStringAsFixed(2)}', style: const TextStyle(fontWeight: FontWeight.w600)),
                        ],
                      ),
                      if (currentOrder.discount > 0) ...[
                        const SizedBox(height: 6),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text('Discount', style: TextStyle(color: AppTheme.textSecondary)),
                            Text('-₹${currentOrder.discount.toStringAsFixed(2)}', style: const TextStyle(color: Colors.red, fontWeight: FontWeight.w600)),
                          ],
                        ),
                      ],
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Grand Total', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                          Text(
                            '₹${currentOrder.totalAmount.toStringAsFixed(2)}',
                            style: const TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                              color: AppTheme.primaryColor,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Stock Status Card
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: ListTile(
                  leading: Icon(
                    currentOrder.stockDeducted ? Icons.check_circle : Icons.hourglass_empty,
                    color: currentOrder.stockDeducted ? Colors.green : Colors.orange,
                  ),
                  title: const Text('Inventory Ledger Status'),
                  subtitle: Text(
                    currentOrder.stockDeducted
                        ? 'Stock automatically deducted from inventory'
                        : 'Pending stock deduction (Deducted upon confirmation/dispatch)',
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatusButton(
    BuildContext context,
    ShopOrderModel currentOrder,
    String statusKey,
    String label,
    Color color,
  ) {
    final isSelected = currentOrder.status == statusKey;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      selectedColor: color.withValues(alpha: 0.2),
      labelStyle: TextStyle(
        color: isSelected ? color : AppTheme.textPrimary,
        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
      ),
      onSelected: isSelected
          ? null
          : (selected) {
              if (selected) {
                Provider.of<ShopOrderProvider>(context, listen: false)
                    .updateShopOrderStatus(currentOrder.id, statusKey);
              }
            },
    );
  }
}
