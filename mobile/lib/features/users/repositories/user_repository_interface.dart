import '../../auth/models/user_model.dart';

abstract class IUserRepository {
  Future<Map<String, dynamic>> getUsers({
    int page = 1,
    String? search,
    String? role,
    String? status,
  });

  Future<UserModel> getUserDetail(int id);

  Future<UserModel> createUser(Map<String, dynamic> data);

  Future<UserModel> updateUser(int id, Map<String, dynamic> data);

  Future<UserModel> toggleUserStatus(int id);

  Future<void> deleteUser(int id);
}
