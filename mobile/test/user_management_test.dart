import 'package:flutter_test/flutter_test.dart';

import 'package:mobile/core/errors/api_exception.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/users/models/user_management_model.dart';
import 'package:mobile/features/users/providers/user_provider.dart';
import 'package:mobile/features/users/repositories/user_repository.dart';

class MockUserApiClient implements ApiClient {
  Map<String, dynamic>? mockGetUsersResponse;
  Map<String, dynamic>? mockUserDetailResponse;
  Map<String, dynamic>? mockCreateUserResponse;
  Map<String, dynamic>? mockUpdateUserResponse;
  Map<String, dynamic>? mockToggleStatusResponse;

  bool shouldThrowForbidden = false;
  bool shouldThrowValidation = false;
  bool shouldThrowApiError = false;

  String? lastPath;
  String? lastMethod;
  Map<String, String>? lastQueryParams;
  Map<String, dynamic>? lastBody;

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    lastPath = path;
    lastMethod = 'GET';
    lastQueryParams = queryParameters;

    if (shouldThrowForbidden) {
      throw ForbiddenException(message: 'This action is unauthorized.');
    }
    if (shouldThrowApiError) {
      throw ApiException(message: 'Server error');
    }

    if (path.contains('/')) {
      final parts = path.split('/');
      if (parts.length > 2 && int.tryParse(parts.last) != null) {
        return mockUserDetailResponse ?? {
          'success': true,
          'data': {
            'id': int.parse(parts.last),
            'name': 'Test User',
            'email': 'test@dairy.com',
            'role': 'manager',
            'status': true,
            'created_at': '2026-08-01T10:00:00Z',
          }
        };
      }
    }

    return mockGetUsersResponse ?? {
      'success': true,
      'data': [
        {
          'id': 1,
          'name': 'Super Admin User',
          'email': 'superadmin@dairy.com',
          'role': 'super_admin',
          'status': true,
          'created_at': '2026-08-01T10:00:00Z',
        },
        {
          'id': 2,
          'name': 'Manager User',
          'email': 'manager@dairy.com',
          'role': 'manager',
          'status': true,
          'created_at': '2026-08-01T10:00:00Z',
        }
      ],
      'meta': {
        'current_page': 1,
        'last_page': 1,
        'per_page': 15,
        'total': 2,
      }
    };
  }

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    lastPath = path;
    lastMethod = 'POST';
    lastBody = body;

    if (shouldThrowForbidden) {
      throw ForbiddenException(message: 'This action is unauthorized.');
    }
    if (shouldThrowValidation) {
      throw ValidationException(
        message: 'The email has already been taken.',
        errors: {
          'email': ['The email has already been taken.']
        },
      );
    }

    return mockCreateUserResponse ?? {
      'success': true,
      'data': {
        'id': 3,
        'name': body?['name'] ?? 'New Staff',
        'email': body?['email'] ?? 'newstaff@dairy.com',
        'role': body?['role'] ?? 'collection_staff',
        'status': body?['status'] ?? true,
        'created_at': '2026-08-13T10:00:00Z',
      }
    };
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async {
    lastPath = path;
    lastMethod = 'PUT';
    lastBody = body;

    if (shouldThrowForbidden) {
      throw ForbiddenException(message: 'This action is unauthorized.');
    }
    if (shouldThrowValidation) {
      throw ValidationException(
        message: 'Cannot demote or deactivate the last active Super Admin account.',
        errors: {
          'role': ['Cannot demote or deactivate the last active Super Admin account.']
        },
      );
    }

    return mockUpdateUserResponse ?? {
      'success': true,
      'data': {
        'id': 2,
        'name': body?['name'] ?? 'Updated Manager',
        'email': body?['email'] ?? 'manager@dairy.com',
        'role': body?['role'] ?? 'manager',
        'status': body?['status'] ?? true,
        'updated_at': '2026-08-13T10:00:00Z',
      }
    };
  }

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async {
    lastPath = path;
    lastMethod = 'PATCH';

    if (shouldThrowValidation) {
      throw ValidationException(
        message: 'You cannot deactivate your own logged-in account.',
        errors: {},
      );
    }

    return mockToggleStatusResponse ?? {
      'success': true,
      'data': {
        'id': 2,
        'name': 'Manager User',
        'email': 'manager@dairy.com',
        'role': 'manager',
        'status': false,
        'updated_at': '2026-08-13T10:00:00Z',
      }
    };
  }

  @override
  Future<Map<String, dynamic>> delete(String path) async {
    lastPath = path;
    lastMethod = 'DELETE';

    if (shouldThrowForbidden) {
      throw ForbiddenException(message: 'This action is unauthorized.');
    }

    return {'success': true, 'message': 'User account deleted successfully'};
  }
}

