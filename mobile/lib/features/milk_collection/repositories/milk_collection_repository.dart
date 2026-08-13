import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/milk_collection_model.dart';
import 'milk_collection_repository_interface.dart';

class MilkCollectionRepository implements IMilkCollectionRepository {
  final ApiClient apiClient;

  MilkCollectionRepository({required this.apiClient});

  @override
  Future<List<MilkCollectionModel>> getMilkCollections({
    String? search,
    int? villageId,
    int? farmerId,
    String? date,
    String? shift,
    int page = 1,
  }) async {
    final queryParams = <String, String>{};
    if (search != null && search.isNotEmpty) {
      queryParams['search'] = search;
    }
    if (villageId != null) {
      queryParams['village_id'] = villageId.toString();
    }
    if (farmerId != null) {
      queryParams['farmer_id'] = farmerId.toString();
    }
    if (date != null && date.isNotEmpty) {
      queryParams['date'] = date;
    }
    if (shift != null && shift.isNotEmpty) {
      queryParams['shift'] = shift;
    }
    queryParams['page'] = page.toString();

    final response = await apiClient.get(
      ApiConstants.milkCollections,
      queryParameters: queryParams,
    );

    final List<dynamic> data = response['data'] ?? [];
    return data.map((json) => MilkCollectionModel.fromJson(json)).toList();
  }

  @override
  Future<MilkCollectionModel> getMilkCollection(int id) async {
    final response = await apiClient.get('${ApiConstants.milkCollections}/$id');
    return MilkCollectionModel.fromJson(response['data']);
  }

  @override
  Future<MilkCollectionModel> createMilkCollection(Map<String, dynamic> data) async {
    final response = await apiClient.post(
      ApiConstants.milkCollections,
      body: data,
    );
    return MilkCollectionModel.fromJson(response['data']);
  }

  @override
  Future<MilkCollectionModel> updateMilkCollection(
    int id,
    Map<String, dynamic> data,
  ) async {
    final response = await apiClient.put(
      '${ApiConstants.milkCollections}/$id',
      body: data,
    );
    return MilkCollectionModel.fromJson(response['data']);
  }

  @override
  Future<void> deleteMilkCollection(int id) async {
    await apiClient.delete('${ApiConstants.milkCollections}/$id');
  }
}
