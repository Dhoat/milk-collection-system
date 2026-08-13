import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/milk_receiving/models/milk_receiving_model.dart';
import 'package:mobile/features/milk_receiving/providers/milk_receiving_provider.dart';
import 'package:mobile/features/milk_receiving/repositories/milk_receiving_repository.dart';

class MockMilkReceivingApiClient implements ApiClient {
  Map<String, dynamic>? mockGetResponse;
  Map<String, dynamic>? mockPostResponse;
  Map<String, dynamic>? mockPutResponse;
  bool shouldThrow = false;

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    if (shouldThrow) throw Exception('Get Milk Receivings Failed');
    if (path.contains('/summary')) {
      return {
        'success': true,
        'data': {
          'expected_quantity': 150.0,
          'expected_fat': 4.5,
          'expected_snf': 8.5,
          'farmer_count': 10,
        }
      };
    }
    return mockGetResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrow) throw Exception('Create Milk Receiving Failed');
    return mockPostResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrow) throw Exception('Update Milk Receiving Failed');
    return mockPutResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> delete(String path) async {
    if (shouldThrow) throw Exception('Delete Milk Receiving Failed');
    return {'success': true, 'data': null};
  }

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();
}

void main() {
  final sampleReceivingJson = {
    'id': 15,
    'village_id': 9,
    'receiving_date': '2026-08-13',
    'shift': 'morning',
    'expected_quantity': 150.0,
    'received_quantity': 148.5,
    'quantity_variance': -1.5,
    'quantity_variance_percent': -1.0,
    'expected_fat': 4.5,
    'received_fat': 4.4,
    'expected_snf': 8.5,
    'received_snf': 8.4,
    'status': 'discrepancy',
    'verified_by': 1,
    'notes': 'Tanker arrived on time',
    'village': {
      'id': 9,
      'name': 'Maholi Kalan',
      'code': 'VIL-008',
      'status': true,
    },
    'created_at': '2026-08-13T09:00:00+00:00',
    'updated_at': '2026-08-13T09:00:00+00:00',
  };

  group('MilkReceivingModel Unit Tests', () {
    test('MilkReceivingModel.fromJson parses backend MilkReceivingResource JSON correctly', () {
      final receiving = MilkReceivingModel.fromJson(sampleReceivingJson);

      expect(receiving.id, 15);
      expect(receiving.villageId, 9);
      expect(receiving.receivingDate, '2026-08-13');
      expect(receiving.shift, 'morning');
      expect(receiving.expectedQuantity, 150.0);
      expect(receiving.receivedQuantity, 148.5);
      expect(receiving.quantityVariance, -1.5);
      expect(receiving.status, 'discrepancy');
      expect(receiving.hasDiscrepancy, true);
      expect(receiving.village?.name, 'Maholi Kalan');
    });
  });

  group('MilkReceivingRepository Unit Tests', () {
    test('getMilkReceivings returns list of MilkReceivingModel', () async {
      final mockApi = MockMilkReceivingApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleReceivingJson],
      };

      final repo = MilkReceivingRepository(apiClient: mockApi);
      final list = await repo.getMilkReceivings();

      expect(list.length, 1);
      expect(list.first.id, 15);
      expect(list.first.receivedQuantity, 148.5);
    });

    test('getCollectionSummary returns summary map', () async {
      final mockApi = MockMilkReceivingApiClient();
      final repo = MilkReceivingRepository(apiClient: mockApi);

      final summary = await repo.getCollectionSummary(
        villageId: 9,
        date: '2026-08-13',
        shift: 'morning',
      );

      expect(summary['expected_quantity'], 150.0);
      expect(summary['farmer_count'], 10);
    });
  });

  group('MilkReceivingProvider State Tests', () {
    test('fetchMilkReceivings updates state to loaded', () async {
      final mockApi = MockMilkReceivingApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleReceivingJson],
      };

      final repo = MilkReceivingRepository(apiClient: mockApi);
      final provider = MilkReceivingProvider(repository: repo);

      await provider.fetchMilkReceivings();

      expect(provider.status, MilkReceivingStatus.loaded);
      expect(provider.receivings.length, 1);
    });
  });
}
