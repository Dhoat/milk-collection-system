import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/milk_stock_model.dart';
import 'milk_stock_repository_interface.dart';

class MilkStockRepository implements IMilkStockRepository {
  final ApiClient apiClient;

  MilkStockRepository({required this.apiClient});

  @override
  Future<Map<String, dynamic>> getMilkStocks({
    String? date,
    String? type,
    String? search,
    int page = 1,
  }) async {
    final queryParams = <String, String>{};
    if (date != null && date.isNotEmpty) queryParams['date'] = date;
    if (type != null && type.isNotEmpty) queryParams['type'] = type;
    if (search != null && search.isNotEmpty) queryParams['search'] = search;
    queryParams['page'] = page.toString();

    final response = await apiClient.get(
      ApiConstants.milkStocks,
      queryParameters: queryParams,
    );

    final summaryData = response['summary'] ?? {};
    final List<dynamic> listData = response['data'] ?? [];

    final summary = MilkStockSummaryModel.fromJson(summaryData);
    final transactions = listData.map((json) => MilkStockModel.fromJson(json)).toList();

    return {
      'summary': summary,
      'transactions': transactions,
    };
  }

  @override
  Future<MilkStockModel> getMilkStock(int id) async {
    final response = await apiClient.get('${ApiConstants.milkStocks}/$id');
    return MilkStockModel.fromJson(response['data']);
  }

  @override
  Future<MilkStockModel> recordStockOut(Map<String, dynamic> data) async {
    final response = await apiClient.post(
      ApiConstants.milkStocksOut,
      body: data,
    );
    return MilkStockModel.fromJson(response['data']);
  }
}
