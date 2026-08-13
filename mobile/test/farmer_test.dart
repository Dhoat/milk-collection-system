import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/farmers/models/farmer_model.dart';
import 'package:mobile/features/farmers/providers/farmer_provider.dart';
import 'package:mobile/features/farmers/repositories/farmer_repository.dart';

class MockFarmerApiClient implements ApiClient {
  Map<String, dynamic>? mockGetResponse;
  Map<String, dynamic>? mockPostResponse;
  bool shouldThrow = false;

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    if (shouldThrow) throw Exception('Get Farmers Failed');
    return mockGetResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrow) throw Exception('Create Farmer Failed');
    return mockPostResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();

  @override
  Future<Map<String, dynamic>> delete(String path) async {
    if (shouldThrow) throw Exception('Delete Farmer Failed');
    return {'success': true, 'data': null};
  }

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();
}

void main() {
  final sampleFarmerJson = {
    'id': 1,
    'village_id': 1,
    'farmer_code': 'FRM-VIL-001-01',
    'name': 'Gurdeep Singh',
    'father_name': 'Harbans Singh',
    'mobile': '9870100111',
    'alternate_mobile': null,
    'address': 'Binjoki Kalan',
    'gender': 'male',
    'joining_date': '2026-01-01',
    'bank_name': 'State Bank of India',
    'account_number': '305900010000101',
    'ifsc_code': 'SBIN0000590',
    'status': true,
    'village': {
      'id': 1,
      'name': 'Binjoki Kalan',
      'code': 'VIL-001',
      'status': true,
    },
  };

  group('FarmerModel Unit Tests', () {
    test('FarmerModel.fromJson parses FarmerResource JSON correctly with village relationship', () {
      final farmer = FarmerModel.fromJson(sampleFarmerJson);

      expect(farmer.id, 1);
      expect(farmer.name, 'Gurdeep Singh');
      expect(farmer.villageId, 1);
      expect(farmer.village?.name, 'Binjoki Kalan');
      expect(farmer.mobile, '9870100111');
      expect(farmer.status, isTrue);
    });
  });

  group('FarmerRepository Unit Tests', () {
    test('getFarmers returns list of FarmerModel', () async {
      final mockApi = MockFarmerApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleFarmerJson],
      };

      final repo = FarmerRepository(apiClient: mockApi);
      final list = await repo.getFarmers();

      expect(list.length, 1);
      expect(list.first.name, 'Gurdeep Singh');
    });
  });

  group('FarmerProvider State & Relationship Tests', () {
    test('fetchFarmers updates state to loaded', () async {
      final mockApi = MockFarmerApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleFarmerJson],
      };

      final repo = FarmerRepository(apiClient: mockApi);
      final provider = FarmerProvider(farmerRepository: repo);

      await provider.fetchFarmers();

      expect(provider.status, FarmerStatus.loaded);
      expect(provider.farmers.length, 1);
    });

    test('village filter isolates farmers belonging to specific village', () async {
      final mockApi = MockFarmerApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [
          sampleFarmerJson,
          {
            'id': 2,
            'village_id': 2,
            'farmer_code': 'FRM-VIL-002-01',
            'name': 'Amarjit Singh',
            'mobile': '9870100222',
            'status': true,
            'village': {
              'id': 2,
              'name': 'Rampur',
              'code': 'VIL-002',
              'status': true,
            }
          }
        ],
      };

      final repo = FarmerRepository(apiClient: mockApi);
      final provider = FarmerProvider(farmerRepository: repo);

      await provider.fetchFarmers();
      expect(provider.farmers.length, 2);

      provider.setVillageFilter(1);
      expect(provider.farmers.length, 1);
      expect(provider.farmers.first.name, 'Gurdeep Singh');
    });
  });
}
