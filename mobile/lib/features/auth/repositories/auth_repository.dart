import 'package:flutter/foundation.dart';

import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../../../core/storage/token_storage_interface.dart';
import '../models/user_model.dart';
import 'auth_repository_interface.dart';

class AuthRepository implements IAuthRepository {
  final ApiClient apiClient;
  final ITokenStorage tokenStorage;

  AuthRepository({
    required this.apiClient,
    required this.tokenStorage,
  });

  @override
  Future<UserModel> login({
    required String email,
    required String password,
  }) async {
    final response = await apiClient.post(
      ApiConstants.login,
      body: {
        'email': email,
        'password': password,
      },
    );

    if (kDebugMode) {
      print('[LOGIN_REPO] Received API response: $response');
    }

    final data = response['data'] as Map<String, dynamic>;
    final token = data['token'] as String;
    final userJson = data['user'] as Map<String, dynamic>;

    if (kDebugMode) {
      print('[LOGIN_REPO] Token received: ${token.substring(0, 5)}...');
      print('[LOGIN_REPO] Parsing userJson: $userJson');
    }

    // Save token to secure storage
    await tokenStorage.saveToken(token);

    return UserModel.fromJson(userJson);
  }

  @override
  Future<UserModel> getCurrentUser() async {
    final response = await apiClient.get(ApiConstants.me);
    final data = response['data'] as Map<String, dynamic>;
    final userJson = data['user'] as Map<String, dynamic>;

    return UserModel.fromJson(userJson);
  }

  @override
  Future<void> logout() async {
    try {
      await apiClient.post(ApiConstants.logout);
    } catch (_) {
      // Ignore API failure during logout (e.g. token already expired)
    } finally {
      // Always clear local token storage
      await tokenStorage.deleteToken();
    }
  }
}
