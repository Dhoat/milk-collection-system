import 'package:flutter/foundation.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'token_storage_interface.dart';

class SecureTokenStorage implements ITokenStorage {
  static const String _tokenKey = 'sanctum_access_token';
  final FlutterSecureStorage _storage;

  SecureTokenStorage({FlutterSecureStorage? storage})
      : _storage = storage ??
            const FlutterSecureStorage(
              aOptions: AndroidOptions(
                encryptedSharedPreferences: true,
              ),
            );

  @override
  Future<String?> getToken() async {
    try {
      final token = await _storage.read(key: _tokenKey);
      if (kDebugMode) {
        print('[TOKEN_STORAGE] getToken() -> ${token != null ? "Found" : "Null"}');
      }
      return token;
    } catch (e) {
      if (kDebugMode) {
        print('[TOKEN_STORAGE] getToken() failed: $e');
      }
      return null;
    }
  }

  @override
  Future<void> saveToken(String token) async {
    try {
      if (kDebugMode) {
        print('[TOKEN_STORAGE] saveToken() saving token...');
      }
      await _storage.write(key: _tokenKey, value: token);
      if (kDebugMode) {
        print('[TOKEN_STORAGE] saveToken() success');
      }
    } catch (e) {
      if (kDebugMode) {
        print('[TOKEN_STORAGE] saveToken() error: $e');
      }
      // Fallback if encryptedSharedPreferences fails on legacy emulators
      try {
        const fallbackStorage = FlutterSecureStorage();
        await fallbackStorage.write(key: _tokenKey, value: token);
      } catch (fallbackErr) {
        if (kDebugMode) {
          print('[TOKEN_STORAGE] fallback saveToken() error: $fallbackErr');
        }
      }
    }
  }

  @override
  Future<void> deleteToken() async {
    try {
      await _storage.delete(key: _tokenKey);
      if (kDebugMode) {
        print('[TOKEN_STORAGE] deleteToken() success');
      }
    } catch (e) {
      if (kDebugMode) {
        print('[TOKEN_STORAGE] deleteToken() error: $e');
      }
    }
  }

  @override
  Future<bool> hasToken() async {
    final token = await getToken();
    return token != null && token.isNotEmpty;
  }
}

/// In-memory storage implementation for unit testing
class InMemoryTokenStorage implements ITokenStorage {
  String? _token;

  InMemoryTokenStorage([this._token]);

  @override
  Future<String?> getToken() async => _token;

  @override
  Future<void> saveToken(String token) async {
    _token = token;
  }

  @override
  Future<void> deleteToken() async {
    _token = null;
  }

  @override
  Future<bool> hasToken() async => _token != null && _token!.isNotEmpty;
}
