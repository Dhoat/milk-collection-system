import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/storage/secure_token_storage.dart';
import 'package:mobile/core/storage/token_storage_interface.dart';

void main() {
  group('TokenStorage Unit Tests', () {
    late ITokenStorage storage;

    setUp(() {
      storage = InMemoryTokenStorage();
    });

    test('should return null when no token is saved', () async {
      final token = await storage.getToken();
      final hasToken = await storage.hasToken();

      expect(token, isNull);
      expect(hasToken, isFalse);
    });

    test('should save and retrieve token correctly', () async {
      const sampleToken = '1|sanctum_bearer_token_string';
      await storage.saveToken(sampleToken);

      final token = await storage.getToken();
      final hasToken = await storage.hasToken();

      expect(token, equals(sampleToken));
      expect(hasToken, isTrue);
    });

    test('should delete token correctly', () async {
      await storage.saveToken('1|test_token');
      await storage.deleteToken();

      final token = await storage.getToken();
      final hasToken = await storage.hasToken();

      expect(token, isNull);
      expect(hasToken, isFalse);
    });
  });
}
