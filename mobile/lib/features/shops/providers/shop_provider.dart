import 'package:flutter/foundation.dart';
import '../../../core/errors/api_exception.dart';
import '../models/shop_model.dart';
import '../repositories/shop_repository_interface.dart';

enum ShopStatus { initial, loading, loaded, error }

class ShopProvider extends ChangeNotifier {
  final IShopRepository repository;

  ShopStatus _status = ShopStatus.initial;
  List<ShopModel> _shops = [];

  String _searchQuery = '';
  bool? _statusFilter;

  bool _isSaving = false;
  bool _isTogglingStatus = false;
  bool _isDeleting = false;

  String? _errorMessage;
  String? _successMessage;
  Map<String, dynamic>? _validationErrors;

  ShopProvider({required this.repository});

  ShopStatus get status => _status;
  List<ShopModel> get shops {
    var result = _shops;
    if (_searchQuery.isNotEmpty) {
      final q = _searchQuery.toLowerCase();
      result = result
          .where((s) =>
              s.name.toLowerCase().contains(q) ||
              s.shopCode.toLowerCase().contains(q) ||
              s.ownerName.toLowerCase().contains(q) ||
              s.phone.contains(q))
          .toList();
    }
    if (_statusFilter != null) {
      result = result.where((s) => s.status == _statusFilter).toList();
    }
    return result;
  }

  List<ShopModel> get allShops => _shops;
  String get searchQuery => _searchQuery;
  bool? get statusFilter => _statusFilter;

  bool get isLoading => _status == ShopStatus.loading;
  bool get hasError => _status == ShopStatus.error;
  bool get isSaving => _isSaving;
  bool get isTogglingStatus => _isTogglingStatus;
  bool get isDeleting => _isDeleting;

  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;
  Map<String, dynamic>? get validationErrors => _validationErrors;

  Future<void> fetchShops() async {
    _status = ShopStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      _shops = await repository.getShops(
        search: _searchQuery,
        status: _statusFilter,
      );
      _status = ShopStatus.loaded;
    } on ApiException catch (e) {
      _status = ShopStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = ShopStatus.error;
      _errorMessage = 'Failed to load shops.';
    }

    notifyListeners();
  }

  void setSearchQuery(String query) {
    _searchQuery = query;
    fetchShops();
  }

  void setStatusFilter(bool? filter) {
    _statusFilter = filter;
    fetchShops();
  }

  void clearFilters() {
    _searchQuery = '';
    _statusFilter = null;
    fetchShops();
  }

  Future<bool> createShop(Map<String, dynamic> data) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final newShop = await repository.createShop(data);
      _shops.insert(0, newShop);
      _successMessage = 'Shop created successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to create shop.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> updateShop(int id, Map<String, dynamic> data) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final updatedShop = await repository.updateShop(id, data);
      final index = _shops.indexWhere((s) => s.id == id);
      if (index != -1) {
        _shops[index] = updatedShop;
      }
      _successMessage = 'Shop updated successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to update shop.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> toggleShopStatus(int id) async {
    _isTogglingStatus = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final updatedShop = await repository.toggleShopStatus(id);
      final index = _shops.indexWhere((s) => s.id == id);
      if (index != -1) {
        _shops[index] = updatedShop;
      }
      _successMessage = 'Shop status updated successfully.';
      _isTogglingStatus = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to update shop status.';
    } finally {
      _isTogglingStatus = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> deleteShop(int id) async {
    _isDeleting = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      await repository.deleteShop(id);
      _shops.removeWhere((s) => s.id == id);
      _successMessage = 'Shop deleted successfully.';
      _isDeleting = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to delete shop.';
    } finally {
      _isDeleting = false;
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
