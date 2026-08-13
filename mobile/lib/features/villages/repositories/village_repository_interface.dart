import '../models/village_model.dart';

abstract class IVillageRepository {
  Future<List<VillageModel>> getVillages({
    String? search,
    bool? status,
    int page = 1,
    int perPage = 15,
  });

  Future<VillageModel> getVillage(int id);

  Future<VillageModel> createVillage({
    required String name,
    required String code,
    String? address,
    bool status = true,
  });

  Future<VillageModel> updateVillage({
    required int id,
    required String name,
    required String code,
    String? address,
    bool status = true,
  });

  Future<void> deleteVillage(int id);
}
