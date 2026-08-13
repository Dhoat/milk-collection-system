import '../models/delivery_model.dart';

abstract class IDeliveryRepository {
  Future<List<DeliveryModel>> getDeliveries({
    String? search,
    String? status,
    int? shopId,
    int? assignedTo,
    String? date,
    int page = 1,
    int perPage = 15,
  });

  Future<DeliveryModel> getDelivery(int id);

  Future<DeliveryModel> createDelivery(Map<String, dynamic> data);

  Future<DeliveryModel> updateDeliveryStatus(int id, String status, {int? assignedTo});

  Future<void> deleteDelivery(int id);
}
