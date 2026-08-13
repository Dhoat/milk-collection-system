import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../models/village_model.dart';
import '../providers/village_provider.dart';
import '../widgets/village_card.dart';

class VillageListScreen extends StatefulWidget {
  const VillageListScreen({super.key});

  @override
  State<VillageListScreen> createState() => _VillageListScreenState();
}

class _VillageListScreenState extends State<VillageListScreen> {
  final _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final villageProvider = Provider.of<VillageProvider>(context, listen: false);
      villageProvider.fetchVillages();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _confirmDelete(VillageModel village) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Village?'),
        content: Text(
          'Are you sure you want to delete "${village.name}"? This operation cannot be undone.',
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
              final provider = Provider.of<VillageProvider>(context, listen: false);
              final success = await provider.deleteVillage(village.id);
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text('Village "${village.name}" deleted.'),
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
    final villageProvider = Provider.of<VillageProvider>(context);
    final list = villageProvider.villages;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Village Management'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: () => villageProvider.fetchVillages(),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.of(context).pushNamed(AppRoutes.villageCreate);
        },
        icon: const Icon(Icons.add),
        label: const Text('Add Village'),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await villageProvider.fetchVillages();
        },
        child: SafeArea(
          child: Column(
            children: [
              // Search Bar
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: TextField(
                  controller: _searchController,
                  onChanged: (val) => villageProvider.setSearchQuery(val),
                  decoration: InputDecoration(
                    hintText: 'Search by village name or code...',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon: _searchController.text.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.clear),
                            onPressed: () {
                              _searchController.clear();
                              villageProvider.setSearchQuery('');
                            },
                          )
                        : null,
                  ),
                ),
              ),

              // Error Notification Banner
              if (villageProvider.errorMessage != null)
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0),
                  child: ErrorBanner(
                    message: villageProvider.errorMessage!,
                    onDismiss: () => villageProvider.clearMessages(),
                  ),
                ),

              // Content Area
              Expanded(
                child: villageProvider.isLoading
                    ? const LoadingIndicator(message: 'Loading villages...')
                    : villageProvider.hasError
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                const Icon(Icons.error_outline, size: 48, color: Colors.red),
                                const SizedBox(height: 12),
                                Text(
                                  villageProvider.errorMessage ?? 'Failed to load villages.',
                                  textAlign: TextAlign.center,
                                  style: const TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                ),
                                const SizedBox(height: 16),
                                ElevatedButton.icon(
                                  onPressed: () => villageProvider.fetchVillages(),
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
                                    const Icon(Icons.location_city_outlined, size: 56, color: Colors.grey),
                                    const SizedBox(height: 12),
                                    Text(
                                      villageProvider.searchQuery.isNotEmpty
                                          ? 'No villages matching "${villageProvider.searchQuery}"'
                                          : 'No villages registered yet.',
                                      style: const TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                    ),
                                    const SizedBox(height: 16),
                                    ElevatedButton.icon(
                                      onPressed: () {
                                        Navigator.of(context).pushNamed(AppRoutes.villageCreate);
                                      },
                                      icon: const Icon(Icons.add),
                                      label: const Text('Add First Village'),
                                    ),
                                  ],
                                ),
                              )
                            : ListView.builder(
                                itemCount: list.length,
                                padding: const EdgeInsets.only(bottom: 80),
                                itemBuilder: (context, index) {
                                  final village = list[index];
                                  return VillageCard(
                                    village: village,
                                    onTap: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.villageDetail,
                                        arguments: village,
                                      );
                                    },
                                    onEdit: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.villageEdit,
                                        arguments: village,
                                      );
                                    },
                                    onDelete: () => _confirmDelete(village),
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
