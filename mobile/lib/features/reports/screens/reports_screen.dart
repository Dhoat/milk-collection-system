import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../providers/report_provider.dart';
import '../widgets/report_filter_bar.dart';
import 'daily_report_screen.dart';
import 'monthly_report_screen.dart';

class ReportsScreen extends StatefulWidget {
  const ReportsScreen({super.key});

  @override
  State<ReportsScreen> createState() => _ReportsScreenState();
}

class _ReportsScreenState extends State<ReportsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<ReportProvider>(context, listen: false);
      provider.fetchReport();
    });
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<ReportProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Reports & Analytics'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => provider.fetchReport(refresh: true),
            tooltip: 'Refresh Report',
          ),
        ],
      ),
      body: Column(
        children: [
          const ReportFilterBar(),
          Expanded(
            child: _buildReportBody(provider),
          ),
        ],
      ),
    );
  }

  Widget _buildReportBody(ReportProvider provider) {
    if (provider.status == ReportStateStatus.loading && provider.dailyReport == null && provider.monthlyReport == null) {
      return const LoadingIndicator(message: 'Generating report statistics...');
    }

    if (provider.status == ReportStateStatus.forbidden) {
      return Padding(
        padding: const EdgeInsets.all(24.0),
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.amber.shade50,
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.lock_outline, size: 64, color: Colors.amber.shade800),
              ),
              const SizedBox(height: 20),
              const Text(
                'Access Restricted',
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                  color: AppTheme.textPrimary,
                ),
              ),
              const SizedBox(height: 10),
              Text(
                provider.errorMessage ?? 'Reports and financial analytics are accessible to Super Admins and Managers only.',
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 14, color: AppTheme.textSecondary),
              ),
            ],
          ),
        ),
      );
    }

    if (provider.status == ReportStateStatus.error && provider.dailyReport == null && provider.monthlyReport == null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              ErrorBanner(
                message: provider.errorMessage ?? 'Failed to load report data',
              ),
              const SizedBox(height: 16),
              ElevatedButton.icon(
                onPressed: () => provider.fetchReport(refresh: true),
                icon: const Icon(Icons.refresh),
                label: const Text('Retry Report'),
              ),
            ],
          ),
        ),
      );
    }

    final isDaily = provider.mode == ReportMode.daily;

    return RefreshIndicator(
      onRefresh: () => provider.fetchReport(refresh: true),
      child: isDaily
          ? (provider.dailyReport != null
              ? DailyReportScreen(report: provider.dailyReport!)
              : const Center(child: Text('No daily report data available.')))
          : (provider.monthlyReport != null
              ? MonthlyReportScreen(report: provider.monthlyReport!)
              : const Center(child: Text('No monthly report data available.'))),
    );
  }
}
