import '../models/daily_report_model.dart';
import '../models/monthly_report_model.dart';

abstract class IReportRepository {
  Future<DailyReportModel> getDailyReport({
    String? date,
    int? villageId,
    int? shopId,
    int? productId,
  });

  Future<MonthlyReportModel> getMonthlyReport({
    int? month,
    int? year,
    int? villageId,
    int? shopId,
    int? productId,
  });
}
