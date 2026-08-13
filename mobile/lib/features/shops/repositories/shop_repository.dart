import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/shop_model.dart';
import 'shop_repository_interface.dart';

class ShopRepository implements IShopRepository {
  final ApiClient apiClient;

  ShopRepository({required this.apiClient});

  @override
  Future<List<ShopModel>> getShops({
    String? search,
    bool? status,
    int? villageId,
    int page = 1,
  }) async {
    final queryParams = <String, String>{};
    if (search != null && search.isNotEmpty) queryParams['search'] = search;
    if (status != null) queryParams['status'] = status ? '1' : '0';
    if (villageId != null) queryParams['village_id'] = villageId.toString();
    queryParams['page'] = page.toString();

    final response = await apiClient.get(
      ApiConstants.shops,
      queryParameters: queryParams,
    );

    final List<dynamic> data = response['data'] ?? [];
    return data.map((json) => ShopModel.fromJson(json)).toList();
  }

  @override
  Future<ShopModel> getShop(int id) async {
    final response = await apiClient.get('${ApiConstants.shops}/$id');
    return ShopModel.fromJson(response['data']);
  }

  @override
  Future<ShopModel> createShop(Map<String, dynamic> data) async {
    final response = await apiClient.post(
      ApiConstants.shops,
      body: data,
    );
    return ShopModel.fromJson(response['data']);
  }

  @override
  Future<ShopModel> updateShop(int id, Map<String, dynamic> data) async {
    final response = await apiClient.put(
      '${ApiConstants.shops}/$id',
      body: data,
    );
    return ShopModel.fromJson(response['data']);
  }

  @override
  Future<ShopModel> toggleShopStatus(int id) async {
    final response = await apiClient.patch(
      '${ApiConstants.shops}/$id/toggle-status',
    );
    return ShopModel.fromJson(response['data']);
  }

  @override
  Future<void> deleteShop(int id) async {
    await apiClient.delete('${ApiConstants.shops}/$id');
  }
}
