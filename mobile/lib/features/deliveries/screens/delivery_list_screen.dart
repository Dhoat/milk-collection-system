import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../../auth/providers/auth_provider.dart';
import '../providers/delivery_provider.dart';
import '../widgets/delivery_card.dart';

class DeliveryListScreen extends StatefulWidget {
  const DeliveryListScreen({super.key});

  @override
  State<DeliveryListScreen> createState() => _DeliveryListScreenState();
}

class _DeliveryListScreenState extends State<DeliveryListScreen> {
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<DeliveryProvider>(context, listen: false);
      provider.fetchDeliveries();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final deliveryProvider = Provider.of<DeliveryProvider>(context);
    final userRole = authProvider.user?.role ?? '';
    final canCreate = userRole == 'super_admin' || userRole == 'manager' || userRole == 'center_staff';

    return Scaffold(
      appBar: AppBar(
        title: const Text('Milk Delivery Dispatches'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => deliveryProvider.fetchDeliveries(refresh: true),
            tooltip: 'Refresh Deliveries',
          ),
        ],
      ),
      body: SafeArea(
        child: Column(
          children: [
            // Search & Filter Header
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
              child: TextField(
                controller: _searchController,
                decoration: InputDecoration(
                  hintText: 'Search by delivery #, shop or order...',
                  prefixIcon: const Icon(Icons.search),
                  suffixIcon: _searchController.text.isNotEmpty
                      ? IconButton(
                          icon: const Icon(Icons.clear),
                          onPressed: () {
                            _searchController.clear();
                            deliveryProvider.setSearchQuery('');
                          },
                        )
                      : null,
                ),
                onChanged: (val) => deliveryProvider.setSearchQuery(val),
              ),
            ),

            // Status Filter Chips
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
              child: Row(
                children: [
                  _buildFilterChip(context, 'all', 'All Dispatches'),
                  _buildFilterChip(context, 'pending', 'Pending'),
                  _buildFilterChip(context, 'assigned', 'Assigned'),
                  _buildFilterChip(context, 'out_for_delivery', 'Out for Delivery'),
                  _buildFilterChip(context, 'delivered', 'Delivered'),
                  _buildFilterChip(context, 'failed', 'Failed'),
                  _buildFilterChip(context, 'cancelled', 'Cancelled'),
                ],
              ),
            ),

            // KPI Metrics Banner
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              child: Card(
                color: AppTheme.backgroundColor,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(12.0),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceAround,
                    children: [
                      _buildMetricItem('Total', '${deliveryProvider.totalCount}', AppTheme.textPrimary),
                      _buildMetricItem('Pending', '${deliveryProvider.pendingCount}', const Color(0xFFD97706)),
                      _buildMetricItem('In Transit', '${deliveryProvider.outForDeliveryCount}', const Color(0xFF7C3AED)),
                      _buildMetricItem('Delivered', '${deliveryProvider.deliveredCount}', const Color(0xFF059669)),
                    ],
                  ),
                ),
              ),
            ),

            // Content Area
            Expanded(
              child: Builder(
                builder: (context) {
                  if (deliveryProvider.status == DeliveryStateStatus.loading &&
                      deliveryProvider.allDeliveries.isEmpty) {
                    return const Center(child: LoadingIndicator(message: 'Loading deliveries...'));
                  }

                  if (deliveryProvider.status == DeliveryStateStatus.error &&
                      deliveryProvider.allDeliveries.isEmpty) {
                    return Padding(
                      padding: const EdgeInsets.all(20.0),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          ErrorBanner(
                            message: deliveryProvider.errorMessage ?? 'Failed to load deliveries.',
                          ),
                          const SizedBox(height: 16),
                          ElevatedButton.icon(
                            onPressed: () => deliveryProvider.fetchDeliveries(refresh: true),
                            icon: const Icon(Icons.refresh),
                            label: const Text('Retry'),
                          ),
                        ],
                      ),
                    );
                  }

                  final list = deliveryProvider.deliveries;

                  if (list.isEmpty) {
                    return RefreshIndicator(
                      onRefresh: () => deliveryProvider.fetchDeliveries(refresh: true),
                      child: ListView(
                        physics: const AlwaysScrollableScrollPhysics(),
                        padding: const EdgeInsets.all(40),
                        children: [
                          Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(Icons.local_shipping_outlined, size: 64, color: Colors.grey.shade400),
                                const SizedBox(height: 16),
                                const Text(
                                  'No deliveries found',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                    color: AppTheme.textPrimary,
                                  ),
                                ),
                                const SizedBox(height: 6),
                                const Text(
                                  'Try adjusting your search filter or dispatch a new shop order.',
                                  textAlign: TextAlign.center,
                                  style: TextStyle(color: AppTheme.textSecondary),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    );
                  }

                  return RefreshIndicator(
                    onRefresh: () => deliveryProvider.fetchDeliveries(refresh: true),
                    child: ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: list.length,
                      itemBuilder: (context, index) {
                        final delivery = list[index];
                        return DeliveryCard(
                          delivery: delivery,
                          onTap: () {
                            Navigator.of(context).pushNamed(
                              AppRoutes.deliveryDetail,
                              arguments: delivery,
                            );
                          },
                          onUpdateStatus: canCreate && !delivery.isTerminalState
                              ? () {
                                  Navigator.of(context).pushNamed(
                                    AppRoutes.updateDeliveryStatus,
                                    arguments: delivery,
                                  );
                                }
                              : null,
                        );
                      },
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
      floatingActionButton: canCreate
          ? FloatingActionButton.extended(
              onPressed: () {
                Navigator.of(context).pushNamed(AppRoutes.createDelivery);
              },
              backgroundColor: AppTheme.primaryColor,
              icon: const Icon(Icons.add_location_alt_outlined),
              label: const Text('Dispatch Order'),
            )
          : null,
    );
  }

  Widget _buildFilterChip(BuildContext context, String key, String label) {
    final provider = Provider.of<DeliveryProvider>(context);
    final isSelected = provider.statusFilter == key;

    return Padding(
      padding: const EdgeInsets.only(right: 8.0),
      child: FilterChip(
        label: Text(label),
        selected: isSelected,
        selectedColor: AppTheme.primaryColor.withValues(alpha: 0.2),
        checkmarkColor: AppTheme.primaryColor,
        labelStyle: TextStyle(
          color: isSelected ? AppTheme.primaryColor : AppTheme.textPrimary,
          fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
        ),
        onSelected: (selected) {
          provider.setStatusFilter(key);
        },
      ),
    );
  }

  Widget _buildMetricItem(String label, String value, Color color) {
    return Column(
      children: [
        Text(
          value,
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        Text(
          label,
          style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary),
        ),
      ],
    );
  }
}
