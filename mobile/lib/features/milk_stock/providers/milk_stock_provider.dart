import 'package:flutter/foundation.dart';
import '../../../core/errors/api_exception.dart';
import '../models/milk_stock_model.dart';
import '../repositories/milk_stock_repository_interface.dart';

enum MilkStockStatus { initial, loading, loaded, error }

class MilkStockProvider extends ChangeNotifier {
  final IMilkStockRepository repository;

  MilkStockStatus _status = MilkStockStatus.initial;
  List<MilkStockModel> _transactions = [];
  MilkStockSummaryModel? _summary;

  String? _selectedDate;
  String? _selectedType;
  String _searchQuery = '';

  bool _isSaving = false;
  String? _errorMessage;
  String? _successMessage;
  Map<String, dynamic>? _validationErrors;

  MilkStockProvider({required this.repository});

  MilkStockStatus get status => _status;
  List<MilkStockModel> get transactions => _transactions;
  MilkStockSummaryModel? get summary => _summary;

  String? get selectedDate => _selectedDate;
  String? get selectedType => _selectedType;
  String get searchQuery => _searchQuery;

  bool get isLoading => _status == MilkStockStatus.loading;
  bool get hasError => _status == MilkStockStatus.error;
  bool get isSaving => _isSaving;

  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;
  Map<String, dynamic>? get validationErrors => _validationErrors;

  double get availableStock => _summary?.availableStock ?? 0.0;

  Future<void> fetchMilkStocks() async {
    _status = MilkStockStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await repository.getMilkStocks(
        date: _selectedDate,
        type: _selectedType,
        search: _searchQuery,
      );
      _summary = result['summary'] as MilkStockSummaryModel?;
      _transactions = result['transactions'] as List<MilkStockModel>;
      _status = MilkStockStatus.loaded;
    } on ApiException catch (e) {
      _status = MilkStockStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = MilkStockStatus.error;
      _errorMessage = 'Failed to load milk stock ledger.';
    }

    notifyListeners();
  }

  void setDateFilter(String? date) {
    _selectedDate = date;
    fetchMilkStocks();
  }

  void setTypeFilter(String? type) {
    _selectedType = type;
    fetchMilkStocks();
  }

  void setSearchQuery(String query) {
    _searchQuery = query;
    fetchMilkStocks();
  }

  void clearFilters() {
    _selectedDate = null;
    _selectedType = null;
    _searchQuery = '';
    fetchMilkStocks();
  }

  Future<bool> recordStockOut(Map<String, dynamic> data) async {
    _isSaving = true;
    _errorMessage = null;
    _successMessage = null;
    _validationErrors = null;
    notifyListeners();

    try {
      final newStockOut = await repository.recordStockOut(data);
      _transactions.insert(0, newStockOut);
      _successMessage = 'Stock OUT transaction recorded successfully.';
      
      // Refresh summary & stock list to update available stock
      await fetchMilkStocks();
      
      _isSaving = false;
      notifyListeners();
      return true;
    } on ValidationException catch (e) {
      _errorMessage = e.message;
      _validationErrors = e.errors;
    } on ApiException catch (e) {
      _errorMessage = e.message;
    } catch (e) {
      _errorMessage = 'Failed to record stock out transaction.';
    } finally {
      _isSaving = false;
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
