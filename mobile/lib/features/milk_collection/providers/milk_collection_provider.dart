import 'package:flutter/foundation.dart';
import '../../../core/errors/api_exception.dart';
import '../models/milk_collection_model.dart';
import '../repositories/milk_collection_repository_interface.dart';

enum MilkCollectionStatus { initial, loading, loaded, error }

class MilkCollectionProvider extends ChangeNotifier {
  final IMilkCollectionRepository repository;

  MilkCollectionStatus _status = MilkCollectionStatus.initial;
  List<MilkCollectionModel> _collections = [];

  String _searchQuery = '';
  int? _selectedFarmerId;
  int? _selectedVillageId;
  String? _selectedDate;
  String? _selectedShift;

  bool _isSaving = false;
  bool _isDeleting = false;

  String? _errorMessage;
  String? _successMessage;
  Map<String, dynamic>? _validationErrors;

  MilkCollectionProvider({required this.repository});

  MilkCollectionStatus get status => _status;
  List<MilkCollectionModel> get collections {
    var result = _collections;
    if (_searchQuery.isNotEmpty) {
      final query = _searchQuery.toLowerCase();
      result = result.where((c) {
        final matchesFarmer = c.farmer?.name.toLowerCase().contains(query) ?? false;
        final matchesCode = c.farmer?.farmerCode.toLowerCase().contains(query) ?? false;
        final matchesVillage = c.farmer?.village?.name.toLowerCase().contains(query) ?? false;
        return matchesFarmer || matchesCode || matchesVillage;
      }).toList();
    }
    if (_selectedShift != null && _selectedShift!.isNotEmpty) {
      result = result.where((c) => c.shift == _selectedShift).toList();
    }
    if (_selectedFarmerId != null) {
      result = result.where((c) => c.farmerId == _selectedFarmerId).toList();
    }
    if (_selectedVillageId != null) {
      result = result.where((c) => c.farmer?.villageId == _selectedVillageId).toList();
    }
    if (_selectedDate != null && _selectedDate!.isNotEmpty) {
      result = result.where((c) => c.collectionDate == _selectedDate).toList();
    }
    return result;
  }

  List<MilkCollectionModel> get allCollections => _collections;
  String get searchQuery => _searchQuery;
  int? get selectedFarmerId => _selectedFarmerId;
  int? get selectedVillageId => _selectedVillageId;
  String? get selectedDate => _selectedDate;
  String? get selectedShift => _selectedShift;

  bool get isLoading => _status == MilkCollectionStatus.loading;
  bool get hasError => _status == MilkCollectionStatus.error;
  bool get isSaving => _isSaving;
  bool get isDeleting => _isDeleting;
  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;
  Map<String, dynamic>? get validationErrors => _validationErrors;

  Future<void> fetchMilkCollections() async {
    _status = MilkCollectionStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      _collections = await repository.getMilkCollections(
        search: _searchQuery.isNotEmpty ? _searchQuery : null,
        villageId: _selectedVillageId,
        farmerId: _selectedFarmerId,
        date: _selectedDate,
        shift: _selectedShift,
      );
      _status = MilkCollectionStatus.loaded;
    } on ApiException catch (e) {
      _status = MilkCollectionStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = MilkCollectionStatus.error;
      _errorMessage = 'Failed to load milk collections list.';
    }

    notifyListeners();
  }

  void setSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }

  void setFarmerFilter(int? farmerId) {
    _selectedFarmerId = farmerId;
    fetchMilkCollections();
  }

  void setVillageFilter(int? villageId) {
    _selectedVillageId = villageId;
    fetchMilkCollections();
  }

  void setDateFilter(String? date) {
    _selectedDate = date;
    fetchMilkCollections();
  }

  void setShiftFilter(String? shift) {
    _selectedShift = shift;
    fetchMilkCollections();
  }

  void clearFilters() {
    _searchQuery = '';
    _selectedFarmerId = null;
    _selectedVillageId = null;
    _selectedDate = null;
    _selectedShift = null;
    fetchMilkCollections();
  }

  Future<bool> createMilkCollection(Map<String, dynamic> data) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final newCollection = await repository.createMilkCollection(data);
      _collections.insert(0, newCollection);
      _successMessage = 'Milk collection recorded successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to record milk collection entry.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> updateMilkCollection(int id, Map<String, dynamic> data) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final updatedCollection = await repository.updateMilkCollection(id, data);

      final index = _collections.indexWhere((c) => c.id == id);
      if (index != -1) {
        _collections[index] = updatedCollection;
      }
      _successMessage = 'Milk collection entry updated successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to update milk collection entry.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> deleteMilkCollection(int id) async {
    _isDeleting = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      await repository.deleteMilkCollection(id);
      _collections.removeWhere((c) => c.id == id);
      _successMessage = 'Milk collection record deleted.';
      _isDeleting = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to delete milk collection entry.';
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
