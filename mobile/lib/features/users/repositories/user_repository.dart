import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../../auth/models/user_model.dart';
import 'user_repository_interface.dart';

class UserRepository implements IUserRepository {
  final ApiClient apiClient;

  UserRepository({required this.apiClient});

  @override
  Future<Map<String, dynamic>> getUsers({
    int page = 1,
    String? search,
    String? role,
    String? status,
  }) async {
    final queryParams = <String, String>{
      'page': page.toString(),
    };

    if (search != null && search.trim().isNotEmpty) {
      queryParams['search'] = search.trim();
    }
    if (role != null && role.isNotEmpty) {
      queryParams['role'] = role;
    }
    if (status != null && status.isNotEmpty) {
      queryParams['status'] = status;
    }

    final response = await apiClient.get(
      ApiConstants.users,
      queryParameters: queryParams,
    );

    final rawList = response['data'] as List<dynamic>? ?? [];
    final users = rawList.map((json) => UserModel.fromJson(json)).toList();
    final meta = response['meta'] as Map<String, dynamic>? ?? {};

    return {
      'users': users,
      'meta': meta,
    };
  }

  @override
  Future<UserModel> getUserDetail(int id) async {
    final response = await apiClient.get('${ApiConstants.users}/$id');
    return UserModel.fromJson(response['data']);
  }

  @override
  Future<UserModel> createUser(Map<String, dynamic> data) async {
    final response = await apiClient.post(
      ApiConstants.users,
      body: data,
    );
    return UserModel.fromJson(response['data']);
  }

  @override
  Future<UserModel> updateUser(int id, Map<String, dynamic> data) async {
    final response = await apiClient.put(
      '${ApiConstants.users}/$id',
      body: data,
    );
    return UserModel.fromJson(response['data']);
  }

  @override
  Future<UserModel> toggleUserStatus(int id) async {
    final response = await apiClient.patch(
      '${ApiConstants.users}/$id/toggle-status',
    );
    return UserModel.fromJson(response['data']);
  }

  @override
  Future<void> deleteUser(int id) async {
    await apiClient.delete('${ApiConstants.users}/$id');
  }
}
