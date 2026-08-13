import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/profile/providers/profile_provider.dart';
import 'package:mobile/features/profile/repositories/profile_repository.dart';

class MockProfileApiClient implements ApiClient {
  Map<String, dynamic>? mockGetResponse;
  Map<String, dynamic>? mockPutResponse;
  Map<String, dynamic>? mockPatchResponse;
  bool shouldThrow = false;

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    if (shouldThrow) throw Exception('Get Profile Failed');
    return mockGetResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrow) throw Exception('Update Profile Failed');
    return mockPutResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrow) throw Exception('Password Change Failed');
    return mockPatchResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> delete(String path) async => throw UnimplementedError();

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();
}

void main() {
  final sampleUserJson = {
    'id': 3,
    'name': 'System Super Admin',
    'email': 'superadmin@dairy.com',
    'role': 'super_admin',
    'status': true,
  };

  group('ProfileRepository Unit Tests', () {
    test('getProfile returns UserModel on success', () async {
      final mockApi = MockProfileApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': {'user': sampleUserJson},
      };

      final repo = ProfileRepository(apiClient: mockApi);
      final user = await repo.getProfile();

      expect(user.name, 'System Super Admin');
      expect(user.email, 'superadmin@dairy.com');
    });

    test('updateProfile returns updated UserModel', () async {
      final mockApi = MockProfileApiClient();
      mockApi.mockPutResponse = {
        'success': true,
        'data': {
          'user': {
            'id': 3,
            'name': 'Updated Admin',
            'email': 'updatedadmin@dairy.com',
            'role': 'super_admin',
            'status': true,
          }
        },
      };

      final repo = ProfileRepository(apiClient: mockApi);
      final updatedUser = await repo.updateProfile(name: 'Updated Admin', email: 'updatedadmin@dairy.com');

      expect(updatedUser.name, 'Updated Admin');
      expect(updatedUser.email, 'updatedadmin@dairy.com');
    });
  });

  group('ProfileProvider State Tests', () {
    test('fetchProfile loads user details', () async {
      final mockApi = MockProfileApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': {'user': sampleUserJson},
      };

      final repo = ProfileRepository(apiClient: mockApi);
      final provider = ProfileProvider(profileRepository: repo);

      await provider.fetchProfile();

      expect(provider.user, isNotNull);
      expect(provider.user?.name, 'System Super Admin');
      expect(provider.errorMessage, isNull);
    });

    test('updateProfile handles success and state transition', () async {
      final mockApi = MockProfileApiClient();
      mockApi.mockPutResponse = {
        'success': true,
        'data': {
          'user': {
            'id': 3,
            'name': 'Jane Admin',
            'email': 'jane@dairy.com',
            'role': 'manager',
            'status': true,
          }
        },
      };

      final repo = ProfileRepository(apiClient: mockApi);
      final provider = ProfileProvider(profileRepository: repo);

      final success = await provider.updateProfile(name: 'Jane Admin', email: 'jane@dairy.com');

      expect(success, isTrue);
      expect(provider.user?.name, 'Jane Admin');
      expect(provider.successMessage, 'Profile updated successfully.');
    });
  });
}
