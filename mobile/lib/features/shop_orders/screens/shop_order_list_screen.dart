import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../models/shop_order_model.dart';
import '../providers/shop_order_provider.dart';
import '../widgets/shop_order_card.dart';

class ShopOrderListScreen extends StatefulWidget {
  const ShopOrderListScreen({super.key});

  @override
  State<ShopOrderListScreen> createState() => _ShopOrderListScreenState();
}

class _ShopOrderListScreenState extends State<ShopOrderListScreen> {
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<ShopOrderProvider>(context, listen: false);
      provider.fetchShopOrders();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _confirmDelete(ShopOrderModel order) {
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
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Shop order deleted.'),
                    backgroundColor: Colors.red,
                  ),
                );
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
    final provider = Provider.of<ShopOrderProvider>(context);
    final orders = provider.orders;

    // Calculate Summary Metrics
    final totalOrders = orders.length;
    final totalQty = orders.fold(0.0, (sum, o) => sum + o.totalQuantity);
    final totalAmount = orders.fold(0.0, (sum, o) => sum + o.totalAmount);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Shop Orders'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: () => provider.fetchShopOrders(),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.of(context).pushNamed(AppRoutes.shopOrderCreate);
        },
        icon: const Icon(Icons.add_shopping_cart),
        label: const Text('Create Order'),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await provider.fetchShopOrders();
        },
        child: SafeArea(
          child: Column(
            children: [
              // Summary KPI Header
              Container(
                margin: const EdgeInsets.fromLTRB(16, 12, 16, 8),
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF065F46), Color(0xFF047857)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [
                    BoxShadow(
                      color: AppTheme.primaryColor.withValues(alpha: 0.3),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,
                  children: [
                    _buildSummaryItem('Total Orders', totalOrders.toString(), Icons.receipt_long),
                    Container(height: 36, width: 1, color: Colors.white30),
                    _buildSummaryItem('Total Qty', '${totalQty.toStringAsFixed(1)} L/Kg', Icons.scale),
                    Container(height: 36, width: 1, color: Colors.white30),
                    _buildSummaryItem('Total Value', '₹${totalAmount.toStringAsFixed(0)}', Icons.payments),
                  ],
                ),
              ),

              // Search & Filter Controls
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
                child: Column(
                  children: [
                    TextField(
                      controller: _searchController,
                      decoration: InputDecoration(
                        hintText: 'Search by order number or shop name...',
                        prefixIcon: const Icon(Icons.search, size: 20),
                        suffixIcon: _searchController.text.isNotEmpty
                            ? IconButton(
                                icon: const Icon(Icons.clear, size: 18),
                                onPressed: () {
                                  _searchController.clear();
                                  provider.setSearchQuery('');
                                },
                              )
                            : null,
                        contentPadding: const EdgeInsets.symmetric(vertical: 10, horizontal: 16),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
                        ),
                      ),
                      onSubmitted: (query) => provider.setSearchQuery(query),
                    ),
                    const SizedBox(height: 8),
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: [
                          _buildFilterChip(provider, 'All', null),
                          const SizedBox(width: 6),
                          _buildFilterChip(provider, 'Pending', 'pending'),
                          const SizedBox(width: 6),
                          _buildFilterChip(provider, 'Confirmed', 'confirmed'),
                          const SizedBox(width: 6),
                          _buildFilterChip(provider, 'Preparing', 'preparing'),
                          const SizedBox(width: 6),
                          _buildFilterChip(provider, 'Dispatched', 'dispatched'),
                          const SizedBox(width: 6),
                          _buildFilterChip(provider, 'Delivered', 'delivered'),
                          const SizedBox(width: 6),
                          _buildFilterChip(provider, 'Cancelled', 'cancelled'),
                          if (provider.selectedStatus != null || provider.searchQuery.isNotEmpty) ...[
                            const SizedBox(width: 8),
                            ActionChip(
                              label: const Text('Clear'),
                              avatar: const Icon(Icons.clear_all, size: 16),
                              onPressed: () {
                                _searchController.clear();
                                provider.clearFilters();
                              },
                            ),
                          ],
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              // Error Notification Banner
              if (provider.errorMessage != null)
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0),
                  child: ErrorBanner(
                    message: provider.errorMessage!,
                    onDismiss: () => provider.clearMessages(),
                  ),
                ),

              // Content Area
              Expanded(
                child: provider.isLoading
                    ? const LoadingIndicator(message: 'Loading shop orders...')
                    : provider.hasError
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                const Icon(Icons.error_outline, size: 48, color: Colors.red),
                                const SizedBox(height: 12),
                                Text(
                                  provider.errorMessage ?? 'Failed to load shop orders.',
                                  textAlign: TextAlign.center,
                                  style: const TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                ),
                                const SizedBox(height: 16),
                                ElevatedButton.icon(
                                  onPressed: () => provider.fetchShopOrders(),
                                  icon: const Icon(Icons.refresh),
                                  label: const Text('Retry'),
                                ),
                              ],
                            ),
                          )
                        : orders.isEmpty
                            ? Center(
                                child: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    const Icon(Icons.shopping_bag_outlined, size: 56, color: Colors.grey),
                                    const SizedBox(height: 12),
                                    const Text(
                                      'No shop orders found.',
                                      style: TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                    ),
                                    const SizedBox(height: 16),
                                    ElevatedButton.icon(
                                      onPressed: () {
                                        Navigator.of(context).pushNamed(AppRoutes.shopOrderCreate);
                                      },
                                      icon: const Icon(Icons.add),
                                      label: const Text('Create First Order'),
                                    ),
                                  ],
                                ),
                              )
                            : ListView.builder(
                                itemCount: orders.length,
                                padding: const EdgeInsets.only(bottom: 80),
                                itemBuilder: (context, index) {
                                  final order = orders[index];
                                  return ShopOrderCard(
                                    order: order,
                                    onTap: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.shopOrderDetail,
                                        arguments: order,
                                      );
                                    },
                                    onStatusSelected: (newStatus) {
                                      provider.updateShopOrderStatus(order.id, newStatus);
                                    },
                                    onDelete: () => _confirmDelete(order),
                                  );
                                },
                              ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSummaryItem(String label, String value, IconData icon) {
    return Column(
      children: [
        Icon(icon, color: Colors.white70, size: 20),
        const SizedBox(height: 4),
        Text(
          value,
          style: const TextStyle(
            color: Colors.white,
            fontSize: 16,
            fontWeight: FontWeight.bold,
          ),
        ),
        Text(
          label,
          style: const TextStyle(
            color: Colors.white70,
            fontSize: 11,
          ),
        ),
      ],
    );
  }

  Widget _buildFilterChip(ShopOrderProvider provider, String label, String? value) {
    final isSelected = provider.selectedStatus == value;
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (_) => provider.setStatusFilter(value),
      selectedColor: AppTheme.primaryColor.withValues(alpha: 0.2),
      checkmarkColor: AppTheme.primaryColor,
    );
  }
}
