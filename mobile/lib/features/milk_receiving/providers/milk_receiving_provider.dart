import 'package:flutter/foundation.dart';
import '../../../core/errors/api_exception.dart';
import '../models/milk_receiving_model.dart';
import '../repositories/milk_receiving_repository_interface.dart';

enum MilkReceivingStatus { initial, loading, loaded, error }

class MilkReceivingProvider extends ChangeNotifier {
  final IMilkReceivingRepository repository;

  MilkReceivingStatus _status = MilkReceivingStatus.initial;
  List<MilkReceivingModel> _receivings = [];

  String? _selectedDate;
  int? _selectedVillageId;
  String? _selectedShift;
  String? _selectedStatus;

  bool _isSaving = false;
  bool _isDeleting = false;
  bool _isFetchingSummary = false;

  String? _errorMessage;
  String? _successMessage;
  Map<String, dynamic>? _validationErrors;
  Map<String, dynamic>? _currentCollectionSummary;

  MilkReceivingProvider({required this.repository});

  MilkReceivingStatus get status => _status;
  List<MilkReceivingModel> get receivings {
    var result = _receivings;
    if (_selectedVillageId != null) {
      result = result.where((r) => r.villageId == _selectedVillageId).toList();
    }
    if (_selectedShift != null && _selectedShift!.isNotEmpty) {
      result = result.where((r) => r.shift == _selectedShift).toList();
    }
    if (_selectedDate != null && _selectedDate!.isNotEmpty) {
      result = result.where((r) => r.receivingDate == _selectedDate).toList();
    }
    if (_selectedStatus != null && _selectedStatus!.isNotEmpty) {
      result = result.where((r) => r.status == _selectedStatus).toList();
    }
    return result;
  }

  List<MilkReceivingModel> get allReceivings => _receivings;
  String? get selectedDate => _selectedDate;
  int? get selectedVillageId => _selectedVillageId;
  String? get selectedShift => _selectedShift;
  String? get selectedStatus => _selectedStatus;

  bool get isLoading => _status == MilkReceivingStatus.loading;
  bool get hasError => _status == MilkReceivingStatus.error;
  bool get isSaving => _isSaving;
  bool get isDeleting => _isDeleting;
  bool get isFetchingSummary => _isFetchingSummary;

  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;
  Map<String, dynamic>? get validationErrors => _validationErrors;
  Map<String, dynamic>? get currentCollectionSummary => _currentCollectionSummary;

  Future<void> fetchMilkReceivings() async {
    _status = MilkReceivingStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      _receivings = await repository.getMilkReceivings(
        date: _selectedDate,
        villageId: _selectedVillageId,
        shift: _selectedShift,
        status: _selectedStatus,
      );
      _status = MilkReceivingStatus.loaded;
    } on ApiException catch (e) {
      _status = MilkReceivingStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = MilkReceivingStatus.error;
      _errorMessage = 'Failed to load milk receiving records.';
    }

    notifyListeners();
  }

  void setDateFilter(String? date) {
    _selectedDate = date;
    fetchMilkReceivings();
  }

  void setVillageFilter(int? villageId) {
    _selectedVillageId = villageId;
    fetchMilkReceivings();
  }

  void setShiftFilter(String? shift) {
    _selectedShift = shift;
    fetchMilkReceivings();
  }

  void setStatusFilter(String? status) {
    _selectedStatus = status;
    fetchMilkReceivings();
  }

  void clearFilters() {
    _selectedDate = null;
    _selectedVillageId = null;
    _selectedShift = null;
    _selectedStatus = null;
    fetchMilkReceivings();
  }

  Future<void> fetchCollectionSummary({
    required int villageId,
    required String date,
    required String shift,
  }) async {
    _isFetchingSummary = true;
    _currentCollectionSummary = null;
    notifyListeners();

    try {
      _currentCollectionSummary = await repository.getCollectionSummary(
        villageId: villageId,
        date: date,
        shift: shift,
      );
    } catch (_) {
      _currentCollectionSummary = null;
    } finally {
      _isFetchingSummary = false;
      notifyListeners();
    }
  }

  Future<bool> createMilkReceiving(Map<String, dynamic> data) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final newReceiving = await repository.createMilkReceiving(data);
      _receivings.insert(0, newReceiving);
      _successMessage = 'Milk receiving record created successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to create milk receiving record.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> updateMilkReceiving(int id, Map<String, dynamic> data) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final updatedReceiving = await repository.updateMilkReceiving(id, data);
      final index = _receivings.indexWhere((r) => r.id == id);
      if (index != -1) {
        _receivings[index] = updatedReceiving;
      }
      _successMessage = 'Milk receiving record updated successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to update milk receiving record.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> deleteMilkReceiving(int id) async {
    _isDeleting = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      await repository.deleteMilkReceiving(id);
      _receivings.removeWhere((r) => r.id == id);
      _successMessage = 'Milk receiving record deleted.';
      _isDeleting = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to delete milk receiving record.';
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
