import 'package:flutter_test/flutter_test.dart';

import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/shop_orders/models/product_model.dart';
import 'package:mobile/features/shop_orders/models/shop_order_model.dart';
import 'package:mobile/features/shop_orders/providers/shop_order_provider.dart';
import 'package:mobile/features/shop_orders/repositories/shop_order_repository.dart';

class MockShopOrderApiClient implements ApiClient {
  Map<String, dynamic>? mockGetResponse;
  Map<String, dynamic>? mockProductsResponse;
  Map<String, dynamic>? mockPostResponse;
  Map<String, dynamic>? mockPatchResponse;

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    if (path.contains('/products')) {
      return mockProductsResponse ?? {'success': true, 'data': []};
    }
    return mockGetResponse ?? {'success': true, 'data': []};
  }

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    return mockPostResponse ?? {'success': true, 'data': {}};
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();

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
  final sampleProductJson = {
    'id': 1,
    'product_code': 'PRD-RAW-MILK',
    'name': 'Raw Fresh Milk',
    'category': 'raw_milk',
    'unit': 'Litre',
    'unit_price': 60.0,
    'stock_quantity': 0.0,
    'available_stock': 500.0,
    'status': true,
    'notes': null,
  };

  final sampleShopOrderJson = {
    'id': 50,
    'order_number': 'ORD-2026-0050',
    'shop_id': 10,
    'order_date': '2026-08-13',
    'status': 'pending',
    'subtotal': 1200.0,
    'discount': 50.0,
    'total_amount': 1150.0,
    'stock_deducted': false,
    'created_by': 3,
    'notes': 'Urgent morning dispatch',
    'shop': {
      'id': 10,
      'shop_code': 'SHP-101',
      'name': 'Green Dairy Store',
      'owner_name': 'Ramesh Kumar',
      'phone': '9876543210',
      'status': true,
      'credit_limit': 15000.0,
    },
    'items': [
      {
        'id': 101,
        'shop_order_id': 50,
        'product_id': 1,
        'product_name': 'Raw Fresh Milk',
        'unit': 'Litre',
        'quantity': 20.0,
        'unit_price': 60.0,
        'line_total': 1200.0,
      }
    ],
    'created_at': '2026-08-13T10:00:00Z',
    'updated_at': '2026-08-13T10:00:00Z',
  };

  group('ShopOrderModel Unit Tests', () {
    test('ShopOrderModel.fromJson parses JSON correctly', () {
      final order = ShopOrderModel.fromJson(sampleShopOrderJson);

      expect(order.id, equals(50));
      expect(order.orderNumber, equals('ORD-2026-0050'));
      expect(order.shopId, equals(10));
      expect(order.status, equals('pending'));
      expect(order.statusDisplayName, equals('Pending'));
      expect(order.totalAmount, equals(1150.0));
      expect(order.items.length, equals(1));
      expect(order.items.first.productName, equals('Raw Fresh Milk'));
      expect(order.totalQuantity, equals(20.0));
    });

    test('ProductModel.fromJson parses product JSON correctly', () {
      final product = ProductModel.fromJson(sampleProductJson);

      expect(product.id, equals(1));
      expect(product.productCode, equals('PRD-RAW-MILK'));
      expect(product.availableStock, equals(500.0));
      expect(product.isRawMilk, isTrue);
    });
  });

  group('ShopOrderRepository Unit Tests', () {
    test('getShopOrders returns list of ShopOrderModel', () async {
      final mockApi = MockShopOrderApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleShopOrderJson],
      };

      final repository = ShopOrderRepository(apiClient: mockApi);
      final orders = await repository.getShopOrders();

      expect(orders.length, equals(1));
      expect(orders.first.orderNumber, equals('ORD-2026-0050'));
    });

    test('getProducts returns list of ProductModel', () async {
      final mockApi = MockShopOrderApiClient();
      mockApi.mockProductsResponse = {
        'success': true,
        'data': [sampleProductJson],
      };

      final repository = ShopOrderRepository(apiClient: mockApi);
      final products = await repository.getProducts();

      expect(products.length, equals(1));
      expect(products.first.name, equals('Raw Fresh Milk'));
    });

    test('updateShopOrderStatus patches status endpoint', () async {
      final mockApi = MockShopOrderApiClient();
      mockApi.mockPatchResponse = {
        'success': true,
        'data': {...sampleShopOrderJson, 'status': 'confirmed', 'stock_deducted': true},
      };

      final repository = ShopOrderRepository(apiClient: mockApi);
      final order = await repository.updateShopOrderStatus(50, 'confirmed');

      expect(order.status, equals('confirmed'));
      expect(order.stockDeducted, isTrue);
    });
  });

  group('ShopOrderProvider State & Status Tests', () {
    test('fetchShopOrders updates state to loaded', () async {
      final mockApi = MockShopOrderApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleShopOrderJson],
      };

      final repository = ShopOrderRepository(apiClient: mockApi);
      final provider = ShopOrderProvider(repository: repository);

      expect(provider.status, equals(ShopOrderStatus.initial));

      await provider.fetchShopOrders();

      expect(provider.status, equals(ShopOrderStatus.loaded));
      expect(provider.orders.length, equals(1));
      expect(provider.orders.first.orderNumber, equals('ORD-2026-0050'));
    });

    test('updateShopOrderStatus updates item state in provider', () async {
      final mockApi = MockShopOrderApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleShopOrderJson],
      };

      final repository = ShopOrderRepository(apiClient: mockApi);
      final provider = ShopOrderProvider(repository: repository);

      await provider.fetchShopOrders();

      mockApi.mockPatchResponse = {
        'success': true,
        'data': {...sampleShopOrderJson, 'status': 'confirmed'},
      };

      final success = await provider.updateShopOrderStatus(50, 'confirmed');

      expect(success, isTrue);
      expect(provider.orders.first.status, equals('confirmed'));
    });
  });
}
