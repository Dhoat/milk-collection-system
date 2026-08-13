import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/errors/api_exception.dart';
import 'package:mobile/core/storage/secure_token_storage.dart';
import 'package:mobile/core/storage/token_storage_interface.dart';
import 'package:mobile/features/auth/models/user_model.dart';
import 'package:mobile/features/auth/providers/auth_provider.dart';
import 'package:mobile/features/auth/repositories/auth_repository_interface.dart';

class MockAuthRepository implements IAuthRepository {
  UserModel? mockUser;
  Object? errorToThrow;

  @override
  Future<UserModel> getCurrentUser() async {
    if (errorToThrow != null) throw errorToThrow!;
    return mockUser ??
        const UserModel(
          id: 1,
          name: 'Test User',
          email: 'test@dairy.com',
          role: 'super_admin',
          status: true,
        );
  }

  @override
  Future<UserModel> login({required String email, required String password}) async {
    if (errorToThrow != null) throw errorToThrow!;
    return mockUser ??
        UserModel(
          id: 1,
          name: 'Test User',
          email: email,
          role: 'super_admin',
          status: true,
        );
  }

  @override
  Future<void> logout() async {}
}

void main() {
  group('AuthProvider State Management Tests', () {
    late MockAuthRepository mockAuthRepo;
    late ITokenStorage tokenStorage;
    late AuthProvider authProvider;

    setUp(() {
      mockAuthRepo = MockAuthRepository();
      tokenStorage = InMemoryTokenStorage();
      authProvider = AuthProvider(
        authRepository: mockAuthRepo,
        tokenStorage: tokenStorage,
      );
    });

    test('initialize should set unauthenticated when no token exists', () async {
      await authProvider.initialize();

      expect(authProvider.status, equals(AuthStatus.unauthenticated));
      expect(authProvider.user, isNull);
      expect(authProvider.isAuthenticated, isFalse);
    });

    test('initialize should authenticate user when token is valid', () async {
      await tokenStorage.saveToken('1|valid_token');
      await authProvider.initialize();

      expect(authProvider.status, equals(AuthStatus.authenticated));
      expect(authProvider.user, isNotNull);
      expect(authProvider.isAuthenticated, isTrue);
    });

    test('login success should transition state to authenticated', () async {
      final result = await authProvider.login(
        email: 'admin@dairy.com',
        password: 'password123',
      );

      expect(result, isTrue);
      expect(authProvider.status, equals(AuthStatus.authenticated));
      expect(authProvider.user?.email, equals('admin@dairy.com'));
      expect(authProvider.errorMessage, isNull);
    });

    test('login validation error should capture field errors', () async {
      mockAuthRepo.errorToThrow = ValidationException(
        message: 'Validation failed',
        errors: {
          'email': ['These credentials do not match our records.'],
        },
      );

      final result = await authProvider.login(
        email: 'wrong@dairy.com',
        password: 'invalidpassword',
      );

      expect(result, isFalse);
      expect(authProvider.status, equals(AuthStatus.unauthenticated));
      expect(authProvider.errorMessage, equals('Validation failed'));
      expect(authProvider.validationErrors, isNotNull);
      expect(authProvider.validationErrors!['email'], isNotNull);
    });

    test('logout should reset auth state', () async {
      await authProvider.login(email: 'admin@dairy.com', password: 'password');
      await authProvider.logout();

      expect(authProvider.status, equals(AuthStatus.unauthenticated));
      expect(authProvider.user, isNull);
    });
  });
}
