enum AppModule {
  dashboard,
  villages,
  farmers,
  milkCollection,
  milkReceiving,
  milkStock,
  shops,
  shopOrders,
  deliveries,
  reports,
  users,
  profile,
}

class RolePermissions {
  /// Map route paths to their required AppModule
  static const Map<String, AppModule> routeModuleMap = {
    '/home': AppModule.dashboard,
    '/dashboard': AppModule.dashboard,
    '/profile': AppModule.profile,
    '/villages': AppModule.villages,
    '/villages/create': AppModule.villages,
    '/villages/detail': AppModule.villages,
    '/villages/edit': AppModule.villages,
    '/farmers': AppModule.farmers,
    '/farmers/create': AppModule.farmers,
    '/farmers/detail': AppModule.farmers,
    '/farmers/edit': AppModule.farmers,
    '/milk-collections': AppModule.milkCollection,
    '/milk-collections/create': AppModule.milkCollection,
    '/milk-collections/detail': AppModule.milkCollection,
    '/milk-collections/edit': AppModule.milkCollection,
    '/milk-receivings': AppModule.milkReceiving,
    '/milk-receivings/create': AppModule.milkReceiving,
    '/milk-receivings/detail': AppModule.milkReceiving,
    '/milk-receivings/edit': AppModule.milkReceiving,
    '/milk-stocks': AppModule.milkStock,
    '/milk-stocks/out': AppModule.milkStock,
    '/shops': AppModule.shops,
    '/shops/create': AppModule.shops,
    '/shops/detail': AppModule.shops,
    '/shops/edit': AppModule.shops,
    '/shop-orders': AppModule.shopOrders,
    '/shop-orders/create': AppModule.shopOrders,
    '/shop-orders/detail': AppModule.shopOrders,
    '/shop-orders/edit': AppModule.shopOrders,
    '/deliveries': AppModule.deliveries,
    '/deliveries/create': AppModule.deliveries,
    '/deliveries/detail': AppModule.deliveries,
    '/deliveries/update-status': AppModule.deliveries,
    '/reports': AppModule.reports,
    '/users': AppModule.users,
    '/users/create': AppModule.users,
    '/users/detail': AppModule.users,
    '/users/edit': AppModule.users,
  };

  /// Single Source of Truth for Role-based Module Access
  static bool canAccessModule(String? role, AppModule module) {
    if (role == null || role.isEmpty) return false;

    // Self-service profile is accessible by all authenticated users
    if (module == AppModule.profile || module == AppModule.dashboard) {
      return true;
    }

    switch (role) {
      case 'super_admin':
        return true;

      case 'manager':
        return module != AppModule.users;

      case 'collection_staff':
        return module == AppModule.villages ||
            module == AppModule.farmers ||
            module == AppModule.milkCollection;

      case 'center_staff':
        return module == AppModule.milkReceiving ||
            module == AppModule.milkStock ||
            module == AppModule.shops ||
            module == AppModule.shopOrders ||
            module == AppModule.deliveries;

      default:
        return false;
    }
  }

  /// Check route authorization by role
  static bool canAccessRoute(String? role, String routeName) {
    if (routeName == '/login') return true;
    final module = routeModuleMap[routeName];
    if (module == null) return true; // Unrestricted public or unrecognized route
    return canAccessModule(role, module);
  }
}
