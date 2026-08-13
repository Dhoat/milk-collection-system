import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/dashboard_model.dart';
import 'dashboard_repository_interface.dart';

class DashboardRepository implements IDashboardRepository {
  final ApiClient apiClient;

  DashboardRepository({required this.apiClient});

  @override
  Future<DashboardModel> getDashboard({String? date}) async {
    final queryParams = date != null ? {'date': date} : null;
    final response = await apiClient.get(
      ApiConstants.dashboard,
      queryParameters: queryParams,
    );

    final data = response['data'] as Map<String, dynamic>;
    return DashboardModel.fromJson(data);
  }
}
