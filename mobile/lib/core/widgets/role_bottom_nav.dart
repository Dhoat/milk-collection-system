import 'package:flutter/material.dart';
import '../../app/routes/app_routes.dart';
import '../permissions/role_permissions.dart';
import '../theme/app_theme.dart';

class RoleBottomNavItem {
  final String label;
  final IconData icon;
  final IconData activeIcon;
  final String routeName;
  final AppModule module;

  const RoleBottomNavItem({
    required this.label,
    required this.icon,
    required this.activeIcon,
    required this.routeName,
    required this.module,
  });
}

class RoleBottomNav extends StatelessWidget {
  final String currentRoute;
  final String? userRole;

  const RoleBottomNav({
    super.key,
    required this.currentRoute,
    required this.userRole,
  });

  static List<RoleBottomNavItem> _getAllNavItems(String? role) {
    // Basic items available based on role
    final List<RoleBottomNavItem> items = [];

    items.add(const RoleBottomNavItem(
      label: 'Home',
      icon: Icons.grid_view,
      activeIcon: Icons.grid_view_rounded,
      routeName: AppRoutes.home,
      module: AppModule.dashboard,
    ));

    if (RolePermissions.canAccessModule(role, AppModule.milkCollection)) {
      items.add(const RoleBottomNavItem(
        label: 'Collections',
        icon: Icons.water_drop_outlined,
        activeIcon: Icons.water_drop,
        routeName: AppRoutes.milkCollections,
        module: AppModule.milkCollection,
      ));
    }

    if (RolePermissions.canAccessModule(role, AppModule.milkReceiving)) {
      items.add(const RoleBottomNavItem(
        label: 'Receiving',
        icon: Icons.unarchive_outlined,
        activeIcon: Icons.unarchive,
        routeName: AppRoutes.milkReceivings,
        module: AppModule.milkReceiving,
      ));
    }

    if (RolePermissions.canAccessModule(role, AppModule.farmers)) {
      items.add(const RoleBottomNavItem(
        label: 'Farmers',
        icon: Icons.people_outline,
        activeIcon: Icons.people,
        routeName: AppRoutes.farmers,
        module: AppModule.farmers,
      ));
    }

    if (RolePermissions.canAccessModule(role, AppModule.villages)) {
      items.add(const RoleBottomNavItem(
        label: 'Villages',
        icon: Icons.location_city_outlined,
        activeIcon: Icons.location_city,
        routeName: AppRoutes.villages,
        module: AppModule.villages,
      ));
    }

    if (RolePermissions.canAccessModule(role, AppModule.reports)) {
      items.add(const RoleBottomNavItem(
        label: 'Reports',
        icon: Icons.bar_chart_outlined,
        activeIcon: Icons.bar_chart,
        routeName: AppRoutes.reports,
        module: AppModule.reports,
      ));
    }

    items.add(const RoleBottomNavItem(
      label: 'More',
      icon: Icons.more_horiz_outlined,
      activeIcon: Icons.more_horiz,
      routeName: AppRoutes.profile,
      module: AppModule.profile,
    ));

    return items;
  }

  @override
  Widget build(BuildContext context) {
    final navItems = _getAllNavItems(userRole);

    int selectedIndex = navItems.indexWhere((item) => item.routeName == currentRoute);
    if (selectedIndex == -1) selectedIndex = 0;

    return Container(
      decoration: BoxDecoration(
        color: AppTheme.surfaceColor,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 10,
            offset: const Offset(0, -2),
          ),
        ],
      ),
      child: SafeArea(
        child: Container(
          height: 60,
          padding: const EdgeInsets.symmetric(horizontal: 8),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: List.generate(navItems.length, (index) {
              final item = navItems[index];
              final isSelected = index == selectedIndex;

              return Expanded(
                child: InkWell(
                  onTap: () {
                    if (item.routeName != currentRoute) {
                      Navigator.of(context).pushReplacementNamed(item.routeName);
                    }
                  },
                  borderRadius: BorderRadius.circular(12),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 2, vertical: 6),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          isSelected ? item.activeIcon : item.icon,
                          color: isSelected ? AppTheme.primaryColor : AppTheme.textMuted,
                          size: 20,
                        ),
                        const SizedBox(height: 2),
                        FittedBox(
                          fit: BoxFit.scaleDown,
                          child: Text(
                            item.label,
                            maxLines: 1,
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
                              color: isSelected ? AppTheme.primaryColor : AppTheme.textSecondary,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            }),
          ),
        ),
      ),
    );
  }
}
