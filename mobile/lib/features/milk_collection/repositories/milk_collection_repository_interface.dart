import '../models/milk_collection_model.dart';

abstract class IMilkCollectionRepository {
  Future<List<MilkCollectionModel>> getMilkCollections({
    String? search,
    int? villageId,
    int? farmerId,
    String? date,
    String? shift,
    int page = 1,
  });

  Future<MilkCollectionModel> getMilkCollection(int id);

  Future<MilkCollectionModel> createMilkCollection(Map<String, dynamic> data);

  Future<MilkCollectionModel> updateMilkCollection(
    int id,
    Map<String, dynamic> data,
  );

  Future<void> deleteMilkCollection(int id);
}
