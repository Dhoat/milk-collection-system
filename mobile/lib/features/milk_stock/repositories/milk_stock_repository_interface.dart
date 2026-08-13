import '../models/milk_stock_model.dart';

abstract class IMilkStockRepository {
  Future<Map<String, dynamic>> getMilkStocks({
    String? date,
    String? type,
    String? search,
    int page = 1,
  });

  Future<MilkStockModel> getMilkStock(int id);

  Future<MilkStockModel> recordStockOut(Map<String, dynamic> data);
}
