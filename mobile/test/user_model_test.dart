import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/features/auth/models/user_model.dart';

void main() {
  group('UserModel Unit Tests', () {
    final sampleUserJson = {
      'id': 1,
      'name': 'Super Admin User',
      'email': 'admin@dairy.com',
      'role': 'super_admin',
      'status': true,
      'email_verified_at': '2026-08-01T10:00:00.000000Z',
      'created_at': '2026-08-01T08:30:00.000000Z',
      'updated_at': '2026-08-13T09:00:00.000000Z',
    };

    test('should correctly parse UserResource JSON structure', () {
      final user = UserModel.fromJson(sampleUserJson);

      expect(user.id, equals(1));
      expect(user.name, equals('Super Admin User'));
      expect(user.email, equals('admin@dairy.com'));
      expect(user.role, equals('super_admin'));
      expect(user.status, isTrue);
      expect(user.isSuperAdmin, isTrue);
      expect(user.isManager, isFalse);
      expect(user.roleDisplayName, equals('Super Admin'));
    });

    test('should correctly convert UserModel back to JSON', () {
      final user = UserModel.fromJson(sampleUserJson);
      final json = user.toJson();

      expect(json['id'], equals(1));
      expect(json['name'], equals('Super Admin User'));
      expect(json['email'], equals('admin@dairy.com'));
      expect(json['role'], equals('super_admin'));
      expect(json['status'], isTrue);
    });

    test('should return correct role display names for all roles', () {
      final manager = UserModel.fromJson({...sampleUserJson, 'role': 'manager'});
      final collectionStaff = UserModel.fromJson({...sampleUserJson, 'role': 'collection_staff'});
      final centerStaff = UserModel.fromJson({...sampleUserJson, 'role': 'center_staff'});

      expect(manager.isManager, isTrue);
      expect(manager.roleDisplayName, equals('Manager'));

      expect(collectionStaff.isCollectionStaff, isTrue);
      expect(collectionStaff.roleDisplayName, equals('Collection Staff'));

      expect(centerStaff.isCenterStaff, isTrue);
      expect(centerStaff.roleDisplayName, equals('Center Staff'));
    });
  });
}
