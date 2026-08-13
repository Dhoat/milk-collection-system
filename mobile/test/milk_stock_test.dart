import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/milk_stock/models/milk_stock_model.dart';
import 'package:mobile/features/milk_stock/providers/milk_stock_provider.dart';
import 'package:mobile/features/milk_stock/repositories/milk_stock_repository.dart';

class MockMilkStockApiClient implements ApiClient {
  Map<String, dynamic>? mockGetResponse;
  Map<String, dynamic>? mockPostResponse;
  bool shouldThrow = false;

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    if (shouldThrow) throw Exception('Get Milk Stocks Failed');
    return mockGetResponse ?? {
      'success': true,
      'summary': {
        'opening_stock': 100.0,
        'today_received': 500.0,
        'today_stock_out': 150.0,
        'available_stock': 450.0,
      },
      'data': [],
    };
  }

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    if (shouldThrow) throw Exception('Stock Out Failed');
    return mockPostResponse ?? {
      'success': true,
      'data': {
        'id': 99,
        'transaction_date': '2026-08-13',
        'type': 'out',
        'item_type': 'raw_milk',
        'quantity': 50.0,
        'source_or_reason': 'Distribution to Shop #1',
      },
    };
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();

  @override
  Future<Map<String, dynamic>> delete(String path) async => throw UnimplementedError();

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();
}

void main() {
  final sampleStockTransactionJson = {
    'id': 20,
    'transaction_date': '2026-08-13',
    'type': 'in',
    'item_type': 'raw_milk',
    'quantity': 148.5,
    'fat': 4.4,
    'snf': 8.4,
    'milk_receiving_id': 15,
    'source_or_reason': 'Milk Receiving - Maholi Kalan (Aug 13, 2026 - Morning)',
    'notes': 'Synced batch',
  };

  group('MilkStockModel & Summary Unit Tests', () {
    test('MilkStockModel.fromJson parses backend MilkStockResource JSON correctly', () {
      final stock = MilkStockModel.fromJson(sampleStockTransactionJson);

      expect(stock.id, 20);
      expect(stock.transactionDate, '2026-08-13');
      expect(stock.type, 'in');
      expect(stock.quantity, 148.5);
      expect(stock.isStockIn, true);
      expect(stock.sourceOrReason, contains('Maholi Kalan'));
    });

    test('MilkStockSummaryModel.fromJson parses KPI metrics correctly', () {
      final summary = MilkStockSummaryModel.fromJson({
        'opening_stock': 100.0,
        'today_received': 500.0,
        'today_stock_out': 150.0,
        'available_stock': 450.0,
      });

      expect(summary.openingStock, 100.0);
      expect(summary.todayReceived, 500.0);
      expect(summary.todayStockOut, 150.0);
      expect(summary.availableStock, 450.0);
    });
  });

  group('MilkStockRepository Unit Tests', () {
    test('getMilkStocks returns summary and transactions list', () async {
      final mockApi = MockMilkStockApiClient();
      mockApi.mockGetResponse = {
        'success': true,
        'summary': {
          'opening_stock': 100.0,
          'today_received': 500.0,
          'today_stock_out': 150.0,
          'available_stock': 450.0,
        },
        'data': [sampleStockTransactionJson],
      };

      final repo = MilkStockRepository(apiClient: mockApi);
      final result = await repo.getMilkStocks();

      final summary = result['summary'] as MilkStockSummaryModel;
      final transactions = result['transactions'] as List<MilkStockModel>;

      expect(summary.availableStock, 450.0);
      expect(transactions.length, 1);
      expect(transactions.first.id, 20);
    });

    test('recordStockOut posts payload and returns created MilkStockModel', () async {
      final mockApi = MockMilkStockApiClient();
      final repo = MilkStockRepository(apiClient: mockApi);

      final stockOut = await repo.recordStockOut({
        'transaction_date': '2026-08-13',
        'quantity': 50.0,
        'source_or_reason': 'Distribution to Shop #1',
      });

      expect(stockOut.id, 99);
      expect(stockOut.type, 'out');
      expect(stockOut.quantity, 50.0);
    });
  });

  group('MilkStockProvider State Tests', () {
    test('fetchMilkStocks updates state to loaded', () async {
      final mockApi = MockMilkStockApiClient();
      final repo = MilkStockRepository(apiClient: mockApi);
      final provider = MilkStockProvider(repository: repo);

      await provider.fetchMilkStocks();

      expect(provider.status, MilkStockStatus.loaded);
      expect(provider.availableStock, 450.0);
    });
  });
}
