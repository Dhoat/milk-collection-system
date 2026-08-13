import '../models/user_model.dart';

abstract class IAuthRepository {
  Future<UserModel> login({required String email, required String password});
  Future<UserModel> getCurrentUser();
  Future<void> logout();
}
