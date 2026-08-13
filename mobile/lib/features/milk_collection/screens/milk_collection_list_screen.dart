import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../models/milk_collection_model.dart';
import '../providers/milk_collection_provider.dart';
import '../widgets/milk_collection_card.dart';

class MilkCollectionListScreen extends StatefulWidget {
  const MilkCollectionListScreen({super.key});

  @override
  State<MilkCollectionListScreen> createState() => _MilkCollectionListScreenState();
}

class _MilkCollectionListScreenState extends State<MilkCollectionListScreen> {
  final _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<MilkCollectionProvider>(context, listen: false);
      provider.fetchMilkCollections();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _confirmDelete(MilkCollectionModel collection) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Milk Collection Record?'),
        content: Text(
          'Are you sure you want to delete this collection entry of ${collection.milkQuantity.toStringAsFixed(1)}L for ${collection.farmer?.name ?? "Farmer #${collection.farmerId}"}? This action cannot be undone.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.of(ctx).pop();
              final provider = Provider.of<MilkCollectionProvider>(context, listen: false);
              final success = await provider.deleteMilkCollection(collection.id);
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Milk collection record deleted.'),
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

  void _selectDate(BuildContext context, MilkCollectionProvider provider) async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: now,
      firstDate: DateTime(now.year - 1),
      lastDate: DateTime(now.year + 1),
    );
    if (picked != null) {
      final formatted = picked.toString().substring(0, 10);
      provider.setDateFilter(formatted);
    }
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<MilkCollectionProvider>(context);
    final list = provider.collections;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Milk Collections'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: () => provider.fetchMilkCollections(),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.of(context).pushNamed(AppRoutes.milkCollectionCreate);
        },
        icon: const Icon(Icons.add),
        label: const Text('Collect Milk'),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await provider.fetchMilkCollections();
        },
        child: SafeArea(
          child: Column(
            children: [
              // Search & Filter Header
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  children: [
                    TextField(
                      controller: _searchController,
                      onChanged: (val) => provider.setSearchQuery(val),
                      decoration: InputDecoration(
                        hintText: 'Search farmer name, code, village...',
                        prefixIcon: const Icon(Icons.search),
                        suffixIcon: _searchController.text.isNotEmpty
                            ? IconButton(
                                icon: const Icon(Icons.clear),
                                onPressed: () {
                                  _searchController.clear();
                                  provider.setSearchQuery('');
                                },
                              )
                            : null,
                      ),
                    ),
                    const SizedBox(height: 10),
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: [
                          // Shift Filters
                          FilterChip(
                            label: const Text('All Shifts'),
                            selected: provider.selectedShift == null,
                            onSelected: (_) => provider.setShiftFilter(null),
                            selectedColor: AppTheme.primaryColor.withValues(alpha: 0.2),
                            checkmarkColor: AppTheme.primaryColor,
                          ),
                          const SizedBox(width: 8),
                          FilterChip(
                            label: const Text('Morning Shift'),
                            selected: provider.selectedShift == 'morning',
                            onSelected: (_) => provider.setShiftFilter('morning'),
                            selectedColor: const Color(0xFFFFEDD5),
                            checkmarkColor: const Color(0xFFEA580C),
                          ),
                          const SizedBox(width: 8),
                          FilterChip(
                            label: const Text('Evening Shift'),
                            selected: provider.selectedShift == 'evening',
                            onSelected: (_) => provider.setShiftFilter('evening'),
                            selectedColor: const Color(0xFFE0F2FE),
                            checkmarkColor: const Color(0xFF0284C7),
                          ),
                          const SizedBox(width: 8),
                          // Date Filter Chip
                          ActionChip(
                            avatar: Icon(
                              Icons.calendar_today,
                              size: 16,
                              color: provider.selectedDate != null
                                  ? AppTheme.primaryColor
                                  : AppTheme.textSecondary,
                            ),
                            label: Text(
                              provider.selectedDate ?? 'Select Date',
                              style: TextStyle(
                                color: provider.selectedDate != null
                                    ? AppTheme.primaryColor
                                    : AppTheme.textPrimary,
                                fontWeight: provider.selectedDate != null
                                    ? FontWeight.bold
                                    : FontWeight.normal,
                              ),
                            ),
                            onPressed: () => _selectDate(context, provider),
                          ),
                          if (provider.selectedDate != null ||
                              provider.selectedShift != null ||
                              provider.searchQuery.isNotEmpty) ...[
                            const SizedBox(width: 8),
                            ActionChip(
                              label: const Text('Clear All'),
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

              // Content List Area
              Expanded(
                child: provider.isLoading
                    ? const LoadingIndicator(message: 'Loading milk collections...')
                    : provider.hasError
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                const Icon(Icons.error_outline, size: 48, color: Colors.red),
                                const SizedBox(height: 12),
                                Text(
                                  provider.errorMessage ?? 'Failed to load milk collections.',
                                  textAlign: TextAlign.center,
                                  style: const TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                ),
                                const SizedBox(height: 16),
                                ElevatedButton.icon(
                                  onPressed: () => provider.fetchMilkCollections(),
                                  icon: const Icon(Icons.refresh),
                                  label: const Text('Retry'),
                                ),
                              ],
                            ),
                          )
                        : list.isEmpty
                            ? Center(
                                child: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    const Icon(Icons.water_drop_outlined, size: 56, color: Colors.grey),
                                    const SizedBox(height: 12),
                                    Text(
                                      provider.searchQuery.isNotEmpty || provider.selectedShift != null || provider.selectedDate != null
                                          ? 'No collections matching filter criteria'
                                          : 'No milk collections recorded yet.',
                                      style: const TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                    ),
                                    const SizedBox(height: 16),
                                    ElevatedButton.icon(
                                      onPressed: () {
                                        Navigator.of(context).pushNamed(AppRoutes.milkCollectionCreate);
                                      },
                                      icon: const Icon(Icons.add_circle_outline),
                                      label: const Text('Record Milk Collection'),
                                    ),
                                  ],
                                ),
                              )
                            : ListView.builder(
                                itemCount: list.length,
                                padding: const EdgeInsets.only(bottom: 80),
                                itemBuilder: (context, index) {
                                  final collection = list[index];
                                  return MilkCollectionCard(
                                    collection: collection,
                                    onTap: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.milkCollectionDetail,
                                        arguments: collection,
                                      );
                                    },
                                    onEdit: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.milkCollectionEdit,
                                        arguments: collection,
                                      );
                                    },
                                    onDelete: () => _confirmDelete(collection),
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
