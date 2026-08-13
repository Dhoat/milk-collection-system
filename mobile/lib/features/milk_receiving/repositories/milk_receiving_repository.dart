import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/milk_receiving_model.dart';
import 'milk_receiving_repository_interface.dart';

class MilkReceivingRepository implements IMilkReceivingRepository {
  final ApiClient apiClient;

  MilkReceivingRepository({required this.apiClient});

  @override
  Future<List<MilkReceivingModel>> getMilkReceivings({
    String? date,
    int? villageId,
    String? shift,
    String? status,
    int page = 1,
  }) async {
    final queryParams = <String, String>{};
    if (date != null && date.isNotEmpty) queryParams['date'] = date;
    if (villageId != null) queryParams['village_id'] = villageId.toString();
    if (shift != null && shift.isNotEmpty) queryParams['shift'] = shift;
    if (status != null && status.isNotEmpty) queryParams['status'] = status;
    queryParams['page'] = page.toString();

    final response = await apiClient.get(
      ApiConstants.milkReceivings,
      queryParameters: queryParams,
    );

    final List<dynamic> data = response['data'] ?? [];
    return data.map((json) => MilkReceivingModel.fromJson(json)).toList();
  }

  @override
  Future<MilkReceivingModel> getMilkReceiving(int id) async {
    final response = await apiClient.get('${ApiConstants.milkReceivings}/$id');
    return MilkReceivingModel.fromJson(response['data']);
  }

  @override
  Future<MilkReceivingModel> createMilkReceiving(Map<String, dynamic> data) async {
    final response = await apiClient.post(
      ApiConstants.milkReceivings,
      body: data,
    );
    return MilkReceivingModel.fromJson(response['data']);
  }

  @override
  Future<MilkReceivingModel> updateMilkReceiving(
    int id,
    Map<String, dynamic> data,
  ) async {
    final response = await apiClient.put(
      '${ApiConstants.milkReceivings}/$id',
      body: data,
    );
    return MilkReceivingModel.fromJson(response['data']);
  }

  @override
  Future<void> deleteMilkReceiving(int id) async {
    await apiClient.delete('${ApiConstants.milkReceivings}/$id');
  }

  @override
  Future<Map<String, dynamic>> getCollectionSummary({
    required int villageId,
    required String date,
    required String shift,
  }) async {
    final response = await apiClient.get(
      ApiConstants.milkReceivingsSummary,
      queryParameters: {
        'village_id': villageId.toString(),
        'date': date,
        'shift': shift,
      },
    );
    return response['data'] ?? {};
  }
}
