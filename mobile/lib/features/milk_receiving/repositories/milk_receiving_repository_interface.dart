import '../models/milk_receiving_model.dart';

abstract class IMilkReceivingRepository {
  Future<List<MilkReceivingModel>> getMilkReceivings({
    String? date,
    int? villageId,
    String? shift,
    String? status,
    int page = 1,
  });

  Future<MilkReceivingModel> getMilkReceiving(int id);

  Future<MilkReceivingModel> createMilkReceiving(Map<String, dynamic> data);

  Future<MilkReceivingModel> updateMilkReceiving(
    int id,
    Map<String, dynamic> data,
  );

  Future<void> deleteMilkReceiving(int id);

  Future<Map<String, dynamic>> getCollectionSummary({
    required int villageId,
    required String date,
    required String shift,
  });
}
