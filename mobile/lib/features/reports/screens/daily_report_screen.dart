import 'package:flutter/material.dart';

import '../../../core/theme/app_theme.dart';
import '../models/daily_report_model.dart';
import '../widgets/report_section.dart';
import '../widgets/report_summary_card.dart';

class DailyReportScreen extends StatelessWidget {
  final DailyReportModel report;

  const DailyReportScreen({super.key, required this.report});

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // 1. FINANCIAL SUMMARY OVERVIEW
          ReportSection(
            title: 'Daily Financial Summary',
            icon: Icons.account_balance_wallet_outlined,
            child: Column(
              children: [
                Row(
                  children: [
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Shop Sales Revenue',
                        value: '₹${report.financial.totalSales.toStringAsFixed(2)}',
                        icon: Icons.payments_outlined,
                        iconColor: const Color(0xFF059669),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Farmer Milk Expense',
                        value: '₹${report.financial.milkExpense.toStringAsFixed(2)}',
                        icon: Icons.money_off_outlined,
                        iconColor: const Color(0xFFDC2626),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                ReportSummaryCard(
                  title: 'Net Daily Margin',
                  value: '₹${report.financial.netBalance.toStringAsFixed(2)}',
                  subtitle: report.financial.netBalance >= 0 ? 'Positive Margin' : 'Expense Exceeds Revenue',
                  icon: Icons.price_check_outlined,
                  iconColor: report.financial.netBalance >= 0 ? const Color(0xFF059669) : const Color(0xFFDC2626),
                ),
              ],
            ),
          ),

          // 2. MILK COLLECTION SUMMARY
          ReportSection(
            title: 'Milk Collection & Quality Metrics',
            icon: Icons.water_drop_outlined,
            child: Column(
              children: [
                Row(
                  children: [
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Total Milk Quantity',
                        value: '${report.collection.totalLitres.toStringAsFixed(1)} L',
                        subtitle: '${report.collection.farmersCount} Farmers',
                        icon: Icons.opacity,
                        iconColor: AppTheme.primaryColor,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Collection Amount',
                        value: '₹${report.collection.totalAmount.toStringAsFixed(2)}',
                        subtitle: 'Avg ₹${report.collection.farmersCount > 0 ? (report.collection.totalAmount / report.collection.farmersCount).toStringAsFixed(1) : 0}/Farmer',
                        icon: Icons.currency_rupee,
                        iconColor: const Color(0xFFD97706),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Average Fat %',
                        value: '${report.collection.avgFat.toStringAsFixed(2)}%',
                        icon: Icons.analytics_outlined,
                        iconColor: const Color(0xFF7C3AED),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Average SNF %',
                        value: '${report.collection.avgSnf.toStringAsFixed(2)}%',
                        icon: Icons.stacked_bar_chart,
                        iconColor: const Color(0xFF0284C7),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // 3. VILLAGE-WISE COLLECTION BREAKDOWN
          if (report.collection.villageBreakdown.isNotEmpty)
            ReportSection(
              title: 'Village Collection Breakdown',
              icon: Icons.location_city_outlined,
              child: ListView.separated(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: report.collection.villageBreakdown.length,
                separatorBuilder: (ctx, i) => const Divider(height: 12),
                itemBuilder: (context, index) {
                  final v = report.collection.villageBreakdown[index];
                  return Padding(
                    padding: const EdgeInsets.symmetric(vertical: 4.0),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                v.villageName,
                                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                              ),
                              Text(
                                '${v.farmersCount} Farmers',
                                style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                              ),
                            ],
                          ),
                        ),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Text(
                              '${v.totalLitres.toStringAsFixed(1)} L',
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppTheme.primaryColor),
                            ),
                            Text(
                              '₹${v.totalAmount.toStringAsFixed(2)}',
                              style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                            ),
                          ],
                        ),
                      ],
                    ),
                  );
                },
              ),
            ),

          // 4. MAIN CENTER RECEIVING & STOCK LEDGER
          ReportSection(
            title: 'Center Intake & Inventory Ledger',
            icon: Icons.inventory_2_outlined,
            child: Column(
              children: [
                Row(
                  children: [
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Intake Received',
                        value: '${report.center.totalReceived.toStringAsFixed(1)} L',
                        subtitle: '${report.center.recordsCount} Records',
                        icon: Icons.input_outlined,
                        iconColor: const Color(0xFF0284C7),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Collection Diff',
                        value: '${report.center.diffLitres >= 0 ? "+" : ""}${report.center.diffLitres.toStringAsFixed(1)} L',
                        subtitle: report.center.diffLitres >= 0 ? 'Surplus' : 'Variance Loss',
                        icon: Icons.compare_arrows_outlined,
                        iconColor: report.center.diffLitres >= 0 ? const Color(0xFF059669) : const Color(0xFFDC2626),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: AppTheme.backgroundColor,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceAround,
                    children: [
                      _buildStockMetric('Opening', '${report.stock.opening.toStringAsFixed(1)} L'),
                      _buildStockMetric('Stock In', '+${report.stock.stockIn.toStringAsFixed(1)} L', color: const Color(0xFF059669)),
                      _buildStockMetric('Stock Out', '-${report.stock.stockOut.toStringAsFixed(1)} L', color: const Color(0xFFDC2626)),
                      _buildStockMetric('Closing', '${report.stock.closing.toStringAsFixed(1)} L', color: AppTheme.primaryColor),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // 5. SHOP ORDERS SUMMARY
          ReportSection(
            title: 'Shop Orders Summary (${report.orders.totalCount} Orders)',
            icon: Icons.shopping_bag_outlined,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Total Order Value: ₹${report.orders.totalValue.toStringAsFixed(2)}',
                  style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                ),
                const SizedBox(height: 12),
                Wrap(
                  spacing: 10,
                  runSpacing: 8,
                  children: report.orders.byStatus.entries.map((e) {
                    return Chip(
                      label: Text('${e.key.toUpperCase()}: ${e.value}'),
                      backgroundColor: AppTheme.backgroundColor,
                    );
                  }).toList(),
                ),
              ],
            ),
          ),

          // 6. DELIVERY DISPATCHES SUMMARY
          ReportSection(
            title: 'Delivery Dispatches (${report.deliveries.totalCount} Dispatches)',
            icon: Icons.local_shipping_outlined,
            child: Wrap(
              spacing: 10,
              runSpacing: 8,
              children: report.deliveries.byStatus.entries.map((e) {
                return Chip(
                  label: Text('${e.key.toUpperCase()}: ${e.value}'),
                  backgroundColor: AppTheme.backgroundColor,
                );
              }).toList(),
            ),
          ),

          // 7. PRODUCTS SOLD BREAKDOWN
          if (report.products.items.isNotEmpty)
            ReportSection(
              title: 'Products Sold (${report.products.totalUnits.toStringAsFixed(0)} Units)',
              icon: Icons.category_outlined,
              child: ListView.separated(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: report.products.items.length,
                separatorBuilder: (ctx, i) => const Divider(height: 12),
                itemBuilder: (context, index) {
                  final p = report.products.items[index];
                  return Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          '${p.productName} (${p.unit})',
                          style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                        ),
                      ),
                      Text(
                        '${p.totalQty.toStringAsFixed(0)} ${p.unit} (₹${p.totalSales.toStringAsFixed(2)})',
                        style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.primaryColor),
                      ),
                    ],
                  );
                },
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildStockMetric(String label, String value, {Color color = AppTheme.textPrimary}) {
    return Column(
      children: [
        Text(
          value,
          style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: color),
        ),
        Text(
          label,
          style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary),
        ),
      ],
    );
  }
}
