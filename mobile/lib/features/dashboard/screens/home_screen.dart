import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/permissions/role_permissions.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_banner.dart';
import '../../../core/widgets/role_bottom_nav.dart';
import '../../../core/widgets/skeleton_loader.dart';
import '../../../core/widgets/stat_card.dart';
import '../../auth/providers/auth_provider.dart';
import '../providers/dashboard_provider.dart';
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

  void _confirmLogout(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Logout?'),
        content: const Text('Are you sure you want to log out of Dairy Management?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.of(ctx).pop();
              final authProvider = Provider.of<AuthProvider>(context, listen: false);
              await authProvider.logout();
              if (context.mounted) {
                Navigator.of(context).pushNamedAndRemoveUntil(AppRoutes.login, (route) => false);
              }
            },
            child: const Text('Logout', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final dashboardProvider = Provider.of<DashboardProvider>(context);
    final user = authProvider.user;
    final data = dashboardProvider.dashboardData;

    return Scaffold(
      backgroundColor: AppTheme.backgroundColor,
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: () async {
            await dashboardProvider.refresh();
          },
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Top Modern Green Dashboard Header matching reference image
                Container(
                  padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
                  decoration: const BoxDecoration(
                    color: AppTheme.primaryColor,
                    borderRadius: BorderRadius.only(
                      bottomLeft: Radius.circular(24),
                      bottomRight: Radius.circular(24),
                    ),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    ClipRRect(
                                      borderRadius: BorderRadius.circular(6),
                                      child: Image.asset(
                                        'assets/images/app_logo.png',
                                        width: 18,
                                        height: 18,
                                        errorBuilder: (context, error, stackTrace) => const Icon(Icons.pets, size: 18, color: Colors.white),
                                      ),
                                    ),
                                    const SizedBox(width: 6),
                                    const Text(
                                      'Dairy Management',
                                      style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.white70,
                                        letterSpacing: 0.3,
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 6),
                                Row(
                                  children: [
                                    Flexible(
                                      child: Text(
                                        'Good Morning, 👋',
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        style: const TextStyle(
                                          fontSize: 14,
                                          color: Colors.white70,
                                          fontWeight: FontWeight.w500,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  user?.name ?? 'Field Collection Staff',
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.white,
                                  ),
                                ),
                                const SizedBox(height: 6),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                                  decoration: BoxDecoration(
                                    color: Colors.white.withValues(alpha: 0.2),
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: Text(
                                    user?.roleDisplayName ?? 'Collection Staff',
                                    style: const TextStyle(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w600,
                                      color: Colors.white,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          Row(
                            children: [
                              IconButton(
                                icon: const Icon(Icons.notifications_outlined, color: Colors.white),
                                tooltip: 'Notifications',
                                onPressed: () {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(content: Text('No new notifications.')),
                                  );
                                },
                              ),
                              IconButton(
                                icon: const Icon(Icons.logout_rounded, color: Colors.white),
                                tooltip: 'Logout',
                                onPressed: () => _confirmLogout(context),
                              ),
                              InkWell(
                                onTap: () => Navigator.of(context).pushNamed(AppRoutes.profile),
                                borderRadius: BorderRadius.circular(20),
                                child: CircleAvatar(
                                  radius: 20,
                                  backgroundColor: Colors.white,
                                  child: Text(
                                    user?.name.substring(0, 1).toUpperCase() ?? 'U',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                      color: AppTheme.primaryColor,
                                      fontSize: 16,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),

                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Date Selector Header Chip
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(color: const Color(0xFFE2E8F0)),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                const Icon(Icons.calendar_today_outlined, size: 14, color: AppTheme.primaryColor),
                                const SizedBox(width: 6),
                                Text(
                                  data != null ? 'Today, ${data.today}' : 'Today',
                                  style: const TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.w600,
                                    color: AppTheme.textPrimary,
                                  ),
                                ),
                                const Icon(Icons.arrow_drop_down, size: 18, color: AppTheme.textSecondary),
                              ],
                            ),
                          ),
                          const Spacer(),
                          IconButton(
                            icon: const Icon(Icons.refresh, size: 20, color: AppTheme.textSecondary),
                            onPressed: () => dashboardProvider.refresh(),
                            tooltip: 'Refresh metrics',
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),

                      // Error Notification Banner
                      if (dashboardProvider.hasError) ...[
                        ErrorBanner(
                          message: dashboardProvider.errorMessage ?? 'Failed to load live metrics.',
                          onDismiss: () => dashboardProvider.fetchDashboard(),
                        ),
                        const SizedBox(height: 16),
                      ],

                      // Loading Skeleton State
                      if (dashboardProvider.isLoading) ...[
                        const Row(
                          children: [
                            Expanded(child: SkeletonLoader(height: 110)),
                            SizedBox(width: 12),
                            Expanded(child: SkeletonLoader(height: 110)),
                          ],
                        ),
                        const SizedBox(height: 12),
                        const Row(
                          children: [
                            Expanded(child: SkeletonLoader(height: 110)),
                            SizedBox(width: 12),
                            Expanded(child: SkeletonLoader(height: 110)),
                          ],
                        ),
                        const SizedBox(height: 20),
                      ],

                      // Today Overview KPI Grid matching reference design
                      if (data != null && !dashboardProvider.isLoading) ...[
                        const Text(
                          "Today's Overview",
                          style: TextStyle(
                            fontSize: 17,
                            fontWeight: FontWeight.bold,
                            color: AppTheme.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 12),

                        LayoutBuilder(
                          builder: (context, constraints) {
                            final double width = constraints.maxWidth;
                            final int crossAxisCount = width >= 800 ? 4 : 2;
                            final double aspectRatio = width < 360 ? 1.1 : 1.25;

                            return GridView.count(
                              crossAxisCount: crossAxisCount,
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              crossAxisSpacing: 12,
                              mainAxisSpacing: 12,
                              childAspectRatio: aspectRatio,
                              children: [
                                StatCard(
                                  title: 'Milk Collected',
                                  value: '${data.kpis.todayQuantity.toStringAsFixed(0)} L',
                                  icon: Icons.water_drop_outlined,
                                  iconColor: AppTheme.primaryColor,
                                  backgroundColor: AppTheme.pastelGreenBg,
                                  trendText: '↑ 8.4% vs yesterday',
                                  isTrendPositive: true,
                                ),
                                StatCard(
                                  title: 'Farmers',
                                  value: '${data.kpis.totalFarmers}',
                                  icon: Icons.people_outline,
                                  iconColor: const Color(0xFF0284C7),
                                  backgroundColor: AppTheme.pastelBlueBg,
                                  trendText: '↑ ${data.kpis.activeFarmers} active',
                                  isTrendPositive: true,
                                ),
                                StatCard(
                                  title: 'Collection Amount',
                                  value: '₹ ${data.kpis.todayAmount.toStringAsFixed(0)}',
                                  icon: Icons.currency_rupee_outlined,
                                  iconColor: const Color(0xFFD97706),
                                  backgroundColor: AppTheme.pastelAmberBg,
                                  trendText: '↑ 6.2% vs yesterday',
                                  isTrendPositive: true,
                                ),
                                StatCard(
                                  title: 'Villages Covered',
                                  value: '${data.kpis.totalVillages}',
                                  icon: Icons.location_city_outlined,
                                  iconColor: const Color(0xFF7C3AED),
                                  backgroundColor: AppTheme.pastelPurpleBg,
                                  subtitle: 'Tap to manage',
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.villages),
                                ),
                              ],
                            );
                          },
                        ),
                        const SizedBox(height: 20),

                        // Milk Collection (Today) Shift Card matching reference image
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: const Color(0xFFF1F5F9)),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Milk Collection (Today)',
                                style: TextStyle(
                                  fontSize: 15,
                                  fontWeight: FontWeight.bold,
                                  color: AppTheme.textPrimary,
                                ),
                              ),
                              const SizedBox(height: 16),
                              Row(
                                children: [
                                  // Morning Shift Column
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const Row(
                                          children: [
                                            Icon(Icons.wb_sunny_outlined, color: Color(0xFFEA580C), size: 16),
                                            SizedBox(width: 4),
                                            Expanded(
                                              child: Text(
                                                'Morning Shift',
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                                style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppTheme.textSecondary),
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 6),
                                        Text(
                                          '${data.todayOverview.morning.quantity.toStringAsFixed(0)} L',
                                          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                                        ),
                                        const SizedBox(height: 6),
                                        ClipRRect(
                                          borderRadius: BorderRadius.circular(4),
                                          child: LinearProgressIndicator(
                                            value: (data.todayOverview.morning.quantity + data.todayOverview.evening.quantity) > 0
                                                ? data.todayOverview.morning.quantity / (data.todayOverview.morning.quantity + data.todayOverview.evening.quantity)
                                                : 0.5,
                                            backgroundColor: const Color(0xFFFED7AA),
                                            color: const Color(0xFFEA580C),
                                            minHeight: 6,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  const SizedBox(width: 24),

                                  // Evening Shift Column
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const Row(
                                          children: [
                                            Icon(Icons.nights_stay_outlined, color: Color(0xFF0284C7), size: 16),
                                            SizedBox(width: 4),
                                            Expanded(
                                              child: Text(
                                                'Evening Shift',
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                                style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppTheme.textSecondary),
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 6),
                                        Text(
                                          '${data.todayOverview.evening.quantity.toStringAsFixed(0)} L',
                                          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                                        ),
                                        const SizedBox(height: 6),
                                        ClipRRect(
                                          borderRadius: BorderRadius.circular(4),
                                          child: LinearProgressIndicator(
                                            value: (data.todayOverview.morning.quantity + data.todayOverview.evening.quantity) > 0
                                                ? data.todayOverview.evening.quantity / (data.todayOverview.morning.quantity + data.todayOverview.evening.quantity)
                                                : 0.5,
                                            backgroundColor: const Color(0xFFBAE6FD),
                                            color: const Color(0xFF0284C7),
                                            minHeight: 6,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 20),
                      ],

                      // Quick Actions Section matching reference image
                      const Text(
                        'Quick Actions',
                        style: TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 12),
                      LayoutBuilder(
                        builder: (context, constraints) {
                          final double width = constraints.maxWidth;
                          final int crossAxisCount = width >= 800 ? 5 : (width >= 500 ? 4 : 3);
                          final double aspectRatio = width < 360 ? 0.95 : 1.05;

                          return GridView.count(
                            crossAxisCount: crossAxisCount,
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            crossAxisSpacing: 10,
                            mainAxisSpacing: 10,
                            childAspectRatio: aspectRatio,
                            children: [
                              if (RolePermissions.canAccessModule(user?.role, AppModule.milkCollection))
                                QuickActionCard(
                                  label: 'Collect Milk',
                                  icon: Icons.add_circle_outline,
                                  color: AppTheme.primaryColor,
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.milkCollectionCreate),
                                ),
                              if (RolePermissions.canAccessModule(user?.role, AppModule.farmers))
                                QuickActionCard(
                                  label: 'Add Farmer',
                                  icon: Icons.person_add_alt_outlined,
                                  color: const Color(0xFF0284C7),
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.farmerCreate),
                                ),
                              if (RolePermissions.canAccessModule(user?.role, AppModule.villages))
                                QuickActionCard(
                                  label: 'Villages',
                                  icon: Icons.location_city_outlined,
                                  color: const Color(0xFF6366F1),
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.villages),
                                ),
                              if (RolePermissions.canAccessModule(user?.role, AppModule.reports))
                                QuickActionCard(
                                  label: 'View Reports',
                                  icon: Icons.bar_chart_outlined,
                                  color: const Color(0xFF7C3AED),
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.reports),
                                ),
                              if (RolePermissions.canAccessModule(user?.role, AppModule.milkReceiving))
                                QuickActionCard(
                                  label: 'Milk Receiving',
                                  icon: Icons.unarchive_outlined,
                                  color: const Color(0xFF0D9488),
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.milkReceivings),
                                ),
                              if (RolePermissions.canAccessModule(user?.role, AppModule.milkStock))
                                QuickActionCard(
                                  label: 'Milk Stock',
                                  icon: Icons.inventory_2_outlined,
                                  color: const Color(0xFF10B981),
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.milkStocks),
                                ),
                              if (RolePermissions.canAccessModule(user?.role, AppModule.shops))
                                QuickActionCard(
                                  label: 'Shops',
                                  icon: Icons.storefront_outlined,
                                  color: const Color(0xFFD97706),
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.shops),
                                ),
                              if (RolePermissions.canAccessModule(user?.role, AppModule.shopOrders))
                                QuickActionCard(
                                  label: 'Shop Orders',
                                  icon: Icons.shopping_bag_outlined,
                                  color: const Color(0xFFEA580C),
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.shopOrders),
                                ),
                              if (RolePermissions.canAccessModule(user?.role, AppModule.deliveries))
                                QuickActionCard(
                                  label: 'Deliveries',
                                  icon: Icons.local_shipping_outlined,
                                  color: const Color(0xFF0284C7),
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.deliveries),
                                ),
                              if (RolePermissions.canAccessModule(user?.role, AppModule.users))
                                QuickActionCard(
                                  label: 'User Mgmt',
                                  icon: Icons.manage_accounts_outlined,
                                  color: const Color(0xFF7E22CE),
                                  onTap: () => Navigator.of(context).pushNamed(AppRoutes.users),
                                ),
                            ],
                          );
                        },
                      ),
                      const SizedBox(height: 20),

                      // Recent Activity Feed
                      if (data != null && data.recentActivity.isNotEmpty) ...[
                        const Text(
                          'Recent Activity',
                          style: TextStyle(
                            fontSize: 17,
                            fontWeight: FontWeight.bold,
                            color: AppTheme.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 10),
                        Card(
                          elevation: 0,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                          child: Padding(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              children: data.recentActivity
                                  .take(5)
                                  .map((item) => RecentActivityItem(item: item))
                                  .toList(),
                            ),
                          ),
                        ),
                        const SizedBox(height: 24),
                      ],
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
      bottomNavigationBar: RoleBottomNav(
        currentRoute: AppRoutes.home,
        userRole: user?.role,
      ),
    );
  }
}
