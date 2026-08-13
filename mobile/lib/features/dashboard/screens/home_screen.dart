import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/loading_indicator.dart';
import '../../auth/providers/auth_provider.dart';
import '../providers/dashboard_provider.dart';
import '../widgets/kpi_card.dart';
import '../widgets/quick_action_card.dart';
import '../widgets/recent_activity_item.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final dashboardProvider = Provider.of<DashboardProvider>(context, listen: false);
      dashboardProvider.fetchDashboard();
    });
  }



  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final dashboardProvider = Provider.of<DashboardProvider>(context);
    final user = authProvider.user;
    final data = dashboardProvider.dashboardData;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dairy Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.account_circle_outlined),
            tooltip: 'My Profile',
            onPressed: () {
              Navigator.of(context).pushNamed(AppRoutes.profile);
            },
          ),
          IconButton(
            icon: const Icon(Icons.logout),
            tooltip: 'Logout',
            onPressed: () async {
              final navigator = Navigator.of(context);
              await authProvider.logout();
              navigator.pushNamedAndRemoveUntil(
                AppRoutes.login,
                (route) => false,
              );
            },
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await dashboardProvider.refresh();
        },
        child: SafeArea(
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Header User Profile Card
                Card(
                  elevation: 2,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.all(18.0),
                    child: Row(
                      children: [
                        InkWell(
                          onTap: () => Navigator.of(context).pushNamed(AppRoutes.profile),
                          child: CircleAvatar(
                            radius: 26,
                            backgroundColor: AppTheme.primaryColor.withValues(alpha: 0.1),
                            child: const Icon(
                              Icons.person,
                              size: 30,
                              color: AppTheme.primaryColor,
                            ),
                          ),
                        ),
                        const SizedBox(width: 14),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                user != null ? 'Welcome, ${user.name}' : 'Welcome User',
                                style: const TextStyle(
                                  fontSize: 17,
                                  fontWeight: FontWeight.bold,
                                  color: AppTheme.textPrimary,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                user?.email ?? '',
                                style: const TextStyle(
                                  fontSize: 13,
                                  color: AppTheme.textSecondary,
                                ),
                              ),
                            ],
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: AppTheme.primaryColor.withValues(alpha: 0.1),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(
                            user?.roleDisplayName ?? 'Staff',
                            style: const TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.primaryColor,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 20),

                // Error Banner with Retry
                if (dashboardProvider.hasError) ...[
                  ErrorBanner(
                    message: dashboardProvider.errorMessage ?? 'Failed to load dashboard data.',
                    onDismiss: () => dashboardProvider.fetchDashboard(),
                  ),
                  const SizedBox(height: 12),
                  Center(
                    child: ElevatedButton.icon(
                      onPressed: () => dashboardProvider.fetchDashboard(),
                      icon: const Icon(Icons.refresh),
                      label: const Text('Retry Loading Dashboard'),
                    ),
                  ),
                  const SizedBox(height: 20),
                ],

                // Loading State
                if (dashboardProvider.isLoading)
                  const SizedBox(
                    height: 220,
                    child: LoadingIndicator(message: 'Syncing live dashboard metrics...'),
                  ),

                // Live KPI Metrics Grid
                if (data != null && !dashboardProvider.isLoading) ...[
                  Row(
                    children: [
                      const Text(
                        'Today Overview',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const Spacer(),
                      Text(
                        data.today,
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w500,
                          color: AppTheme.textSecondary,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  GridView.count(
                    crossAxisCount: 2,
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    crossAxisSpacing: 12,
                    mainAxisSpacing: 12,
                    childAspectRatio: 1.5,
                    children: [
                      KpiCard(
                        title: 'Total Farmers',
                        value: data.kpis.totalFarmers.toString(),
                        subtitle: '${data.kpis.activeFarmers} Active',
                        icon: Icons.people_outline,
                        iconColor: const Color(0xFF0284C7),
                        backgroundColor: const Color(0xFFE0F2FE),
                      ),
                      KpiCard(
                        title: 'Milk Quantity',
                        value: '${data.kpis.todayQuantity.toStringAsFixed(1)} L',
                        subtitle: 'Today Total',
                        icon: Icons.water_drop_outlined,
                        iconColor: const Color(0xFF059669),
                        backgroundColor: const Color(0xFFD1FAE5),
                      ),
                      KpiCard(
                        title: 'Collection Amount',
                        value: '₹${data.kpis.todayAmount.toStringAsFixed(0)}',
                        subtitle: 'Total Value',
                        icon: Icons.currency_rupee_outlined,
                        iconColor: const Color(0xFFD97706),
                        backgroundColor: const Color(0xFFFEF3C7),
                      ),
                      KpiCard(
                        title: 'Coverage Villages',
                        value: data.kpis.totalVillages.toString(),
                        subtitle: '${data.kpis.activeVillages} Active',
                        icon: Icons.location_city_outlined,
                        iconColor: const Color(0xFF7C3AED),
                        backgroundColor: const Color(0xFFEDE9FE),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),

                  // Shift Collection Breakdown Card
                  Card(
                    elevation: 2,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    child: Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Shift Wise Collection',
                            style: TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.bold,
                              color: AppTheme.textPrimary,
                            ),
                          ),
                          const SizedBox(height: 14),
                          Row(
                            children: [
                              Expanded(
                                child: Container(
                                  padding: const EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFFFF7ED),
                                    borderRadius: BorderRadius.circular(10),
                                    border: Border.all(color: const Color(0xFFFFEDD5)),
                                  ),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Row(
                                        children: [
                                          Icon(Icons.wb_sunny_outlined, color: Color(0xFFEA580C), size: 18),
                                          SizedBox(width: 6),
                                          Text(
                                            'Morning Shift',
                                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Color(0xFF9A3412)),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 6),
                                      Text(
                                        '${data.todayOverview.morning.quantity.toStringAsFixed(1)} Litres',
                                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF9A3412)),
                                      ),
                                      Text(
                                        '${data.todayOverview.morning.farmers} Farmers',
                                        style: const TextStyle(fontSize: 11, color: Color(0xFFC2410C)),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Container(
                                  padding: const EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFF0F9FF),
                                    borderRadius: BorderRadius.circular(10),
                                    border: Border.all(color: const Color(0xFFE0F2FE)),
                                  ),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Row(
                                        children: [
                                          Icon(Icons.nights_stay_outlined, color: Color(0xFF0284C7), size: 18),
                                          SizedBox(width: 6),
                                          Text(
                                            'Evening Shift',
                                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Color(0xFF075985)),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 6),
                                      Text(
                                        '${data.todayOverview.evening.quantity.toStringAsFixed(1)} Litres',
                                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF075985)),
                                      ),
                                      Text(
                                        '${data.todayOverview.evening.farmers} Farmers',
                                        style: const TextStyle(fontSize: 11, color: Color(0xFF0369A1)),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 24),
                ],

                // Quick Action Center
                const Text(
                  'Quick Actions',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: AppTheme.textPrimary,
                  ),
                ),
                const SizedBox(height: 12),
                GridView.count(
                  crossAxisCount: 3,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  crossAxisSpacing: 10,
                  mainAxisSpacing: 10,
                  childAspectRatio: 1.1,
                  children: [
                    QuickActionCard(
                      label: 'Collect Milk',
                      icon: Icons.add_circle_outline,
                      color: const Color(0xFF059669),
                      onTap: () => Navigator.of(context).pushNamed(AppRoutes.milkCollections),
                    ),
                    QuickActionCard(
                      label: 'Milk Receiving',
                      icon: Icons.unarchive_outlined,
                      color: const Color(0xFF0D9488),
                      onTap: () => Navigator.of(context).pushNamed(AppRoutes.milkReceivings),
                    ),
                    QuickActionCard(
                      label: 'Milk Stock',
                      icon: Icons.inventory_2_outlined,
                      color: const Color(0xFF10B981),
                      onTap: () => Navigator.of(context).pushNamed(AppRoutes.milkStocks),
                    ),
                    QuickActionCard(
                      label: 'Farmers',
                      icon: Icons.person_search_outlined,
                      color: const Color(0xFF0284C7),
                      onTap: () => Navigator.of(context).pushNamed(AppRoutes.farmers),
                    ),
                    QuickActionCard(
                      label: 'Villages',
                      icon: Icons.location_city_outlined,
                      color: const Color(0xFF7C3AED),
                      onTap: () => Navigator.of(context).pushNamed(AppRoutes.villages),
                    ),
                    QuickActionCard(
                      label: 'Shops',
                      icon: Icons.storefront_outlined,
                      color: const Color(0xFFD97706),
                      onTap: () => Navigator.of(context).pushNamed(AppRoutes.shops),
                    ),
                    QuickActionCard(
                      label: 'Shop Orders',
                      icon: Icons.shopping_bag_outlined,
                      color: const Color(0xFFEA580C),
                      onTap: () => Navigator.of(context).pushNamed(AppRoutes.shopOrders),
                    ),
                    QuickActionCard(
                      label: 'Deliveries',
                      icon: Icons.local_shipping_outlined,
                      color: const Color(0xFF0284C7),
                      onTap: () => Navigator.of(context).pushNamed(AppRoutes.deliveries),
                    ),
                    QuickActionCard(
                      label: 'Reports',
                      icon: Icons.bar_chart_outlined,
                      color: const Color(0xFF059669),
                      onTap: () => Navigator.of(context).pushNamed(AppRoutes.reports),
                    ),
                    if (authProvider.user?.isSuperAdmin == true)
                      QuickActionCard(
                        label: 'User Mgmt',
                        icon: Icons.manage_accounts_outlined,
                        color: const Color(0xFF7E22CE),
                        onTap: () => Navigator.of(context).pushNamed(AppRoutes.users),
                      ),
                    QuickActionCard(
                      label: 'My Profile',
                      icon: Icons.account_circle_outlined,
                      color: const Color(0xFF4F46E5),
                      onTap: () => Navigator.of(context).pushNamed(AppRoutes.profile),
                    ),
                  ],
                ),
                const SizedBox(height: 24),

                // Recent Activity Feed
                if (data != null && data.recentActivity.isNotEmpty) ...[
                  const Text(
                    'Recent Activity',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 10),
                  Card(
                    elevation: 2,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    child: Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        children: data.recentActivity
                            .take(6)
                            .map((item) => RecentActivityItem(item: item))
                            .toList(),
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }
}
