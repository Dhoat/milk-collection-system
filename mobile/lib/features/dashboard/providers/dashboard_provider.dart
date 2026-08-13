import 'package:flutter/foundation.dart';
import '../../../core/errors/api_exception.dart';
import '../models/dashboard_model.dart';
import '../repositories/dashboard_repository_interface.dart';

enum DashboardStatus { initial, loading, loaded, error }

class DashboardProvider extends ChangeNotifier {
  final IDashboardRepository dashboardRepository;

  DashboardStatus _status = DashboardStatus.initial;
  DashboardModel? _dashboardData;
  String? _errorMessage;

  DashboardProvider({required this.dashboardRepository});

  DashboardStatus get status => _status;
  DashboardModel? get dashboardData => _dashboardData;
  String? get errorMessage => _errorMessage;
  bool get isLoading => _status == DashboardStatus.loading;
  bool get hasError => _status == DashboardStatus.error;
  bool get isLoaded => _status == DashboardStatus.loaded && _dashboardData != null;

  Future<void> fetchDashboard({String? date}) async {
    _status = DashboardStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      _dashboardData = await dashboardRepository.getDashboard(date: date);
      _status = DashboardStatus.loaded;
    } on ApiException catch (e) {
      _status = DashboardStatus.error;
      _errorMessage = e.message;
    } catch (e) {
      _status = DashboardStatus.error;
      _errorMessage = 'Failed to load dashboard data. Please try again.';
    }

    notifyListeners();
  }

  Future<void> refresh() async {
    await fetchDashboard();
  }
}
