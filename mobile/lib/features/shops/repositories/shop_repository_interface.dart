import '../models/shop_model.dart';

abstract class IShopRepository {
  Future<List<ShopModel>> getShops({
    String? search,
    bool? status,
    int? villageId,
    int page = 1,
  });

  Future<ShopModel> getShop(int id);

  Future<ShopModel> createShop(Map<String, dynamic> data);

  Future<ShopModel> updateShop(int id, Map<String, dynamic> data);

  Future<ShopModel> toggleShopStatus(int id);

  Future<void> deleteShop(int id);
}
