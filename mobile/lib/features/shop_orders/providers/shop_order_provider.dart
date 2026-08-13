import 'package:flutter/foundation.dart';
import '../../../core/errors/api_exception.dart';
import '../models/product_model.dart';
import '../models/shop_order_model.dart';
import '../repositories/shop_order_repository_interface.dart';

enum ShopOrderStatus { initial, loading, loaded, error }

class ShopOrderProvider extends ChangeNotifier {
  final IShopOrderRepository repository;

  ShopOrderStatus _status = ShopOrderStatus.initial;
  List<ShopOrderModel> _orders = [];
  List<ProductModel> _products = [];

  String _searchQuery = '';
  String? _selectedStatus;
  int? _selectedShopId;
  String? _selectedDate;

  bool _isSaving = false;
  bool _isUpdatingStatus = false;
  bool _isDeleting = false;

  String? _errorMessage;
  String? _successMessage;
  Map<String, dynamic>? _validationErrors;

  ShopOrderProvider({required this.repository});

  ShopOrderStatus get status => _status;
  List<ShopOrderModel> get orders {
    var result = _orders;
    if (_searchQuery.isNotEmpty) {
      final q = _searchQuery.toLowerCase();
      result = result
          .where((o) =>
              o.orderNumber.toLowerCase().contains(q) ||
              (o.shop?.name.toLowerCase().contains(q) ?? false) ||
              (o.shop?.shopCode.toLowerCase().contains(q) ?? false))
          .toList();
    }
    if (_selectedStatus != null && _selectedStatus!.isNotEmpty) {
      result = result.where((o) => o.status == _selectedStatus).toList();
    }
    if (_selectedShopId != null) {
      result = result.where((o) => o.shopId == _selectedShopId).toList();
    }
    if (_selectedDate != null && _selectedDate!.isNotEmpty) {
      result = result.where((o) => o.orderDate == _selectedDate).toList();
    }
    return result;
  }

  List<ShopOrderModel> get allOrders => _orders;
  List<ProductModel> get products => _products;

  String get searchQuery => _searchQuery;
  String? get selectedStatus => _selectedStatus;
  int? get selectedShopId => _selectedShopId;
  String? get selectedDate => _selectedDate;

  bool get isLoading => _status == ShopOrderStatus.loading;
  bool get hasError => _status == ShopOrderStatus.error;
  bool get isSaving => _isSaving;
  bool get isUpdatingStatus => _isUpdatingStatus;
  bool get isDeleting => _isDeleting;

  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;
  Map<String, dynamic>? get validationErrors => _validationErrors;

  Future<void> fetchShopOrders() async {
    _status = ShopOrderStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      _orders = await repository.getShopOrders(
        search: _searchQuery,
        status: _selectedStatus,
        shopId: _selectedShopId,
        date: _selectedDate,
      );
      _status = ShopOrderStatus.loaded;
    } on ApiException catch (e) {
      _status = ShopOrderStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = ShopOrderStatus.error;
      _errorMessage = 'Failed to load shop orders.';
    }

    notifyListeners();
  }

  Future<void> fetchProducts() async {
    try {
      _products = await repository.getProducts();
      notifyListeners();
    } catch (_) {
      // Products fetch fallback
    }
  }

  void setSearchQuery(String query) {
    _searchQuery = query;
    fetchShopOrders();
  }

  void setStatusFilter(String? status) {
    _selectedStatus = status;
    fetchShopOrders();
  }

  void setShopFilter(int? shopId) {
    _selectedShopId = shopId;
    fetchShopOrders();
  }

  void setDateFilter(String? date) {
    _selectedDate = date;
    fetchShopOrders();
  }

  void clearFilters() {
    _searchQuery = '';
    _selectedStatus = null;
    _selectedShopId = null;
    _selectedDate = null;
    fetchShopOrders();
  }

  Future<bool> createShopOrder(Map<String, dynamic> data) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final newOrder = await repository.createShopOrder(data);
      _orders.insert(0, newOrder);
      _successMessage = 'Shop order created successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to create shop order.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> updateShopOrderStatus(int id, String status) async {
    _isUpdatingStatus = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      final updatedOrder = await repository.updateShopOrderStatus(id, status);
      final index = _orders.indexWhere((o) => o.id == id);
      if (index != -1) {
        _orders[index] = updatedOrder;
      }
      _successMessage = 'Order status updated to ${updatedOrder.statusDisplayName}.';
      _isUpdatingStatus = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to update order status.';
    } finally {
      _isUpdatingStatus = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> deleteShopOrder(int id) async {
    _isDeleting = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      await repository.deleteShopOrder(id);
      _orders.removeWhere((o) => o.id == id);
      _successMessage = 'Shop order deleted.';
      _isDeleting = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to delete shop order.';
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
