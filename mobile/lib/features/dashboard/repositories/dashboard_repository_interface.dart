import '../models/dashboard_model.dart';

abstract class IDashboardRepository {
  Future<DashboardModel> getDashboard({String? date});
}
