import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/delivery_model.dart';
import 'delivery_repository_interface.dart';

class DeliveryRepository implements IDeliveryRepository {
  final ApiClient apiClient;

  DeliveryRepository({required this.apiClient});

  @override
  Future<List<DeliveryModel>> getDeliveries({
    String? search,
    String? status,
    int? shopId,
    int? assignedTo,
    String? date,
    int page = 1,
    int perPage = 15,
  }) async {
    final queryParams = <String, String>{
      'page': page.toString(),
      'per_page': perPage.toString(),
    };

    if (search != null && search.isNotEmpty) queryParams['search'] = search;
    if (status != null && status.isNotEmpty && status != 'all') queryParams['status'] = status;
    if (shopId != null) queryParams['shop_id'] = shopId.toString();
    if (assignedTo != null) queryParams['assigned_to'] = assignedTo.toString();
    if (date != null && date.isNotEmpty) queryParams['date'] = date;

    final response = await apiClient.get(
      ApiConstants.deliveries,
      queryParameters: queryParams,
    );

    final List<dynamic> data = response['data'] ?? [];
    return data.map((json) => DeliveryModel.fromJson(json)).toList();
  }

  @override
  Future<DeliveryModel> getDelivery(int id) async {
    final response = await apiClient.get('${ApiConstants.deliveries}/$id');
    return DeliveryModel.fromJson(response['data']);
  }

  @override
  Future<DeliveryModel> createDelivery(Map<String, dynamic> data) async {
    final response = await apiClient.post(
      ApiConstants.deliveries,
      body: data,
    );
    return DeliveryModel.fromJson(response['data']);
  }

  @override
  Future<DeliveryModel> updateDeliveryStatus(int id, String status, {int? assignedTo}) async {
    final payload = <String, dynamic>{
      'status': status,
    };
    if (assignedTo != null) {
      payload['assigned_to'] = assignedTo;
    }

    final response = await apiClient.patch(
      '${ApiConstants.deliveries}/$id/status',
      body: payload,
    );
    return DeliveryModel.fromJson(response['data']);
  }

  @override
  Future<void> deleteDelivery(int id) async {
    await apiClient.delete('${ApiConstants.deliveries}/$id');
  }
}
