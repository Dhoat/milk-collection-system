import '../models/farmer_model.dart';

abstract class IFarmerRepository {
  Future<List<FarmerModel>> getFarmers({
    String? search,
    int? villageId,
    bool? status,
    int page = 1,
    int perPage = 15,
  });

  Future<FarmerModel> getFarmer(int id);

  Future<FarmerModel> createFarmer(Map<String, dynamic> farmerData);

  Future<FarmerModel> updateFarmer(int id, Map<String, dynamic> farmerData);

  Future<void> deleteFarmer(int id);
}
