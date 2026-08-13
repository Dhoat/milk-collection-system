import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../models/milk_receiving_model.dart';
import '../providers/milk_receiving_provider.dart';
import '../widgets/milk_receiving_card.dart';

class MilkReceivingListScreen extends StatefulWidget {
  const MilkReceivingListScreen({super.key});

  @override
  State<MilkReceivingListScreen> createState() => _MilkReceivingListScreenState();
}

class _MilkReceivingListScreenState extends State<MilkReceivingListScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<MilkReceivingProvider>(context, listen: false);
      provider.fetchMilkReceivings();
    });
  }

  void _confirmDelete(MilkReceivingModel receiving) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Milk Receiving Record?'),
        content: Text(
          'Are you sure you want to delete receiving entry of ${receiving.receivedQuantity.toStringAsFixed(1)}L from ${receiving.village?.name ?? "Village #${receiving.villageId}"}? This will automatically adjust stock inventory.',
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
              final provider = Provider.of<MilkReceivingProvider>(context, listen: false);
              final success = await provider.deleteMilkReceiving(receiving.id);
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Milk receiving record deleted.'),
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

  void _selectDate(BuildContext context, MilkReceivingProvider provider) async {
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
    final provider = Provider.of<MilkReceivingProvider>(context);
    final list = provider.receivings;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Milk Receiving'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: () => provider.fetchMilkReceivings(),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.of(context).pushNamed(AppRoutes.milkReceivingCreate);
        },
        icon: const Icon(Icons.add),
        label: const Text('New Batch Receiving'),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await provider.fetchMilkReceivings();
        },
        child: SafeArea(
          child: Column(
            children: [
              // Filter Chips Row
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
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
                          provider.selectedVillageId != null) ...[
                        const SizedBox(width: 8),
                        ActionChip(
                          label: const Text('Clear All'),
                          avatar: const Icon(Icons.clear_all, size: 16),
                          onPressed: () => provider.clearFilters(),
                        ),
                      ],
                    ],
                  ),
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
                    ? const LoadingIndicator(message: 'Loading milk receiving records...')
                    : provider.hasError
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                const Icon(Icons.error_outline, size: 48, color: Colors.red),
                                const SizedBox(height: 12),
                                Text(
                                  provider.errorMessage ?? 'Failed to load milk receiving records.',
                                  textAlign: TextAlign.center,
                                  style: const TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                ),
                                const SizedBox(height: 16),
                                ElevatedButton.icon(
                                  onPressed: () => provider.fetchMilkReceivings(),
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
                                    const Icon(Icons.local_shipping_outlined, size: 56, color: Colors.grey),
                                    const SizedBox(height: 12),
                                    const Text(
                                      'No milk receiving records found.',
                                      style: TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                    ),
                                    const SizedBox(height: 16),
                                    ElevatedButton.icon(
                                      onPressed: () {
                                        Navigator.of(context).pushNamed(AppRoutes.milkReceivingCreate);
                                      },
                                      icon: const Icon(Icons.add_circle_outline),
                                      label: const Text('Record Milk Receiving'),
                                    ),
                                  ],
                                ),
                              )
                            : ListView.builder(
                                itemCount: list.length,
                                padding: const EdgeInsets.only(bottom: 80),
                                itemBuilder: (context, index) {
                                  final receiving = list[index];
                                  return MilkReceivingCard(
                                    receiving: receiving,
                                    onTap: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.milkReceivingDetail,
                                        arguments: receiving,
                                      );
                                    },
                                    onEdit: () {
                                      Navigator.of(context).pushNamed(
                                        AppRoutes.milkReceivingEdit,
                                        arguments: receiving,
                                      );
                                    },
                                    onDelete: () => _confirmDelete(receiving),
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
