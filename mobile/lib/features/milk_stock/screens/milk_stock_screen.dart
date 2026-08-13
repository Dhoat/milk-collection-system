import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../providers/milk_stock_provider.dart';
import '../widgets/milk_stock_card.dart';
import '../widgets/stock_kpi_card.dart';

class MilkStockScreen extends StatefulWidget {
  const MilkStockScreen({super.key});

  @override
  State<MilkStockScreen> createState() => _MilkStockScreenState();
}

class _MilkStockScreenState extends State<MilkStockScreen> {
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<MilkStockProvider>(context, listen: false);
      provider.fetchMilkStocks();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<MilkStockProvider>(context);
    final summary = provider.summary;
    final transactions = provider.transactions;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Milk Stock & Inventory'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh Ledger',
            onPressed: () => provider.fetchMilkStocks(),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.of(context).pushNamed(AppRoutes.milkStockOut);
        },
        icon: const Icon(Icons.remove_circle_outline),
        backgroundColor: const Color(0xFFDC2626),
        label: const Text('Record Stock OUT'),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await provider.fetchMilkStocks();
        },
        child: SafeArea(
          child: Column(
            children: [
              // Summary KPI Card Header
              if (summary != null)
                Padding(
                  padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
                  child: StockKpiCard(summary: summary),
                ),

              // Filter & Search Controls
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 6.0),
                child: Column(
                  children: [
                    TextField(
                      controller: _searchController,
                      decoration: InputDecoration(
                        hintText: 'Search by source, reason or notes...',
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
                            label: const Text('All Transactions'),
                            selected: provider.selectedType == null,
                            onSelected: (_) => provider.setTypeFilter(null),
                            selectedColor: AppTheme.primaryColor.withValues(alpha: 0.2),
                            checkmarkColor: AppTheme.primaryColor,
                          ),
                          const SizedBox(width: 8),
                          FilterChip(
                            label: const Text('Stock IN (Receivings)'),
                            selected: provider.selectedType == 'in',
                            onSelected: (_) => provider.setTypeFilter('in'),
                            selectedColor: const Color(0xFFD1FAE5),
                            checkmarkColor: const Color(0xFF065F46),
                          ),
                          const SizedBox(width: 8),
                          FilterChip(
                            label: const Text('Stock OUT (Manual)'),
                            selected: provider.selectedType == 'out',
                            onSelected: (_) => provider.setTypeFilter('out'),
                            selectedColor: const Color(0xFFFEE2E2),
                            checkmarkColor: const Color(0xFF991B1B),
                          ),
                          if (provider.selectedType != null || provider.searchQuery.isNotEmpty) ...[
                            const SizedBox(width: 8),
                            ActionChip(
                              label: const Text('Clear Filters'),
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

              // Error Banner
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
                    ? const LoadingIndicator(message: 'Loading stock inventory ledger...')
                    : provider.hasError
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                const Icon(Icons.error_outline, size: 48, color: Colors.red),
                                const SizedBox(height: 12),
                                Text(
                                  provider.errorMessage ?? 'Failed to load stock ledger.',
                                  textAlign: TextAlign.center,
                                  style: const TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                ),
                                const SizedBox(height: 16),
                                ElevatedButton.icon(
                                  onPressed: () => provider.fetchMilkStocks(),
                                  icon: const Icon(Icons.refresh),
                                  label: const Text('Retry'),
                                ),
                              ],
                            ),
                          )
                        : transactions.isEmpty
                            ? Center(
                                child: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    const Icon(Icons.inventory_2_outlined, size: 56, color: Colors.grey),
                                    const SizedBox(height: 12),
                                    const Text(
                                      'No stock ledger transactions recorded yet.',
                                      style: TextStyle(fontSize: 15, color: AppTheme.textSecondary),
                                    ),
                                  ],
                                ),
                              )
                            : ListView.builder(
                                itemCount: transactions.length,
                                padding: const EdgeInsets.only(bottom: 80),
                                itemBuilder: (context, index) {
                                  final item = transactions[index];
                                  return MilkStockCard(transaction: item);
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
