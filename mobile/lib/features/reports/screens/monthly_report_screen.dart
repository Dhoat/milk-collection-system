import 'package:flutter/material.dart';

import '../../../core/theme/app_theme.dart';
import '../models/monthly_report_model.dart';
import '../widgets/report_section.dart';
import '../widgets/report_summary_card.dart';

class MonthlyReportScreen extends StatelessWidget {
  final MonthlyReportModel report;

  const MonthlyReportScreen({super.key, required this.report});

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // 1. MONTHLY FINANCIAL OVERVIEW
          ReportSection(
            title: '${report.monthName} ${report.year} Financial Performance',
            icon: Icons.account_balance_outlined,
            child: Column(
              children: [
                Row(
                  children: [
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Total Sales Revenue',
                        value: '₹${report.financial.totalSales.toStringAsFixed(2)}',
                        icon: Icons.payments_outlined,
                        iconColor: const Color(0xFF059669),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Total Milk Expense',
                        value: '₹${report.financial.milkExpense.toStringAsFixed(2)}',
                        icon: Icons.money_off_outlined,
                        iconColor: const Color(0xFFDC2626),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                ReportSummaryCard(
                  title: 'Net Monthly Margin',
                  value: '₹${report.financial.netBalance.toStringAsFixed(2)}',
                  subtitle: '${report.daysInMonth} Days Operational Window',
                  icon: Icons.price_check_outlined,
                  iconColor: report.financial.netBalance >= 0 ? const Color(0xFF059669) : const Color(0xFFDC2626),
                ),
              ],
            ),
          ),

          // 2. MONTHLY COLLECTION SUMMARY
          ReportSection(
            title: 'Monthly Collection Metrics',
            icon: Icons.water_drop_outlined,
            child: Column(
              children: [
                Row(
                  children: [
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Total Litres Collected',
                        value: '${report.collection.totalLitres.toStringAsFixed(1)} L',
                        subtitle: 'Avg ${report.collection.avgDaily.toStringAsFixed(1)} L/Day',
                        icon: Icons.opacity,
                        iconColor: AppTheme.primaryColor,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Total Milk Expense',
                        value: '₹${report.collection.totalAmount.toStringAsFixed(2)}',
                        subtitle: '${report.collection.farmersCount} Active Farmers',
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
                        title: 'Avg Fat %',
                        value: '${report.collection.avgFat.toStringAsFixed(2)}%',
                        icon: Icons.analytics_outlined,
                        iconColor: const Color(0xFF7C3AED),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Avg SNF %',
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

          // 3. TOP SHOPS PERFORMANCE
          if (report.orders.topShops.isNotEmpty)
            ReportSection(
              title: 'Top Performing Shops',
              icon: Icons.storefront_outlined,
              child: ListView.separated(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: report.orders.topShops.length,
                separatorBuilder: (ctx, i) => const Divider(height: 12),
                itemBuilder: (context, index) {
                  final s = report.orders.topShops[index];
                  final shopName = s.shop?.name ?? 'Shop #${s.shopId}';
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
                                shopName,
                                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                              ),
                              Text(
                                '${s.totalOrders} Orders',
                                style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                              ),
                            ],
                          ),
                        ),
                        Text(
                          '₹${s.totalSales.toStringAsFixed(2)}',
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppTheme.primaryColor),
                        ),
                      ],
                    ),
                  );
                },
              ),
            ),

          // 4. VILLAGE PERFORMANCE
          if (report.collection.villageBreakdown.isNotEmpty)
            ReportSection(
              title: 'Village Collection Performance',
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

          // 5. MONTHLY CENTER & INVENTORY
          ReportSection(
            title: 'Center Intake & Stock Summary',
            icon: Icons.inventory_2_outlined,
            child: Column(
              children: [
                Row(
                  children: [
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Monthly Intake',
                        value: '${report.center.totalReceived.toStringAsFixed(1)} L',
                        subtitle: 'Avg ${report.center.avgDaily.toStringAsFixed(1)} L/Day',
                        icon: Icons.input_outlined,
                        iconColor: const Color(0xFF0284C7),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Closing Stock',
                        value: '${report.center.closingStock.toStringAsFixed(1)} L',
                        subtitle: 'Opening: ${report.center.openingStock.toStringAsFixed(1)} L',
                        icon: Icons.store_outlined,
                        iconColor: const Color(0xFF059669),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // 6. ORDERS & DELIVERIES OVERVIEW
          ReportSection(
            title: 'Shop Orders & Dispatches Breakdown',
            icon: Icons.shopping_bag_outlined,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Orders: ${report.orders.totalCount} (Value: ₹${report.orders.totalValue.toStringAsFixed(2)})',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                Wrap(
                  spacing: 8,
                  runSpacing: 6,
                  children: report.orders.byStatus.entries.map((e) {
                    final statusLabel = e.key.replaceAll('_', ' ').toUpperCase();
                    return Chip(
                      visualDensity: VisualDensity.compact,
                      padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 0),
                      labelPadding: const EdgeInsets.symmetric(horizontal: 6, vertical: 0),
                      label: Text(
                        '$statusLabel: ${e.value}',
                        style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
                      ),
                      backgroundColor: AppTheme.backgroundColor,
                    );
                  }).toList(),
                ),
                const Divider(height: 24),
                Text(
                  'Deliveries: ${report.deliveries.totalCount} Dispatches',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                Wrap(
                  spacing: 8,
                  runSpacing: 6,
                  children: report.deliveries.byStatus.entries.map((e) {
                    final statusLabel = e.key.replaceAll('_', ' ').toUpperCase();
                    return Chip(
                      visualDensity: VisualDensity.compact,
                      padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 0),
                      labelPadding: const EdgeInsets.symmetric(horizontal: 6, vertical: 0),
                      label: Text(
                        '$statusLabel: ${e.value}',
                        style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
                      ),
                      backgroundColor: AppTheme.backgroundColor,
                    );
                  }).toList(),
                ),
              ],
            ),
          ),

          // 7. MONTHLY PRODUCTS SALES
          if (report.products.items.isNotEmpty)
            ReportSection(
              title: 'Monthly Product Sales (${report.products.totalUnits.toStringAsFixed(0)} Units)',
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
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                        ),
                      ),
                      const SizedBox(width: 8),
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
}
