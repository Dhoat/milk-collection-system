import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/daily_report_model.dart';
import '../models/monthly_report_model.dart';
import 'report_repository_interface.dart';

class ReportRepository implements IReportRepository {
  final ApiClient apiClient;

  ReportRepository({required this.apiClient});

  @override
  Future<DailyReportModel> getDailyReport({
    String? date,
    int? villageId,
    int? shopId,
    int? productId,
  }) async {
    final queryParams = <String, String>{};
    if (date != null && date.isNotEmpty) queryParams['date'] = date;
    if (villageId != null) queryParams['village_id'] = villageId.toString();
    if (shopId != null) queryParams['shop_id'] = shopId.toString();
    if (productId != null) queryParams['product_id'] = productId.toString();

    final response = await apiClient.get(
      ApiConstants.dailyReport,
      queryParameters: queryParams,
    );

    return DailyReportModel.fromJson(response['data']);
  }

  @override
  Future<MonthlyReportModel> getMonthlyReport({
    int? month,
    int? year,
    int? villageId,
    int? shopId,
    int? productId,
  }) async {
    final queryParams = <String, String>{};
    if (month != null) queryParams['month'] = month.toString();
    if (year != null) queryParams['year'] = year.toString();
    if (villageId != null) queryParams['village_id'] = villageId.toString();
    if (shopId != null) queryParams['shop_id'] = shopId.toString();
    if (productId != null) queryParams['product_id'] = productId.toString();

    final response = await apiClient.get(
      ApiConstants.monthlyReport,
      queryParameters: queryParams,
    );

    return MonthlyReportModel.fromJson(response['data']);
  }
}
