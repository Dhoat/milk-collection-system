import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/core/storage/secure_token_storage.dart';
import 'package:mobile/core/storage/token_storage_interface.dart';
import 'package:mobile/features/auth/repositories/auth_repository.dart';

class MockApiClient implements ApiClient {
  Map<String, dynamic>? mockResponse;
  Object? errorToThrow;

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    if (errorToThrow != null) throw errorToThrow!;
    return mockResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    if (errorToThrow != null) throw errorToThrow!;
    return mockResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> delete(String path) async => mockResponse ?? {};

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async => mockResponse ?? {};

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async => mockResponse ?? {};
}

void main() {
  group('AuthRepository Unit Tests', () {
    late MockApiClient mockApiClient;
    late ITokenStorage tokenStorage;
    late AuthRepository authRepository;

    setUp(() {
      mockApiClient = MockApiClient();
      tokenStorage = InMemoryTokenStorage();
      authRepository = AuthRepository(
        apiClient: mockApiClient,
        tokenStorage: tokenStorage,
      );
    });

    test('login should save token and return UserModel on success', () async {
      mockApiClient.mockResponse = {
        'success': true,
        'message': 'Login successful',
        'data': {
          'token': '1|mock_sanctum_token',
          'user': {
            'id': 1,
            'name': 'Manager User',
            'email': 'manager@dairy.com',
            'role': 'manager',
            'status': true,
          },
        },
      };

      final user = await authRepository.login(
        email: 'manager@dairy.com',
        password: 'password',
      );

      expect(user.id, equals(1));
      expect(user.email, equals('manager@dairy.com'));
      expect(user.role, equals('manager'));

      final savedToken = await tokenStorage.getToken();
      expect(savedToken, equals('1|mock_sanctum_token'));
    });

    test('getCurrentUser should return user profile', () async {
      mockApiClient.mockResponse = {
        'success': true,
        'data': {
          'user': {
            'id': 2,
            'name': 'Staff Member',
            'email': 'staff@dairy.com',
            'role': 'center_staff',
            'status': true,
          },
        },
      };

      final user = await authRepository.getCurrentUser();

      expect(user.id, equals(2));
      expect(user.role, equals('center_staff'));
    });

    test('logout should clear token from storage', () async {
      await tokenStorage.saveToken('1|old_token');
      mockApiClient.mockResponse = {'success': true, 'message': 'Logged out'};

      await authRepository.logout();

      final token = await tokenStorage.getToken();
      expect(token, isNull);
    });
  });
}
