import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../../villages/providers/village_provider.dart';
import '../models/farmer_model.dart';
import '../providers/farmer_provider.dart';
import '../widgets/farmer_card.dart';

class FarmerListScreen extends StatefulWidget {
  const FarmerListScreen({super.key});

  @override
  State<FarmerListScreen> createState() => _FarmerListScreenState();
}

class _FarmerListScreenState extends State<FarmerListScreen> {
  final _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final farmerProvider = Provider.of<FarmerProvider>(context, listen: false);
      final villageProvider = Provider.of<VillageProvider>(context, listen: false);

      farmerProvider.fetchFarmers();
      if (villageProvider.allVillages.isEmpty) {
        villageProvider.fetchVillages();
      }
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _confirmDelete(FarmerModel farmer) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Farmer Record?'),
        content: Text(
          'Are you sure you want to remove farmer "${farmer.name}" (${farmer.farmerCode})? This action cannot be undone.',
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
              final provider = Provider.of<FarmerProvider>(context, listen: false);
              final success = await provider.deleteFarmer(farmer.id);
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text('Farmer "${farmer.name}" record deleted.'),
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
    final farmerProvider = Provider.of<FarmerProvider>(context);
    final villageProvider = Provider.of<VillageProvider>(context);
    final list = farmerProvider.farmers;
    final villages = villageProvider.allVillages;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Farmer Management'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: () => farmerProvider.fetchFarmers(),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.of(context).pushNamed(AppRoutes.farmerCreate);
        },
        icon: const Icon(Icons.person_add_alt_1),
        label: const Text('Add Farmer'),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await farmerProvider.fetchFarmers();
        },
        child: SafeArea(
          child: Column(
            children: [
              // Search & Village Filter Bar
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  children: [
                    TextField(
                      controller: _searchController,
                      onChanged: (val) => farmerProvider.setSearchQuery(val),
                      decoration: InputDecoration(
                        hintText: 'Search by name, code, mobile, village...',
                        prefixIcon: const Icon(Icons.search),
                        suffixIcon: _searchController.text.isNotEmpty
                            ? IconButton(
                                icon: const Icon(Icons.clear),
                                onPressed: () {
                                  _searchController.clear();
                                  farmerProvider.setSearchQuery('');
                                },
                              )
                            : null,
                      ),
                    ),
                    const SizedBox(height: 10),
                    if (villages.isNotEmpty)
                      SingleChildScrollView(
                        scrollDirection: Axis.horizontal,
                        child: Row(
                          children: [
                            FilterChip(
                              label: const Text('All Villages'),
                              selected: farmerProvider.selectedVillageId == null,
                              onSelected: (_) => farmerProvider.setVillageFilter(null),
                              selectedColor: AppTheme.primaryColor.withValues(alpha: 0.2),
                              checkmarkColor: AppTheme.primaryColor,
                            ),
                            const SizedBox(width: 8),
                            ...villages.map(
                              (v) => Padding(
                                padding: const EdgeInsets.only(right: 8.0),
                                child: FilterChip(
                                  label: Text('${v.name} (${v.code})'),
                                  selected: farmerProvider.selectedVillageId == v.id,
                                  onSelected: (_) => farmerProvider.setVillageFilter(v.id),
                                  selectedColor: AppTheme.primaryColor.withValues(alpha: 0.2),
                                  checkmarkColor: AppTheme.primaryColor,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                  ],
                ),
              ),

              // Error Notification Banner
              if (farmerProvider.errorMessage != null)
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0),
                  child: ErrorBanner(
                    message: farmerProvider.errorMessage!,
                    onDismiss: () => farmerProvider.clearMessages(),
                  ),
                ),

              // Content List Area
              Expanded(
                child: farmerProvider.isLoading
                    ? const LoadingIndicator(message: 'Loading farmers...')
                    : farmerProvider.hasError
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                const Icon(Icons.error_outline, size: 48, color: Colors.red),
                                const SizedBox(height: 12),
                                Text(
                                  farmerProvider.errorMessage ?? 'Failed to load farmers.',
                                  textAlign: TextAlign.center,
                                  style: const TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                ),
                                const SizedBox(height: 16),
                                ElevatedButton.icon(
                                  onPressed: () => farmerProvider.fetchFarmers(),
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
                                    const Icon(Icons.people_outline, size: 56, color: Colors.grey),
                                    const SizedBox(height: 12),
                                    Text(
                                      farmerProvider.searchQuery.isNotEmpty || farmerProvider.selectedVillageId != null
                                          ? 'No farmers matching filter criteria'
                                          : 'No farmers registered yet.',
                                      style: const TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                    ),
                                    const SizedBox(height: 16),
                                    ElevatedButton.icon(
                                      onPressed: () {
                                        Navigator.of(context).pushNamed(AppRoutes.farmerCreate);
                                      },
                                      icon: const Icon(Icons.person_add),
                                      label: const Text('Register First Farmer'),
                                    ),
                                  ],
                                ),
                              )
                            : ListView.builder(
                                itemCount: list.length,
                                padding: const EdgeInsets.only(bottom: 80),
                                itemBuilder: (context, index) {
                                  final farmer = list[index];
                                  return FarmerCard(
                                    farmer: farmer,
                                    onTap: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.farmerDetail,
                                        arguments: farmer,
                                      );
                                    },
                                    onEdit: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.farmerEdit,
                                        arguments: farmer,
                                      );
                                    },
                                    onDelete: () => _confirmDelete(farmer),
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
