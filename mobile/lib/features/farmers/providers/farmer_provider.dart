import 'package:flutter/foundation.dart';
import '../../../core/errors/api_exception.dart';
import '../models/farmer_model.dart';
import '../repositories/farmer_repository_interface.dart';

enum FarmerStatus { initial, loading, loaded, error }

class FarmerProvider extends ChangeNotifier {
  final IFarmerRepository farmerRepository;

  FarmerStatus _status = FarmerStatus.initial;
  List<FarmerModel> _farmers = [];
  String _searchQuery = '';
  int? _selectedVillageId;

  bool _isSaving = false;
  bool _isDeleting = false;

  String? _errorMessage;
  String? _successMessage;
  Map<String, dynamic>? _validationErrors;

  FarmerProvider({required this.farmerRepository});

  FarmerStatus get status => _status;
  
  List<FarmerModel> get farmers {
    var result = _farmers;
    if (_selectedVillageId != null) {
      result = result.where((f) => f.villageId == _selectedVillageId).toList();
    }
    if (_searchQuery.isNotEmpty) {
      final query = _searchQuery.toLowerCase();
      result = result.where((f) {
        final matchesName = f.name.toLowerCase().contains(query);
        final matchesCode = f.farmerCode.toLowerCase().contains(query);
        final matchesMobile = f.mobile.contains(query);
        final matchesVillage = f.village?.name.toLowerCase().contains(query) ?? false;
        return matchesName || matchesCode || matchesMobile || matchesVillage;
      }).toList();
    }
    return result;
  }

  List<FarmerModel> get allFarmers => _farmers;
  String get searchQuery => _searchQuery;
  int? get selectedVillageId => _selectedVillageId;
  bool get isLoading => _status == FarmerStatus.loading;
  bool get hasError => _status == FarmerStatus.error;
  bool get isSaving => _isSaving;
  bool get isDeleting => _isDeleting;
  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;
  Map<String, dynamic>? get validationErrors => _validationErrors;

  Future<void> fetchFarmers({String? search, int? villageId}) async {
    _status = FarmerStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      _farmers = await farmerRepository.getFarmers(
        search: search,
        villageId: villageId ?? _selectedVillageId,
      );
      _status = FarmerStatus.loaded;
    } on ApiException catch (e) {
      _status = FarmerStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = FarmerStatus.error;
      _errorMessage = 'Failed to load farmers list.';
    }

    notifyListeners();
  }

  void setSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }

  void setVillageFilter(int? villageId) {
    _selectedVillageId = villageId;
    fetchFarmers(villageId: villageId);
  }

  Future<bool> createFarmer(Map<String, dynamic> farmerData) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final newFarmer = await farmerRepository.createFarmer(farmerData);
      _farmers.insert(0, newFarmer);
      _successMessage = 'Farmer "${newFarmer.name}" registered successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to register farmer.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> updateFarmer(int id, Map<String, dynamic> farmerData) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final updatedFarmer = await farmerRepository.updateFarmer(id, farmerData);

      final index = _farmers.indexWhere((f) => f.id == id);
      if (index != -1) {
        _farmers[index] = updatedFarmer;
      }
      _successMessage = 'Farmer "${updatedFarmer.name}" updated successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to update farmer.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> deleteFarmer(int id) async {
    _isDeleting = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      await farmerRepository.deleteFarmer(id);
      _farmers.removeWhere((f) => f.id == id);
      _successMessage = 'Farmer record removed successfully.';
      _isDeleting = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to delete farmer record.';
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
