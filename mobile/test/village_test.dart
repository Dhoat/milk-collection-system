import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/villages/models/village_model.dart';
import 'package:mobile/features/villages/providers/village_provider.dart';
import 'package:mobile/features/villages/repositories/village_repository.dart';

class MockVillageApiClient implements ApiClient {
  Map<String, dynamic>? mockGetResponse;
  Map<String, dynamic>? mockPostResponse;
  Map<String, dynamic>? mockPutResponse;
  bool shouldThrow = false;

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    if (shouldThrow) throw Exception('Get Villages Failed');
    return mockGetResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrow) throw Exception('Create Village Failed');
    return mockPostResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrow) throw Exception('Update Village Failed');
    return mockPutResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> delete(String path) async {
    if (shouldThrow) throw Exception('Delete Village Failed');
    return {'success': true, 'data': null};
  }

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();
}

void main() {
  final sampleVillageJson = {
    'id': 1,
    'name': 'Binjoki Kalan',
    'code': 'VIL-001',
    'address': 'Tehsil Ahmedgarh',
    'status': true,
    'farmers_count': 12,
    'created_at': '2026-08-11T06:31:51+00:00',
    'updated_at': '2026-08-11T06:31:51+00:00',
  };

  group('VillageModel Unit Tests', () {
    test('VillageModel.fromJson parses backend Resource JSON correctly', () {
      final village = VillageModel.fromJson(sampleVillageJson);

      expect(village.id, 1);
      expect(village.name, 'Binjoki Kalan');
      expect(village.code, 'VIL-001');
      expect(village.address, 'Tehsil Ahmedgarh');
      expect(village.status, isTrue);
      expect(village.farmersCount, 12);
    });
  });

  group('VillageRepository Unit Tests', () {
    test('getVillages returns list of VillageModel on success', () async {
      final mockApi = MockVillageApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleVillageJson],
      };

      final repo = VillageRepository(apiClient: mockApi);
      final list = await repo.getVillages();

      expect(list.length, 1);
      expect(list.first.name, 'Binjoki Kalan');
    });

    test('createVillage returns created VillageModel', () async {
      final mockApi = MockVillageApiClient();
      mockApi.mockPostResponse = {
        'success': true,
        'data': sampleVillageJson,
      };

      final repo = VillageRepository(apiClient: mockApi);
      final village = await repo.createVillage(name: 'Binjoki Kalan', code: 'VIL-001');

      expect(village.code, 'VIL-001');
    });
  });

  group('VillageProvider State Tests', () {
    test('fetchVillages updates state to loaded', () async {
      final mockApi = MockVillageApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleVillageJson],
      };

      final repo = VillageRepository(apiClient: mockApi);
      final provider = VillageProvider(villageRepository: repo);

      await provider.fetchVillages();

      expect(provider.status, VillageStatus.loaded);
      expect(provider.villages.length, 1);
    });

    test('search query filters villages locally', () async {
      final mockApi = MockVillageApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [
          sampleVillageJson,
          {
            'id': 2,
            'name': 'Rampur',
            'code': 'VIL-002',
            'status': true,
          }
        ],
      };

      final repo = VillageRepository(apiClient: mockApi);
      final provider = VillageProvider(villageRepository: repo);

      await provider.fetchVillages();
      expect(provider.villages.length, 2);

      provider.setSearchQuery('Binjoki');
      expect(provider.villages.length, 1);
      expect(provider.villages.first.name, 'Binjoki Kalan');
    });
  });
}
