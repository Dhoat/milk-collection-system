import 'package:flutter/foundation.dart';

import '../../../core/errors/api_exception.dart';
import '../models/daily_report_model.dart';
import '../models/monthly_report_model.dart';
import '../repositories/report_repository_interface.dart';

enum ReportMode { daily, monthly }
enum ReportStateStatus { initial, loading, loaded, error, forbidden }

class ReportProvider extends ChangeNotifier {
  final IReportRepository repository;

  ReportMode _mode = ReportMode.daily;
  ReportStateStatus _status = ReportStateStatus.initial;
  String? _errorMessage;

  DateTime _selectedDate = DateTime.now();
  int _selectedMonth = DateTime.now().month;
  int _selectedYear = DateTime.now().year;

  int? _selectedVillageId;
  int? _selectedShopId;
  int? _selectedProductId;

  DailyReportModel? _dailyReport;
  MonthlyReportModel? _monthlyReport;

  ReportProvider({required this.repository});

  ReportMode get mode => _mode;
  ReportStateStatus get status => _status;
  String? get errorMessage => _errorMessage;

  DateTime get selectedDate => _selectedDate;
  int get selectedMonth => _selectedMonth;
  int get selectedYear => _selectedYear;

  int? get selectedVillageId => _selectedVillageId;
  int? get selectedShopId => _selectedShopId;
  int? get selectedProductId => _selectedProductId;

  DailyReportModel? get dailyReport => _dailyReport;
  MonthlyReportModel? get monthlyReport => _monthlyReport;

  void setMode(ReportMode mode) {
    if (_mode == mode) return;
    _mode = mode;
    notifyListeners();
    fetchReport();
  }

  void setDate(DateTime date) {
    _selectedDate = date;
    notifyListeners();
    if (_mode == ReportMode.daily) {
      fetchReport();
    }
  }

  void setMonthYear(int month, int year) {
    _selectedMonth = month;
    _selectedYear = year;
    notifyListeners();
    if (_mode == ReportMode.monthly) {
      fetchReport();
    }
  }

  void setFilters({int? villageId, int? shopId, int? productId}) {
    _selectedVillageId = villageId;
    _selectedShopId = shopId;
    _selectedProductId = productId;
    notifyListeners();
    fetchReport();
  }

  void clearMessages() {
    _errorMessage = null;
    notifyListeners();
  }

  Future<void> fetchReport({bool refresh = false}) async {
    _status = ReportStateStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      if (_mode == ReportMode.daily) {
        final dateStr =
            '${_selectedDate.year}-${_selectedDate.month.toString().padLeft(2, '0')}-${_selectedDate.day.toString().padLeft(2, '0')}';
        _dailyReport = await repository.getDailyReport(
          date: dateStr,
          villageId: _selectedVillageId,
          shopId: _selectedShopId,
          productId: _selectedProductId,
        );
      } else {
        _monthlyReport = await repository.getMonthlyReport(
          month: _selectedMonth,
          year: _selectedYear,
          villageId: _selectedVillageId,
          shopId: _selectedShopId,
          productId: _selectedProductId,
        );
      }
      _status = ReportStateStatus.loaded;
    } on ForbiddenException catch (e) {
      _status = ReportStateStatus.forbidden;
      _errorMessage = e.message.isNotEmpty
          ? e.message
          : 'Access denied: Reports are restricted to Super Admins and Managers.';
    } on ApiException catch (e) {
      _status = ReportStateStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = ReportStateStatus.error;
      _errorMessage = 'Failed to load report data. Please try again.';
    }
    notifyListeners();
  }
}
