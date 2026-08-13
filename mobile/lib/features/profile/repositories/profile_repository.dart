import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../../auth/models/user_model.dart';
import 'profile_repository_interface.dart';

class ProfileRepository implements IProfileRepository {
  final ApiClient apiClient;

  ProfileRepository({required this.apiClient});

  @override
  Future<UserModel> getProfile() async {
    final response = await apiClient.get(ApiConstants.profile);
    final data = response['data'] as Map<String, dynamic>;
    final userJson = data['user'] as Map<String, dynamic>;
    return UserModel.fromJson(userJson);
  }

  @override
  Future<UserModel> updateProfile({required String name, required String email}) async {
    final response = await apiClient.put(
      ApiConstants.profile,
      body: {
        'name': name,
        'email': email,
      },
    );
    final data = response['data'] as Map<String, dynamic>;
    final userJson = data['user'] as Map<String, dynamic>;
    return UserModel.fromJson(userJson);
  }

  @override
  Future<void> updatePassword({
    required String currentPassword,
    required String newPassword,
    required String newPasswordConfirmation,
  }) async {
    await apiClient.patch(
      ApiConstants.profilePassword,
      body: {
        'current_password': currentPassword,
        'password': newPassword,
        'password_confirmation': newPasswordConfirmation,
      },
    );
  }
}
