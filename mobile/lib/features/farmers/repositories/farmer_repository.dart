import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/farmer_model.dart';
import 'farmer_repository_interface.dart';

class FarmerRepository implements IFarmerRepository {
  final ApiClient apiClient;

  FarmerRepository({required this.apiClient});

  @override
  Future<List<FarmerModel>> getFarmers({
    String? search,
    int? villageId,
    bool? status,
    int page = 1,
    int perPage = 100,
  }) async {
    final queryParams = <String, String>{
      'page': page.toString(),
      'per_page': perPage.toString(),
    };
    if (search != null && search.trim().isNotEmpty) {
      queryParams['search'] = search.trim();
    }
    if (villageId != null) {
      queryParams['village_id'] = villageId.toString();
    }
    if (status != null) {
      queryParams['status'] = status.toString();
    }

    final response = await apiClient.get(
      ApiConstants.farmers,
      queryParameters: queryParams,
    );

    final rawList = response['data'] as List<dynamic>? ?? [];
    return rawList
        .map((item) => FarmerModel.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  @override
  Future<FarmerModel> getFarmer(int id) async {
    final response = await apiClient.get('${ApiConstants.farmers}/$id');
    final data = response['data'] as Map<String, dynamic>;
    return FarmerModel.fromJson(data);
  }

  @override
  Future<FarmerModel> createFarmer(Map<String, dynamic> farmerData) async {
    final response = await apiClient.post(
      ApiConstants.farmers,
      body: farmerData,
    );
    final data = response['data'] as Map<String, dynamic>;
    return FarmerModel.fromJson(data);
  }

  @override
  Future<FarmerModel> updateFarmer(int id, Map<String, dynamic> farmerData) async {
    final response = await apiClient.put(
      '${ApiConstants.farmers}/$id',
      body: farmerData,
    );
    final data = response['data'] as Map<String, dynamic>;
    return FarmerModel.fromJson(data);
  }

  @override
  Future<void> deleteFarmer(int id) async {
    await apiClient.delete('${ApiConstants.farmers}/$id');
  }
}
