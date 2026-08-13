import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../models/shop_model.dart';
import '../providers/shop_provider.dart';
import '../widgets/shop_card.dart';

class ShopListScreen extends StatefulWidget {
  const ShopListScreen({super.key});

  @override
  State<ShopListScreen> createState() => _ShopListScreenState();
}

class _ShopListScreenState extends State<ShopListScreen> {
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<ShopProvider>(context, listen: false);
      provider.fetchShops();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _confirmDelete(ShopModel shop) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Shop?'),
        content: Text('Are you sure you want to delete ${shop.name} (${shop.shopCode})?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.of(ctx).pop();
              final provider = Provider.of<ShopProvider>(context, listen: false);
              final success = await provider.deleteShop(shop.id);
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Shop deleted.'),
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
    final provider = Provider.of<ShopProvider>(context);
    final shops = provider.shops;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Shop Management'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: () => provider.fetchShops(),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.of(context).pushNamed(AppRoutes.shopCreate);
        },
        icon: const Icon(Icons.add),
        label: const Text('Add Shop'),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await provider.fetchShops();
        },
        child: SafeArea(
          child: Column(
            children: [
              // Search & Filter Controls
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  children: [
                    TextField(
                      controller: _searchController,
                      decoration: InputDecoration(
                        hintText: 'Search by shop name, code, owner or phone...',
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
                          FilterChip(
                            label: const Text('All Statuses'),
                            selected: provider.statusFilter == null,
                            onSelected: (_) => provider.setStatusFilter(null),
                            selectedColor: AppTheme.primaryColor.withValues(alpha: 0.2),
                            checkmarkColor: AppTheme.primaryColor,
                          ),
                          const SizedBox(width: 8),
                          FilterChip(
                            label: const Text('Active Only'),
                            selected: provider.statusFilter == true,
                            onSelected: (_) => provider.setStatusFilter(true),
                            selectedColor: const Color(0xFFD1FAE5),
                            checkmarkColor: const Color(0xFF065F46),
                          ),
                          const SizedBox(width: 8),
                          FilterChip(
                            label: const Text('Inactive Only'),
                            selected: provider.statusFilter == false,
                            onSelected: (_) => provider.setStatusFilter(false),
                            selectedColor: const Color(0xFFF3F4F6),
                            checkmarkColor: const Color(0xFF6B7280),
                          ),
                          if (provider.statusFilter != null || provider.searchQuery.isNotEmpty) ...[
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
                    ? const LoadingIndicator(message: 'Loading shops...')
                    : provider.hasError
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                const Icon(Icons.error_outline, size: 48, color: Colors.red),
                                const SizedBox(height: 12),
                                Text(
                                  provider.errorMessage ?? 'Failed to load shops.',
                                  textAlign: TextAlign.center,
                                  style: const TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                ),
                                const SizedBox(height: 16),
                                ElevatedButton.icon(
                                  onPressed: () => provider.fetchShops(),
                                  icon: const Icon(Icons.refresh),
                                  label: const Text('Retry'),
                                ),
                              ],
                            ),
                          )
                        : shops.isEmpty
                            ? Center(
                                child: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    const Icon(Icons.storefront_outlined, size: 56, color: Colors.grey),
                                    const SizedBox(height: 12),
                                    const Text(
                                      'No shops found.',
                                      style: TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                    ),
                                    const SizedBox(height: 16),
                                    ElevatedButton.icon(
                                      onPressed: () {
                                        Navigator.of(context).pushNamed(AppRoutes.shopCreate);
                                      },
                                      icon: const Icon(Icons.add),
                                      label: const Text('Add First Shop'),
                                    ),
                                  ],
                                ),
                              )
                            : ListView.builder(
                                itemCount: shops.length,
                                padding: const EdgeInsets.only(bottom: 80),
                                itemBuilder: (context, index) {
                                  final shop = shops[index];
                                  return ShopCard(
                                    shop: shop,
                                    onTap: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.shopDetail,
                                        arguments: shop,
                                      );
                                    },
                                    onEdit: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.shopEdit,
                                        arguments: shop,
                                      );
                                    },
                                    onToggleStatus: () {
                                      provider.toggleShopStatus(shop.id);
                                    },
                                    onDelete: () => _confirmDelete(shop),
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
}
