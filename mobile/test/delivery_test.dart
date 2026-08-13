import 'package:flutter_test/flutter_test.dart';

import 'package:mobile/core/errors/api_exception.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/deliveries/models/delivery_model.dart';
import 'package:mobile/features/deliveries/providers/delivery_provider.dart';
import 'package:mobile/features/deliveries/repositories/delivery_repository.dart';

class MockDeliveryApiClient implements ApiClient {
  Map<String, dynamic>? mockGetResponse;
  Map<String, dynamic>? mockPostResponse;
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
        message: 'An active delivery dispatch already exists for this order.',
        errors: {
          'shop_order_id': ['An active delivery dispatch already exists for this order.']
        },
      );
    }
    return mockPostResponse ?? {'success': true, 'data': {}};
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async {
    return {'success': true, 'data': {}};
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
  final sampleDeliveryJson = {
    'id': 1,
    'delivery_number': 'DEL-2026-0001',
    'shop_order_id': 50,
    'shop_id': 10,
    'delivery_date': '2026-08-13',
    'status': 'pending',
    'delivery_address': 'Main Market Road, North Zone',
    'contact_person': 'Ramesh Kumar',
    'contact_phone': '9876543210',
    'assigned_to': 2,
    'created_by': 1,
    'notes': 'Deliver before 10 AM',
    'dispatched_at': null,
    'delivered_at': null,
    'shop_order': {
      'id': 50,
      'order_number': 'ORD-2026-0050',
      'shop_id': 10,
      'order_date': '2026-08-13',
      'status': 'confirmed',
      'subtotal': 1200.0,
      'discount': 50.0,
      'total_amount': 1150.0,
      'stock_deducted': true,
      'items': [],
    },
    'shop': {
      'id': 10,
      'shop_code': 'SHP-101',
      'name': 'Green Dairy Store',
      'owner_name': 'Ramesh Kumar',
      'phone': '9876543210',
      'status': true,
    },
    'assigned_staff': {
      'id': 2,
      'name': 'Staff Driver User',
      'email': 'driver@dairy.com',
      'role': 'center_staff',
      'status': true,
    },
    'created_at': '2026-08-13T10:00:00Z',
    'updated_at': '2026-08-13T10:00:00Z',
  };

  group('DeliveryModel Unit Tests', () {
    test('DeliveryModel.fromJson parses JSON correctly with nested relations', () {
      final delivery = DeliveryModel.fromJson(sampleDeliveryJson);

      expect(delivery.id, equals(1));
      expect(delivery.deliveryNumber, equals('DEL-2026-0001'));
      expect(delivery.shopOrderId, equals(50));
      expect(delivery.shopId, equals(10));
      expect(delivery.status, equals('pending'));
      expect(delivery.statusDisplayName, equals('Pending Dispatch'));
      expect(delivery.shop?.name, equals('Green Dairy Store'));
      expect(delivery.shopOrder?.orderNumber, equals('ORD-2026-0050'));
      expect(delivery.assignedStaff?.name, equals('Staff Driver User'));
      expect(delivery.isTerminalState, isFalse);
      expect(delivery.allowedNextStatuses, contains('out_for_delivery'));
    });

    test('allowedNextStatuses returns correct transitions according to DeliveryService logic', () {
      final pendingDelivery = DeliveryModel.fromJson({...sampleDeliveryJson, 'status': 'pending'});
      expect(pendingDelivery.allowedNextStatuses, equals(['assigned', 'out_for_delivery', 'cancelled']));

      final outForDelivery = DeliveryModel.fromJson({...sampleDeliveryJson, 'status': 'out_for_delivery'});
      expect(outForDelivery.allowedNextStatuses, equals(['delivered', 'failed', 'cancelled']));

      final delivered = DeliveryModel.fromJson({...sampleDeliveryJson, 'status': 'delivered'});
      expect(delivered.isTerminalState, isTrue);
      expect(delivered.allowedNextStatuses, isEmpty);
    });
  });

  group('DeliveryRepository Unit Tests', () {
    test('getDeliveries returns list of DeliveryModel', () async {
      final mockApi = MockDeliveryApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleDeliveryJson],
      };

      final repository = DeliveryRepository(apiClient: mockApi);
      final list = await repository.getDeliveries();

      expect(list.length, equals(1));
      expect(list.first.deliveryNumber, equals('DEL-2026-0001'));
    });

    test('updateDeliveryStatus patches status endpoint with new status', () async {
      final mockApi = MockDeliveryApiClient();
      mockApi.mockPatchResponse = {
        'success': true,
        'data': {...sampleDeliveryJson, 'status': 'out_for_delivery', 'dispatched_at': '2026-08-13T10:15:00Z'},
      };

      final repository = DeliveryRepository(apiClient: mockApi);
      final updated = await repository.updateDeliveryStatus(1, 'out_for_delivery');

      expect(updated.status, equals('out_for_delivery'));
      expect(updated.dispatchedAt, equals('2026-08-13T10:15:00Z'));
    });
  });

  group('DeliveryProvider State & Workflow Tests', () {
    test('fetchDeliveries updates state to loaded', () async {
      final mockApi = MockDeliveryApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'data': [sampleDeliveryJson],
      };

      final repository = DeliveryRepository(apiClient: mockApi);
      final provider = DeliveryProvider(repository: repository);

      expect(provider.status, equals(DeliveryStateStatus.initial));

      await provider.fetchDeliveries();

      expect(provider.status, equals(DeliveryStateStatus.loaded));
      expect(provider.deliveries.length, equals(1));
      expect(provider.deliveries.first.deliveryNumber, equals('DEL-2026-0001'));
    });

    test('createDelivery captures validation errors when active dispatch exists', () async {
      final mockApi = MockDeliveryApiClient();
      mockApi.shouldThrowValidation = true;

      final repository = DeliveryRepository(apiClient: mockApi);
      final provider = DeliveryProvider(repository: repository);

      final success = await provider.createDelivery({'shop_order_id': 50});

      expect(success, isFalse);
      expect(provider.errorMessage, equals('An active delivery dispatch already exists for this order.'));
      expect(
        provider.validationErrors?['shop_order_id']?.first,
        equals('An active delivery dispatch already exists for this order.'),
      );
    });
  });
}
