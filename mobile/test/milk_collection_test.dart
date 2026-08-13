import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/milk_collection/models/milk_collection_model.dart';
import 'package:mobile/features/milk_collection/providers/milk_collection_provider.dart';
import 'package:mobile/features/milk_collection/repositories/milk_collection_repository.dart';

class MockMilkCollectionApiClient implements ApiClient {
  Map<String, dynamic>? mockGetResponse;
  Map<String, dynamic>? mockPostResponse;
  Map<String, dynamic>? mockPutResponse;
  bool shouldThrow = false;

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    if (shouldThrow) throw Exception('Get Milk Collections Failed');
    return mockGetResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrow) throw Exception('Create Milk Collection Failed');
    return mockPostResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrow) throw Exception('Update Milk Collection Failed');
    return mockPutResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> delete(String path) async {
    if (shouldThrow) throw Exception('Delete Milk Collection Failed');
    return {'success': true, 'data': null};
  }

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();
}

void main() {
  final sampleCollectionJson = {
    'id': 10,
    'farmer_id': 89,
    'collection_date': '2026-08-13',
    'shift': 'morning',
    'milk_quantity': 25.5,
    'fat': 4.5,
    'snf': 8.5,
    'rate': 42.0,
    'amount': 1071.0,
    'notes': 'Fresh morning collection',
    'farmer': {
      'id': 89,
      'village_id': 9,
      'farmer_code': 'FRM-VIL-008-08',
      'name': 'Mandeep Singh',
      'mobile': '9870900888',
      'status': true,
      'village': {
        'id': 9,
        'name': 'Maholi Kalan',
        'code': 'VIL-008',
        'status': true,
      },
    },
    'created_at': '2026-08-13T09:00:00+00:00',
    'updated_at': '2026-08-13T09:00:00+00:00',
  };

  group('MilkCollectionModel Unit Tests', () {
    test('MilkCollectionModel.fromJson parses backend MilkCollectionResource JSON correctly', () {
      final collection = MilkCollectionModel.fromJson(sampleCollectionJson);

      expect(collection.id, 10);
      expect(collection.farmerId, 89);
      expect(collection.collectionDate, '2026-08-13');
      expect(collection.shift, 'morning');
      expect(collection.milkQuantity, 25.5);
      expect(collection.fat, 4.5);
      expect(collection.snf, 8.5);
      expect(collection.rate, 42.0);
      expect(collection.amount, 1071.0);
      expect(collection.farmer?.name, 'Mandeep Singh');
      expect(collection.farmer?.village?.name, 'Maholi Kalan');
    });

    test('MilkCollectionModel calculates estimated preview amount correctly', () {
      final collection = MilkCollectionModel.fromJson(sampleCollectionJson);
      expect(collection.milkQuantity * collection.rate, 1071.0);
    });
  });

  group('MilkCollectionRepository Unit Tests', () {
    test('getMilkCollections returns list of MilkCollectionModel', () async {
      final mockApi = MockMilkCollectionApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleCollectionJson],
      };

      final repo = MilkCollectionRepository(apiClient: mockApi);
      final list = await repo.getMilkCollections();

      expect(list.length, 1);
      expect(list.first.id, 10);
      expect(list.first.amount, 1071.0);
    });

    test('createMilkCollection posts payload and returns created MilkCollectionModel', () async {
      final mockApi = MockMilkCollectionApiClient();
      mockApi.mockPostResponse = {
        'success': true,
        'data': sampleCollectionJson,
      };

      final repo = MilkCollectionRepository(apiClient: mockApi);
      final collection = await repo.createMilkCollection({
        'farmer_id': 89,
        'collection_date': '2026-08-13',
        'shift': 'morning',
        'milk_quantity': 25.5,
        'rate': 42.0,
      });

      expect(collection.id, 10);
      expect(collection.milkQuantity, 25.5);
    });
  });

  group('MilkCollectionProvider State & Filter Tests', () {
    test('fetchMilkCollections updates state to loaded', () async {
      final mockApi = MockMilkCollectionApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleCollectionJson],
      };

      final repo = MilkCollectionRepository(apiClient: mockApi);
      final provider = MilkCollectionProvider(repository: repo);

      await provider.fetchMilkCollections();

      expect(provider.status, MilkCollectionStatus.loaded);
      expect(provider.collections.length, 1);
    });

    test('shift filter isolates morning and evening shift entries', () async {
      final mockApi = MockMilkCollectionApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [
          sampleCollectionJson,
          {
            'id': 11,
            'farmer_id': 89,
            'collection_date': '2026-08-13',
            'shift': 'evening',
            'milk_quantity': 30.0,
            'rate': 42.0,
            'amount': 1260.0,
          }
        ],
      };

      final repo = MilkCollectionRepository(apiClient: mockApi);
      final provider = MilkCollectionProvider(repository: repo);

      await provider.fetchMilkCollections();
      expect(provider.collections.length, 2);

      provider.setShiftFilter('morning');
      expect(provider.collections.length, 1);
      expect(provider.collections.first.shift, 'morning');
    });
  });
}
