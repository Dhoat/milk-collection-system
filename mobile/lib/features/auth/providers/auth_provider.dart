import 'package:flutter/foundation.dart';

import '../../../core/errors/api_exception.dart';
import '../../../core/storage/token_storage_interface.dart';
import '../models/user_model.dart';
import '../repositories/auth_repository_interface.dart';

enum AuthStatus {
  initial,
  checking,
  authenticated,
  unauthenticated,
  authenticating,
}

class AuthProvider extends ChangeNotifier {
  final IAuthRepository authRepository;
  final ITokenStorage tokenStorage;

  AuthStatus _status = AuthStatus.initial;
  UserModel? _user;
  String? _errorMessage;
  Map<String, dynamic>? _validationErrors;

  AuthProvider({
    required this.authRepository,
    required this.tokenStorage,
  });

  // Getters
  AuthStatus get status => _status;
  UserModel? get user => _user;
  String? get errorMessage => _errorMessage;
  Map<String, dynamic>? get validationErrors => _validationErrors;
  bool get isAuthenticated => _status == AuthStatus.authenticated && _user != null;
  bool get isAuthenticating => _status == AuthStatus.authenticating;
  bool get isChecking => _status == AuthStatus.checking || _status == AuthStatus.initial;

  /// Check initial authentication state on app launch.
  Future<void> initialize() async {
    _status = AuthStatus.checking;
    _errorMessage = null;
    notifyListeners();

    final hasToken = await tokenStorage.hasToken();
    if (!hasToken) {
      _status = AuthStatus.unauthenticated;
      _user = null;
      notifyListeners();
      return;
    }

    try {
      _user = await authRepository.getCurrentUser();
      _status = AuthStatus.authenticated;
    } on UnauthorizedException {
      await tokenStorage.deleteToken();
      _user = null;
      _status = AuthStatus.unauthenticated;
    } catch (e) {
      await tokenStorage.deleteToken();
      _user = null;
      _status = AuthStatus.unauthenticated;
    }

    notifyListeners();
  }

  /// Perform user login.
  Future<bool> login({
    required String email,
    required String password,
  }) async {
    _status = AuthStatus.authenticating;
    _errorMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      if (kDebugMode) {
        print('[AUTH_PROVIDER] Starting login for: $email');
      }
      _user = await authRepository.login(
        email: email,
        password: password,
      );
      _status = AuthStatus.authenticated;
      if (kDebugMode) {
        print('[AUTH_PROVIDER] Login successful! User: ${_user?.name}');
      }
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      if (kDebugMode) {
        print('[AUTH_PROVIDER] Validation error: ${e.message}');
      }
      _status = AuthStatus.unauthenticated;
      _errorMessage = e.message;
      _validationErrors = e.errors;
      notifyListeners();
      return false;
    } on ApiException catch (e) {
      if (kDebugMode) {
        print('[AUTH_PROVIDER] API exception [${e.statusCode}]: ${e.message}');
      }
      _status = AuthStatus.unauthenticated;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } catch (e, stack) {
      if (kDebugMode) {
        print('[AUTH_PROVIDER] Unexpected error: $e');
        print('[AUTH_PROVIDER] StackTrace: $stack');
      }
      _status = AuthStatus.unauthenticated;
      _errorMessage = 'An unexpected error occurred: ${e.toString()}';
      notifyListeners();
      return false;
    }
  }

  /// Clear errors manually
  void clearError() {
    _errorMessage = null;
    _validationErrors = null;
    notifyListeners();
  }

  /// Logout current user.
  Future<void> logout() async {
    try {
      await authRepository.logout();
    } catch (_) {
      // Handled in repository
    } finally {
      _user = null;
      _status = AuthStatus.unauthenticated;
      _errorMessage = null;
      _validationErrors = null;
      notifyListeners();
    }
  }
}
