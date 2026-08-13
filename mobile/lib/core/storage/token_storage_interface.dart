abstract class ITokenStorage {
  Future<String?> getToken();
  Future<void> saveToken(String token);
  Future<void> deleteToken();
  Future<bool> hasToken();
}
