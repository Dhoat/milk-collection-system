import 'package:flutter/foundation.dart';

import '../../../core/errors/api_exception.dart';
import '../models/delivery_model.dart';
import '../repositories/delivery_repository_interface.dart';

enum DeliveryStateStatus { initial, loading, loaded, error }

class DeliveryProvider extends ChangeNotifier {
  final IDeliveryRepository repository;

  DeliveryStateStatus _status = DeliveryStateStatus.initial;
  List<DeliveryModel> _deliveries = [];
  String? _errorMessage;
  Map<String, dynamic>? _validationErrors;
  bool _isSaving = false;

  String _searchQuery = '';
  String _statusFilter = 'all';
  String? _dateFilter;

  DeliveryProvider({required this.repository});

  DeliveryStateStatus get status => _status;
  List<DeliveryModel> get allDeliveries => _deliveries;
  String? get errorMessage => _errorMessage;
  Map<String, dynamic>? get validationErrors => _validationErrors;
  bool get isSaving => _isSaving;

  String get searchQuery => _searchQuery;
  String get statusFilter => _statusFilter;
  String? get dateFilter => _dateFilter;

  List<DeliveryModel> get deliveries {
    return _deliveries.where((d) {
      final matchesSearch = _searchQuery.isEmpty ||
          d.deliveryNumber.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          (d.shop?.name ?? '').toLowerCase().contains(_searchQuery.toLowerCase()) ||
          (d.shopOrder?.orderNumber ?? '').toLowerCase().contains(_searchQuery.toLowerCase()) ||
          (d.contactPerson ?? '').toLowerCase().contains(_searchQuery.toLowerCase());

      final matchesStatus =
          _statusFilter == 'all' || d.status.toLowerCase() == _statusFilter.toLowerCase();

      final matchesDate = _dateFilter == null || d.deliveryDate == _dateFilter;

      return matchesSearch && matchesStatus && matchesDate;
    }).toList();
  }

  int get totalCount => _deliveries.length;
  int get pendingCount => _deliveries.where((d) => d.status == 'pending' || d.status == 'assigned').length;
  int get outForDeliveryCount => _deliveries.where((d) => d.status == 'out_for_delivery').length;
  int get deliveredCount => _deliveries.where((d) => d.status == 'delivered').length;

  void setSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }

  void setStatusFilter(String status) {
    _statusFilter = status;
    notifyListeners();
  }

  void setDateFilter(String? date) {
    _dateFilter = date;
    notifyListeners();
  }

  void clearMessages() {
    _errorMessage = null;
    _validationErrors = null;
    notifyListeners();
  }

  Future<void> fetchDeliveries({bool refresh = false}) async {
    if (_status == DeliveryStateStatus.loading && !refresh) return;

    _status = DeliveryStateStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      _deliveries = await repository.getDeliveries(
        search: _searchQuery.isNotEmpty ? _searchQuery : null,
        status: _statusFilter != 'all' ? _statusFilter : null,
        date: _dateFilter,
      );
      _status = DeliveryStateStatus.loaded;
    } on ApiException catch (e) {
      _status = DeliveryStateStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = DeliveryStateStatus.error;
      _errorMessage = 'Failed to load deliveries. Please try again.';
    }
    notifyListeners();
  }

  Future<bool> createDelivery(Map<String, dynamic> data) async {
    _isSaving = true;
    _errorMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final newDelivery = await repository.createDelivery(data);
      _deliveries.insert(0, newDelivery);
      _isSaving = false;
      _status = DeliveryStateStatus.loaded;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _isSaving = false;
      _errorMessage = e.message;
      _validationErrors = e.errors;
      notifyListeners();
      return false;
    } on ApiException catch (e) {
      _isSaving = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } catch (e) {
      _isSaving = false;
      _errorMessage = 'Failed to create delivery record.';
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateDeliveryStatus(int id, String newStatus, {int? assignedTo}) async {
    _isSaving = true;
    _errorMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final updatedDelivery = await repository.updateDeliveryStatus(id, newStatus, assignedTo: assignedTo);
      final index = _deliveries.indexWhere((d) => d.id == id);
      if (index != -1) {
        _deliveries[index] = updatedDelivery;
      }
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _isSaving = false;
      _errorMessage = e.message;
      _validationErrors = e.errors;
      notifyListeners();
      return false;
    } on ApiException catch (e) {
      _isSaving = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } catch (e) {
      _isSaving = false;
      _errorMessage = 'Failed to update delivery status.';
      notifyListeners();
      return false;
    }
  }

  Future<bool> deleteDelivery(int id) async {
    _isSaving = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await repository.deleteDelivery(id);
      _deliveries.removeWhere((d) => d.id == id);
      _isSaving = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      _isSaving = false;
      _errorMessage = e.message;
      notifyListeners();
      return false;
    } catch (e) {
      _isSaving = false;
      _errorMessage = 'Failed to delete delivery record.';
      notifyListeners();
      return false;
    }
  }
}
