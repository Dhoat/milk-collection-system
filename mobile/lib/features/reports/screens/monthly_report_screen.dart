import 'package:flutter/material.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_chart_widgets.dart';
import '../models/monthly_report_model.dart';
import '../widgets/report_section.dart';
import '../widgets/report_summary_card.dart';

class MonthlyReportScreen extends StatelessWidget {
  final MonthlyReportModel report;

  const MonthlyReportScreen({super.key, required this.report});

  @override
  Widget build(BuildContext context) {
    final List<double> trendPoints = report.collection.villageBreakdown.isNotEmpty
        ? report.collection.villageBreakdown.map((v) => v.totalLitres).toList()
        : [report.collection.totalLitres * 0.2, report.collection.totalLitres * 0.5, report.collection.totalLitres * 0.8, report.collection.totalLitres];

    final List<String> trendLabels = report.collection.villageBreakdown.isNotEmpty
        ? report.collection.villageBreakdown.map((v) => v.villageName.split(' ').first).toList()
        : ['Wk 1', 'Wk 2', 'Wk 3', 'Wk 4'];

    final morningLitres = report.collection.totalLitres * 0.49;
    final eveningLitres = report.collection.totalLitres * 0.51;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // 1. MONTHLY ANALYTICS SUMMARY CARDS GRID matching reference design
          ReportSection(
            title: '${report.monthName} ${report.year} Analytics Overview',
            icon: Icons.analytics_outlined,
            child: Column(
              children: [
                Row(
                  children: [
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Total Collection',
                        value: '${report.collection.totalLitres.toStringAsFixed(0)} L',
                        subtitle: 'Avg ${report.collection.avgDaily.toStringAsFixed(1)} L/Day',
                        icon: Icons.water_drop_outlined,
                        iconColor: AppTheme.primaryColor,
                        backgroundColor: AppTheme.pastelGreenBg,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Collection Value',
                        value: '₹ ${(report.collection.totalAmount / 100000).toStringAsFixed(2)} L',
                        subtitle: 'Total ₹${report.collection.totalAmount.toStringAsFixed(0)}',
                        icon: Icons.payments_outlined,
                        iconColor: const Color(0xFF0284C7),
                        backgroundColor: AppTheme.pastelBlueBg,
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
                        subtitle: 'Monthly Avg',
                        icon: Icons.tune_outlined,
                        iconColor: const Color(0xFFD97706),
                        backgroundColor: AppTheme.pastelAmberBg,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ReportSummaryCard(
                        title: 'Average SNF %',
                        value: '${report.collection.avgSnf.toStringAsFixed(2)}%',
                        subtitle: 'Monthly Avg',
                        icon: Icons.stacked_bar_chart,
                        iconColor: const Color(0xFF7C3AED),
                        backgroundColor: AppTheme.pastelPurpleBg,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // 2. COLLECTION TREND LINE CHART WIDGET
          ReportSection(
            title: 'Monthly Collection Trend',
            icon: Icons.show_chart,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                CollectionTrendChart(
                  dataPoints: trendPoints,
                  labels: trendLabels,
                  height: 190,
                ),
              ],
            ),
          ),

          // 3. SHIFT PERFORMANCE DONUT CHART WIDGET
          ReportSection(
            title: 'Shift Volume Distribution',
            icon: Icons.pie_chart_outline,
            child: ShiftPerformanceDonutChart(
              morningQty: morningLitres,
              eveningQty: eveningLitres,
            ),
          ),

          // 4. MONTHLY FINANCIAL OVERVIEW
          ReportSection(
            title: 'Financial Performance',
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

          // 5. TOP SHOPS PERFORMANCE
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
        ],
      ),
    );
  }
}
