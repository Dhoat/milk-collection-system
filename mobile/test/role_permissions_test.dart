import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/app/routes/app_routes.dart';
import 'package:mobile/core/permissions/role_permissions.dart';

void main() {
  group('RolePermissions Unit Tests', () {
    test('SUPER ADMIN should access all 11 modules', () {
      const role = 'super_admin';
      expect(RolePermissions.canAccessModule(role, AppModule.dashboard), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.villages), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.farmers), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.milkCollection), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.milkReceiving), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.milkStock), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.shops), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.shopOrders), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.deliveries), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.reports), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.users), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.profile), isTrue);
    });

    test('MANAGER should access 10 modules and be denied User Management', () {
      const role = 'manager';
      expect(RolePermissions.canAccessModule(role, AppModule.dashboard), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.villages), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.farmers), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.milkCollection), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.milkReceiving), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.milkStock), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.shops), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.shopOrders), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.deliveries), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.reports), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.profile), isTrue);

      // HIDDEN / DENIED
      expect(RolePermissions.canAccessModule(role, AppModule.users), isFalse);
    });

    test('COLLECTION STAFF should access only 4 modules (Dashboard, Villages, Farmers, Milk Collection, Profile)', () {
      const role = 'collection_staff';
      expect(RolePermissions.canAccessModule(role, AppModule.dashboard), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.villages), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.farmers), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.milkCollection), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.profile), isTrue);

      // HIDDEN / DENIED
      expect(RolePermissions.canAccessModule(role, AppModule.milkReceiving), isFalse);
      expect(RolePermissions.canAccessModule(role, AppModule.milkStock), isFalse);
      expect(RolePermissions.canAccessModule(role, AppModule.shops), isFalse);
      expect(RolePermissions.canAccessModule(role, AppModule.shopOrders), isFalse);
      expect(RolePermissions.canAccessModule(role, AppModule.deliveries), isFalse);
      expect(RolePermissions.canAccessModule(role, AppModule.reports), isFalse);
      expect(RolePermissions.canAccessModule(role, AppModule.users), isFalse);
    });

    test('CENTER STAFF should access only 6 modules (Dashboard, Milk Receiving, Stock, Shops, Orders, Deliveries, Profile)', () {
      const role = 'center_staff';
      expect(RolePermissions.canAccessModule(role, AppModule.dashboard), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.milkReceiving), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.milkStock), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.shops), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.shopOrders), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.deliveries), isTrue);
      expect(RolePermissions.canAccessModule(role, AppModule.profile), isTrue);

      // HIDDEN / DENIED
      expect(RolePermissions.canAccessModule(role, AppModule.villages), isFalse);
      expect(RolePermissions.canAccessModule(role, AppModule.farmers), isFalse);
      expect(RolePermissions.canAccessModule(role, AppModule.milkCollection), isFalse);
      expect(RolePermissions.canAccessModule(role, AppModule.reports), isFalse);
      expect(RolePermissions.canAccessModule(role, AppModule.users), isFalse);
    });

    test('Route Guard rules block unauthorized routes by role', () {
      expect(RolePermissions.canAccessRoute('collection_staff', AppRoutes.reports), isFalse);
      expect(RolePermissions.canAccessRoute('collection_staff', AppRoutes.users), isFalse);
      expect(RolePermissions.canAccessRoute('collection_staff', AppRoutes.shops), isFalse);

      expect(RolePermissions.canAccessRoute('center_staff', AppRoutes.farmers), isFalse);
      expect(RolePermissions.canAccessRoute('center_staff', AppRoutes.villages), isFalse);
      expect(RolePermissions.canAccessRoute('center_staff', AppRoutes.milkCollections), isFalse);

      expect(RolePermissions.canAccessRoute('manager', AppRoutes.users), isFalse);
      expect(RolePermissions.canAccessRoute('super_admin', AppRoutes.users), isTrue);
    });
  });
}
