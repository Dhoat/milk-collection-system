import 'package:flutter_test/flutter_test.dart';

import 'package:mobile/core/errors/api_exception.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/shops/models/shop_model.dart';
import 'package:mobile/features/shops/providers/shop_provider.dart';
import 'package:mobile/features/shops/repositories/shop_repository.dart';

class MockShopApiClient implements ApiClient {
  Map<String, dynamic>? mockGetResponse;
  Map<String, dynamic>? mockPostResponse;
  Map<String, dynamic>? mockPutResponse;
  Map<String, dynamic>? mockPatchResponse;
  bool shouldThrowValidation = false;

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    return mockGetResponse ?? {'success': true, 'data': []};
  }

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrowValidation) {
      throw ValidationException(
        message: 'The shop code has already been taken.',
        errors: {
          'shop_code': ['The shop code has already been taken.']
        },
      );
    }
    return mockPostResponse ?? {'success': true, 'data': {}};
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async {
    return mockPutResponse ?? {'success': true, 'data': {}};
  }

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async {
    return mockPatchResponse ?? {'success': true, 'data': {}};
  }

  @override
  Future<Map<String, dynamic>> delete(String path) async {
    return {'success': true, 'data': null};
  }
}

void main() {
  final sampleShopJson = {
    'id': 10,
    'shop_code': 'SHP-101',
    'name': 'Green Dairy Store',
    'owner_name': 'Ramesh Kumar',
    'phone': '9876543210',
    'email': 'green@dairy.com',
    'village_id': 2,
    'area': 'North Zone',
    'address': 'Main Market Road',
    'status': true,
    'credit_limit': 15000.0,
    'notes': 'Daily delivery',
    'village': {
      'id': 2,
      'name': 'Dehliz Khurd',
      'code': '2',
      'address': 'Dehliz Khurd',
      'status': true,
    },
    'created_at': '2026-08-13T10:00:00Z',
    'updated_at': '2026-08-13T10:00:00Z',
  };

  group('ShopModel Unit Tests', () {
    test('ShopModel.fromJson parses JSON correctly', () {
      final shop = ShopModel.fromJson(sampleShopJson);

      expect(shop.id, equals(10));
      expect(shop.shopCode, equals('SHP-101'));
      expect(shop.name, equals('Green Dairy Store'));
      expect(shop.ownerName, equals('Ramesh Kumar'));
      expect(shop.phone, equals('9876543210'));
      expect(shop.status, isTrue);
      expect(shop.statusDisplayName, equals('Active'));
      expect(shop.creditLimit, equals(15000.0));
      expect(shop.village?.name, equals('Dehliz Khurd'));
    });
  });

  group('ShopRepository Unit Tests', () {
    test('getShops returns list of ShopModel', () async {
      final mockApi = MockShopApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleShopJson],
      };

      final repository = ShopRepository(apiClient: mockApi);
      final shops = await repository.getShops();

      expect(shops.length, equals(1));
      expect(shops.first.name, equals('Green Dairy Store'));
    });

    test('createShop posts payload and returns created ShopModel', () async {
      final mockApi = MockShopApiClient();
      mockApi.mockPostResponse = {
        'success': true,
        'data': {...sampleShopJson, 'id': 11, 'name': 'New Shop'},
      };

      final repository = ShopRepository(apiClient: mockApi);
      final shop = await repository.createShop({
        'shop_code': 'SHP-102',
        'name': 'New Shop',
        'owner_name': 'Suresh',
        'phone': '9998887770',
        'status': true,
      });

      expect(shop.id, equals(11));
      expect(shop.name, equals('New Shop'));
    });

    test('toggleShopStatus patches status endpoint', () async {
      final mockApi = MockShopApiClient();
      mockApi.mockPatchResponse = {
        'success': true,
        'data': {...sampleShopJson, 'status': false},
      };

      final repository = ShopRepository(apiClient: mockApi);
      final shop = await repository.toggleShopStatus(10);

      expect(shop.status, isFalse);
    });
  });

  group('ShopProvider State & Search Tests', () {
    test('fetchShops updates state to loaded', () async {
      final mockApi = MockShopApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleShopJson],
      };

      final repository = ShopRepository(apiClient: mockApi);
      final provider = ShopProvider(repository: repository);

      expect(provider.status, equals(ShopStatus.initial));

      await provider.fetchShops();

      expect(provider.status, equals(ShopStatus.loaded));
      expect(provider.shops.length, equals(1));
      expect(provider.shops.first.name, equals('Green Dairy Store'));
    });

    test('createShop captures validation errors on 422 response', () async {
      final mockApi = MockShopApiClient();
      mockApi.shouldThrowValidation = true;

      final repository = ShopRepository(apiClient: mockApi);
      final provider = ShopProvider(repository: repository);

      final success = await provider.createShop({'shop_code': 'SHP-101'});

      expect(success, isFalse);
      expect(provider.errorMessage, equals('The shop code has already been taken.'));
      expect(provider.validationErrors?['shop_code']?.first, equals('The shop code has already been taken.'));
    });
  });
}