void main() {
  final sampleUserJson = {
    'id': 1,
    'name': 'Super Admin',
    'email': 'superadmin@dairy.com',
    'role': 'super_admin',
    'status': true,
    'created_at': '2026-08-01T10:00:00Z',
    'updated_at': '2026-08-01T10:00:00Z',
  };

  group('UserModel Unit Tests', () {
    test('UserModel.fromJson parses user resource JSON correctly', () {
      final user = UserModel.fromJson(sampleUserJson);

      expect(user.id, equals(1));
      expect(user.name, equals('Super Admin'));
      expect(user.email, equals('superadmin@dairy.com'));
      expect(user.role, equals('super_admin'));
      expect(user.isSuperAdmin, isTrue);
      expect(user.isActive, isTrue);
      expect(user.roleDisplayName, equals('Super Admin'));
    });

    test('SystemRole exposes 4 system roles', () {
      expect(SystemRole.availableRoles.length, equals(4));
      expect(SystemRole.findByKey('manager').displayName, equals('Manager'));
    });
  });

  group('UserRepository Unit Tests', () {
    test('getUsers builds query parameters and returns paginated users', () async {
      final mockApi = MockUserApiClient();
      final repository = UserRepository(apiClient: mockApi);

      final result = await repository.getUsers(page: 2, search: 'Admin', role: 'super_admin', status: '1');

      expect(result['users'], isA<List<UserModel>>());
      expect(mockApi.lastQueryParams?['page'], equals('2'));
      expect(mockApi.lastQueryParams?['search'], equals('Admin'));
      expect(mockApi.lastQueryParams?['role'], equals('super_admin'));
      expect(mockApi.lastQueryParams?['status'], equals('1'));
    });

    test('createUser sends payload and returns UserModel', () async {
      final mockApi = MockUserApiClient();
      final repository = UserRepository(apiClient: mockApi);

      final payload = {
        'name': 'New Staff',
        'email': 'newstaff@dairy.com',
        'password': 'password123',
        'password_confirmation': 'password123',
        'role': 'collection_staff',
        'status': true,
      };

      final user = await repository.createUser(payload);

      expect(user.id, equals(3));
      expect(user.name, equals('New Staff'));
      expect(mockApi.lastMethod, equals('POST'));
      expect(mockApi.lastBody?['email'], equals('newstaff@dairy.com'));
    });
  });

  group('UserProvider State Tests', () {
    test('fetchUsers loads user list on success', () async {
      final mockApi = MockUserApiClient();
      final repository = UserRepository(apiClient: mockApi);
      final provider = UserProvider(repository: repository);

      expect(provider.status, equals(UserProviderStateStatus.initial));

      await provider.fetchUsers();

      expect(provider.status, equals(UserProviderStateStatus.loaded));
      expect(provider.users.length, equals(2));
    });

    test('fetchUsers sets status to forbidden when user lacks super_admin permission', () async {
      final mockApi = MockUserApiClient();
      mockApi.shouldThrowForbidden = true;

      final repository = UserRepository(apiClient: mockApi);
      final provider = UserProvider(repository: repository);

      await provider.fetchUsers();

      expect(provider.status, equals(UserProviderStateStatus.forbidden));
      expect(provider.errorMessage, contains('unauthorized'));
    });

    test('createUser captures 422 validation errors under fieldErrors', () async {
      final mockApi = MockUserApiClient();
      mockApi.shouldThrowValidation = true;

      final repository = UserRepository(apiClient: mockApi);
      final provider = UserProvider(repository: repository);

      final success = await provider.createUser({
        'name': 'Existing User',
        'email': 'duplicate@dairy.com',
      });

      expect(success, isFalse);
      expect(provider.fieldErrors.containsKey('email'), isTrue);
    });
  });
}
