import '../models/product_model.dart';
import '../models/shop_order_model.dart';

abstract class IShopOrderRepository {
  Future<List<ShopOrderModel>> getShopOrders({
    String? search,
    String? status,
    int? shopId,
    String? date,
    int page = 1,
  });

  Future<List<ProductModel>> getProducts();

  Future<ShopOrderModel> getShopOrder(int id);

  Future<ShopOrderModel> createShopOrder(Map<String, dynamic> data);

  Future<ShopOrderModel> updateShopOrderStatus(int id, String status);

  Future<void> deleteShopOrder(int id);
}
