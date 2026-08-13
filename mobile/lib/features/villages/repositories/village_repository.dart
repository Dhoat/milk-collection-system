import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/village_model.dart';
import 'village_repository_interface.dart';

class VillageRepository implements IVillageRepository {
  final ApiClient apiClient;

  VillageRepository({required this.apiClient});

  @override
  Future<List<VillageModel>> getVillages({
    String? search,
    bool? status,
    int page = 1,
    int perPage = 100, // Load comprehensive list for select dropdowns & list
  }) async {
    final queryParams = <String, String>{
      'page': page.toString(),
      'per_page': perPage.toString(),
    };
    if (search != null && search.trim().isNotEmpty) {
      queryParams['search'] = search.trim();
    }
    if (status != null) {
      queryParams['status'] = status.toString();
    }

    final response = await apiClient.get(
      ApiConstants.villages,
      queryParameters: queryParams,
    );

    final rawList = response['data'] as List<dynamic>? ?? [];
    return rawList
        .map((item) => VillageModel.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  @override
  Future<VillageModel> getVillage(int id) async {
    final response = await apiClient.get('${ApiConstants.villages}/$id');
    final data = response['data'] as Map<String, dynamic>;
    return VillageModel.fromJson(data);
  }

  @override
  Future<VillageModel> createVillage({
    required String name,
    required String code,
    String? address,
    bool status = true,
  }) async {
    final response = await apiClient.post(
      ApiConstants.villages,
      body: {
        'name': name,
        'code': code,
        'address': address,
        'status': status,
      },
    );
    final data = response['data'] as Map<String, dynamic>;
    return VillageModel.fromJson(data);
  }

  @override
  Future<VillageModel> updateVillage({
    required int id,
    required String name,
    required String code,
    String? address,
    bool status = true,
  }) async {
    final response = await apiClient.put(
      '${ApiConstants.villages}/$id',
      body: {
        'name': name,
        'code': code,
        'address': address,
        'status': status,
      },
    );
    final data = response['data'] as Map<String, dynamic>;
    return VillageModel.fromJson(data);
  }

  @override
  Future<void> deleteVillage(int id) async {
    await apiClient.delete('${ApiConstants.villages}/$id');
  }
}
