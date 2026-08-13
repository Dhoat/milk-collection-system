import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/product_model.dart';
import '../models/shop_order_model.dart';
import 'shop_order_repository_interface.dart';

class ShopOrderRepository implements IShopOrderRepository {
  final ApiClient apiClient;

  ShopOrderRepository({required this.apiClient});

  @override
  Future<List<ShopOrderModel>> getShopOrders({
    String? search,
    String? status,
    int? shopId,
    String? date,
    int page = 1,
  }) async {
    final queryParams = <String, String>{};
    if (search != null && search.isNotEmpty) queryParams['search'] = search;
    if (status != null && status.isNotEmpty) queryParams['status'] = status;
    if (shopId != null) queryParams['shop_id'] = shopId.toString();
    if (date != null && date.isNotEmpty) queryParams['date'] = date;
    queryParams['page'] = page.toString();

    final response = await apiClient.get(
      ApiConstants.shopOrders,
      queryParameters: queryParams,
    );

    final List<dynamic> data = response['data'] ?? [];
    return data.map((json) => ShopOrderModel.fromJson(json)).toList();
  }

  @override
  Future<List<ProductModel>> getProducts() async {
    final response = await apiClient.get(ApiConstants.products);
    final List<dynamic> data = response['data'] ?? [];
    return data.map((json) => ProductModel.fromJson(json)).toList();
  }

  @override
  Future<ShopOrderModel> getShopOrder(int id) async {
    final response = await apiClient.get('${ApiConstants.shopOrders}/$id');
    return ShopOrderModel.fromJson(response['data']);
  }

  @override
  Future<ShopOrderModel> createShopOrder(Map<String, dynamic> data) async {
    final response = await apiClient.post(
      ApiConstants.shopOrders,
      body: data,
    );
    return ShopOrderModel.fromJson(response['data']);
  }

  @override
  Future<ShopOrderModel> updateShopOrderStatus(int id, String status) async {
    final response = await apiClient.patch(
      '${ApiConstants.shopOrders}/$id/status',
      body: {'status': status},
    );
    return ShopOrderModel.fromJson(response['data']);
  }

  @override
  Future<void> deleteShopOrder(int id) async {
    await apiClient.delete('${ApiConstants.shopOrders}/$id');
  }
}
