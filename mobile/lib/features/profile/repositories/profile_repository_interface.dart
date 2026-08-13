import '../../auth/models/user_model.dart';

abstract class IProfileRepository {
  Future<UserModel> getProfile();
  Future<UserModel> updateProfile({required String name, required String email});
  Future<void> updatePassword({
    required String currentPassword,
    required String newPassword,
    required String newPasswordConfirmation,
  });
}
