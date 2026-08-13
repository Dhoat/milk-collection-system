import 'package:flutter/foundation.dart';

import '../../../core/errors/api_exception.dart';
import '../../auth/models/user_model.dart';
import '../repositories/user_repository_interface.dart';

enum UserProviderStateStatus { initial, loading, loaded, error, forbidden }

class UserProvider extends ChangeNotifier {
  final IUserRepository repository;

  UserProvider({required this.repository});

  UserProviderStateStatus _status = UserProviderStateStatus.initial;
  String? _errorMessage;
  String? _successMessage;
  Map<String, List<String>> _fieldErrors = {};

  List<UserModel> _users = [];
  UserModel? _selectedUser;

  // Pagination state
  int _currentPage = 1;
  int _lastPage = 1;
  int _totalUsers = 0;
  int _perPage = 15;

  // Filters state
  String _searchQuery = '';
  String? _selectedRoleFilter;
  String? _selectedStatusFilter;

  // Action submitting state
  bool _isSubmitting = false;

  // Getters
  UserProviderStateStatus get status => _status;
  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;
  Map<String, List<String>> get fieldErrors => _fieldErrors;

  List<UserModel> get users => List.unmodifiable(_users);
  UserModel? get selectedUser => _selectedUser;

  int get currentPage => _currentPage;
  int get lastPage => _lastPage;
  int get totalUsers => _totalUsers;
  int get perPage => _perPage;

  String get searchQuery => _searchQuery;
  String? get selectedRoleFilter => _selectedRoleFilter;
  String? get selectedStatusFilter => _selectedStatusFilter;
  bool get isSubmitting => _isSubmitting;

  void setSearchQuery(String query) {
    if (_searchQuery == query) return;
    _searchQuery = query;
    fetchUsers(refresh: true);
  }

  void setRoleFilter(String? role) {
    if (_selectedRoleFilter == role) return;
    _selectedRoleFilter = role;
    fetchUsers(refresh: true);
  }

  void setStatusFilter(String? status) {
    if (_selectedStatusFilter == status) return;
    _selectedStatusFilter = status;
    fetchUsers(refresh: true);
  }

  void clearFieldErrors() {
    _fieldErrors = {};
    _errorMessage = null;
    notifyListeners();
  }

  void clearMessages() {
    _errorMessage = null;
    _successMessage = null;
    _fieldErrors = {};
    notifyListeners();
  }

  Future<void> fetchUsers({bool refresh = false, int page = 1}) async {
    if (refresh) {
      _currentPage = 1;
    } else {
      _currentPage = page;
    }

    _status = UserProviderStateStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await repository.getUsers(
        page: _currentPage,
        search: _searchQuery,
        role: _selectedRoleFilter,
        status: _selectedStatusFilter,
      );

      _users = result['users'] as List<UserModel>;
      final meta = result['meta'] as Map<String, dynamic>;

      _currentPage = meta['current_page'] is int ? meta['current_page'] : 1;
      _lastPage = meta['last_page'] is int ? meta['last_page'] : 1;
      _totalUsers = meta['total'] is int ? meta['total'] : _users.length;
      _perPage = meta['per_page'] is int ? meta['per_page'] : 15;

      _status = UserProviderStateStatus.loaded;
    } on ForbiddenException catch (e) {
      _status = UserProviderStateStatus.forbidden;
      _errorMessage = e.message.isNotEmpty
          ? e.message
          : 'Access denied: User management is restricted to Super Admins only.';
    } on ApiException catch (e) {
      _status = UserProviderStateStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = UserProviderStateStatus.error;
      _errorMessage = 'Failed to load user accounts. Please try again.';
    }
    notifyListeners();
  }

  Future<void> fetchUserDetail(int id) async {
    _status = UserProviderStateStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      _selectedUser = await repository.getUserDetail(id);
      _status = UserProviderStateStatus.loaded;
    } on ForbiddenException catch (e) {
      _status = UserProviderStateStatus.forbidden;
      _errorMessage = e.message;
    } on ApiException catch (e) {
      _status = UserProviderStateStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = UserProviderStateStatus.error;
      _errorMessage = 'Failed to load user details.';
    }
    notifyListeners();
  }

  Future<bool> createUser(Map<String, dynamic> data) async {
    _isSubmitting = true;
    _errorMessage = null;
    _successMessage = null;
    _fieldErrors = {};
    notifyListeners();

    try {
      final newUser = await repository.createUser(data);
      _successMessage = 'User "${newUser.name}" created successfully.';
      _isSubmitting = false;
      notifyListeners();
      await fetchUsers(refresh: true);
      return true;
    } on ValidationException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      _fieldErrors = (e.errors ?? {}).map((key, value) => MapEntry(key, List<String>.from(value)));
      notifyListeners();
      return false;
    } on ForbiddenException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } on ApiException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } catch (e) {
      _isSubmitting = false;
      _errorMessage = 'Failed to create user account.';
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateUser(int id, Map<String, dynamic> data) async {
    _isSubmitting = true;
    _errorMessage = null;
    _successMessage = null;
    _fieldErrors = {};
    notifyListeners();

    try {
      final updated = await repository.updateUser(id, data);
      _selectedUser = updated;
      _successMessage = 'User "${updated.name}" updated successfully.';

      // Update in local list if present
      final index = _users.indexWhere((u) => u.id == id);
      if (index != -1) {
        final newList = List<UserModel>.from(_users);
        newList[index] = updated;
        _users = newList;
      }

      _isSubmitting = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      _fieldErrors = (e.errors ?? {}).map((key, value) => MapEntry(key, List<String>.from(value)));
      notifyListeners();
      return false;
    } on ForbiddenException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } on ApiException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } catch (e) {
      _isSubmitting = false;
      _errorMessage = 'Failed to update user account.';
      notifyListeners();
      return false;
    }
  }

  Future<bool> toggleUserStatus(int id) async {
    _isSubmitting = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      final updated = await repository.toggleUserStatus(id);
      _selectedUser = updated;
      final statusText = updated.status ? 'activated' : 'deactivated';
      _successMessage = 'User account ${updated.name} $statusText.';

      final index = _users.indexWhere((u) => u.id == id);
      if (index != -1) {
        final newList = List<UserModel>.from(_users);
        newList[index] = updated;
        _users = newList;
      }

      _isSubmitting = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } on ForbiddenException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } on ApiException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } catch (e) {
      _isSubmitting = false;
      _errorMessage = 'Failed to change user status.';
      notifyListeners();
      return false;
    }
  }

  Future<bool> deleteUser(int id) async {
    _isSubmitting = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      await repository.deleteUser(id);
      _successMessage = 'User account deleted successfully.';
      _users = _users.where((u) => u.id != id).toList();
      _isSubmitting = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } on ForbiddenException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } on ApiException catch (e) {
      _isSubmitting = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } catch (e) {
      _isSubmitting = false;
      _errorMessage = 'Failed to delete user account.';
      notifyListeners();
      return false;
    }
  }
}
