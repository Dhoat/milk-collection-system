import 'package:flutter/foundation.dart';
import '../../../core/errors/api_exception.dart';
import '../../auth/models/user_model.dart';
import '../repositories/profile_repository_interface.dart';

class ProfileProvider extends ChangeNotifier {
  final IProfileRepository profileRepository;

  bool _isLoading = false;
  bool _isSavingProfile = false;
  bool _isSavingPassword = false;
  UserModel? _user;
  String? _errorMessage;
  String? _successMessage;
  Map<String, dynamic>? _validationErrors;

  ProfileProvider({required this.profileRepository});

  bool get isLoading => _isLoading;
  bool get isSavingProfile => _isSavingProfile;
  bool get isSavingPassword => _isSavingPassword;
  UserModel? get user => _user;
  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;
  Map<String, dynamic>? get validationErrors => _validationErrors;

  Future<void> fetchProfile() async {
    _isLoading = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      _user = await profileRepository.getProfile();
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to load profile details.';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> updateProfile({required String name, required String email}) async {
    _isSavingProfile = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      _user = await profileRepository.updateProfile(name: name, email: email);
      _successMessage = 'Profile updated successfully.';
      _isSavingProfile = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'An unexpected error occurred while updating profile.';
    } finally {
      _isSavingProfile = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> updatePassword({
    required String currentPassword,
    required String newPassword,
    required String newPasswordConfirmation,
  }) async {
    _isSavingPassword = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      await profileRepository.updatePassword(
        currentPassword: currentPassword,
        newPassword: newPassword,
        newPasswordConfirmation: newPasswordConfirmation,
      );
      _successMessage = 'Password changed successfully.';
      _isSavingPassword = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'An unexpected error occurred while changing password.';
    } finally {
      _isSavingPassword = false;
      notifyListeners();
    }
    return false;
  }

  void clearMessages() {
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();
  }
}
