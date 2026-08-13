import 'package:flutter/foundation.dart';
import '../../../core/errors/api_exception.dart';
import '../models/village_model.dart';
import '../repositories/village_repository_interface.dart';

enum VillageStatus { initial, loading, loaded, error }

class VillageProvider extends ChangeNotifier {
  final IVillageRepository villageRepository;

  VillageStatus _status = VillageStatus.initial;
  List<VillageModel> _villages = [];
  String _searchQuery = '';

  bool _isSaving = false;
  bool _isDeleting = false;

  String? _errorMessage;
  String? _successMessage;
  Map<String, dynamic>? _validationErrors;

  VillageProvider({required this.villageRepository});

  VillageStatus get status => _status;
  List<VillageModel> get villages {
    if (_searchQuery.isEmpty) return _villages;
    final query = _searchQuery.toLowerCase();
    return _villages
        .where((v) =>
            v.name.toLowerCase().contains(query) ||
            v.code.toLowerCase().contains(query) ||
            (v.address != null && v.address!.toLowerCase().contains(query)))
        .toList();
  }

  List<VillageModel> get allVillages => _villages;
  String get searchQuery => _searchQuery;
  bool get isLoading => _status == VillageStatus.loading;
  bool get hasError => _status == VillageStatus.error;
  bool get isSaving => _isSaving;
  bool get isDeleting => _isDeleting;
  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;
  Map<String, dynamic>? get validationErrors => _validationErrors;

  Future<void> fetchVillages({String? search}) async {
    _status = VillageStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      _villages = await villageRepository.getVillages(search: search);
      _status = VillageStatus.loaded;
    } on ApiException catch (e) {
      _status = VillageStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = VillageStatus.error;
      _errorMessage = 'Failed to load villages list.';
    }

    notifyListeners();
  }

  void setSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }

  Future<bool> createVillage({
    required String name,
    required String code,
    String? address,
    bool status = true,
  }) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final newVillage = await villageRepository.createVillage(
        name: name,
        code: code,
        address: address,
        status: status,
      );
      _villages.insert(0, newVillage);
      _successMessage = 'Village "${newVillage.name}" created successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to create village.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> updateVillage({
    required int id,
    required String name,
    required String code,
    String? address,
    bool status = true,
  }) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final updatedVillage = await villageRepository.updateVillage(
        id: id,
        name: name,
        code: code,
        address: address,
        status: status,
      );

      final index = _villages.indexWhere((v) => v.id == id);
      if (index != -1) {
        _villages[index] = updatedVillage;
      }
      _successMessage = 'Village "${updatedVillage.name}" updated successfully.';
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to update village.';
    } finally {
      _isSaving = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> deleteVillage(int id) async {
    _isDeleting = true;
    _errorMessage = null;
    _successMessage = null;
    notifyListeners();

    try {
      await villageRepository.deleteVillage(id);
      _villages.removeWhere((v) => v.id == id);
      _successMessage = 'Village deleted successfully.';
      _isDeleting = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to delete village.';
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
