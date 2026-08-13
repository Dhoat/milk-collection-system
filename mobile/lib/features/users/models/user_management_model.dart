export '../../auth/models/user_model.dart';

class SystemRole {
  final String key;
  final String displayName;
  final String description;

  const SystemRole({
    required this.key,
    required this.displayName,
    required this.description,
  });

  static const List<SystemRole> availableRoles = [
    SystemRole(
      key: 'super_admin',
      displayName: 'Super Admin',
      description: 'Full system control, user management, and executive access',
    ),
    SystemRole(
      key: 'manager',
      displayName: 'Manager',
      description: 'Operations, inventory, collections, sales, and reports access',
    ),
    SystemRole(
      key: 'collection_staff',
      displayName: 'Collection Staff',
      description: 'Field milk collections, farmer onboarding, and village data',
    ),
    SystemRole(
      key: 'center_staff',
      displayName: 'Center Staff',
      description: 'Main center milk intake, stock management, shop orders, and deliveries',
    ),
  ];

  static SystemRole findByKey(String key) {
    return availableRoles.firstWhere(
      (r) => r.key == key,
      orElse: () => SystemRole(key: key, displayName: key, description: ''),
    );
  }
}
