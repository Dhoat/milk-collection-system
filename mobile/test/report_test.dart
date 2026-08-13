import 'package:flutter_test/flutter_test.dart';

import 'package:mobile/core/errors/api_exception.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/reports/models/daily_report_model.dart';
import 'package:mobile/features/reports/models/monthly_report_model.dart';
import 'package:mobile/features/reports/providers/report_provider.dart';
import 'package:mobile/features/reports/repositories/report_repository.dart';

class MockReportApiClient implements ApiClient {
  Map<String, dynamic>? mockDailyResponse;
  Map<String, dynamic>? mockMonthlyResponse;
  bool shouldThrowForbidden = false;
  bool shouldThrowApiError = false;
  String? lastGetPath;
  Map<String, String>? lastQueryParams;

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    lastGetPath = path;
    lastQueryParams = queryParameters;

    if (shouldThrowForbidden) {
      throw ForbiddenException(message: 'This action is unauthorized.');
    }
    if (shouldThrowApiError) {
      throw ApiException(message: 'Server error');
    }

    if (path.contains('daily')) {
      return mockDailyResponse ?? {'success': true, 'data': {}};
    }
    return mockMonthlyResponse ?? {'success': true, 'data': {}};
  }

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    return {'success': true};
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async {
    return {'success': true};
  }

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async {
    return {'success': true};
  }

  @override
  Future<Map<String, dynamic>> delete(String path) async {
    return {'success': true};
  }
}

void main() {
  final sampleDailyJson = {
    'date': '2026-08-11',
    'collection': {
      'farmers_count': 102,
      'total_litres': 3171.5,
      'avg_per_farmer': 31.09,
      'total_amount': 173592.11,
      'avg_fat': 5.19,
      'avg_snf': 8.69,
      'village_breakdown': [
        {
          'village_id': 1,
          'village_name': 'Binjiki kalan',
          'total_litres': '293.50',
          'total_amount': '16066.92',
          'farmers_count': 9
        }
      ]
    },
    'center': {
      'total_received': 3146.89,
      'records_count': 26,
      'diff_litres': -24.61
    },
    'stock': {
      'opening': 93736.14,
      'in': 3146.89,
      'out': 0,
      'closing': 96883.03
    },
    'orders': {
      'total_count': 6,
      'total_value': 68918.98,
      'by_status': {
        'pending': 0,
        'confirmed': 2,
        'delivered': 1
      }
    },
    'deliveries': {
      'total_count': 6,
      'by_status': {
        'pending': 2,
        'out_for_delivery': 1,
        'delivered': 1
      }
    },
    'products': {
      'total_units': 242,
      'items': [
        {
          'product_id': 10,
          'product_name': 'Pure Desi Ghee 1L Jar',
          'unit': 'Jar',
          'total_qty': '58.00',
          'total_sales': '37700.00'
        }
      ]
    },
    'financial': {
      'total_sales': 68918.98,
      'milk_expense': 173592.11,
      'net_balance': -104673.13
    }
  };

  final sampleMonthlyJson = {
    'month': 8,
    'year': 2026,
    'start_date': '2026-08-01',
    'end_date': '2026-08-31',
    'days_in_month': 31,
    'collection': {
      'total_litres': 34812.5,
      'farmers_count': 120,
      'avg_daily': 1122.98,
      'total_amount': 1909170.67,
      'avg_fat': 5.25,
      'avg_snf': 8.75,
      'village_breakdown': []
    },
    'center': {
      'total_received': 34616.51,
      'avg_daily': 1116.66,
      'opening_stock': 62266.52,
      'stock_in': 34616.51,
      'stock_out': 0,
      'closing_stock': 96883.03
    },
    'orders': {
      'total_count': 52,
      'total_value': 452186.66,
      'by_status': {'delivered': 22, 'pending': 8},
      'top_shops': [
        {
          'shop_id': 2,
          'total_orders': 6,
          'total_sales': '89786.50',
          'shop': {
            'id': 2,
            'shop_code': 'SHP-001',
            'name': 'Malerkotla Central Dairy Store',
            'owner_name': 'Owner Name',
            'phone': '9872011001',
            'status': true,
            'credit_limit': 50000.0,
          }
        }
      ]
    },
    'products': {
      'total_units': 2495,
      'items': []
    },
    'deliveries': {
      'total_count': 45,
      'by_status': {'delivered': 22}
    },
    'financial': {
      'total_sales': 452186.66,
      'milk_expense': 1909170.67,
      'net_balance': -1456984.01
    }
  };

  group('DailyReportModel Unit Tests', () {
    test('DailyReportModel.fromJson parses JSON correctly', () {
      final model = DailyReportModel.fromJson(sampleDailyJson);

      expect(model.date, equals('2026-08-11'));
      expect(model.collection.farmersCount, equals(102));
      expect(model.collection.totalLitres, equals(3171.5));
      expect(model.collection.avgFat, equals(5.19));
      expect(model.collection.villageBreakdown.first.villageName, equals('Binjiki kalan'));
      expect(model.center.diffLitres, equals(-24.61));
      expect(model.stock.closing, equals(96883.03));
      expect(model.orders.totalValue, equals(68918.98));
      expect(model.products.items.first.productName, equals('Pure Desi Ghee 1L Jar'));
      expect(model.financial.netBalance, equals(-104673.13));
    });
  });

  group('MonthlyReportModel Unit Tests', () {
    test('MonthlyReportModel.fromJson parses JSON correctly', () {
      final model = MonthlyReportModel.fromJson(sampleMonthlyJson);

      expect(model.month, equals(8));
      expect(model.year, equals(2026));
      expect(model.monthName, equals('August'));
      expect(model.collection.totalLitres, equals(34812.5));
      expect(model.orders.topShops.first.shop?.name, equals('Malerkotla Central Dairy Store'));
      expect(model.financial.totalSales, equals(452186.66));
    });
  });

  group('ReportRepository Unit Tests', () {
    test('getDailyReport builds query parameters and returns DailyReportModel', () async {
      final mockApi = MockReportApiClient();
      mockApi.mockDailyResponse = {'success': true, 'data': sampleDailyJson};

      final repository = ReportRepository(apiClient: mockApi);
      final report = await repository.getDailyReport(date: '2026-08-11', villageId: 5);

      expect(report.date, equals('2026-08-11'));
      expect(mockApi.lastQueryParams?['date'], equals('2026-08-11'));
      expect(mockApi.lastQueryParams?['village_id'], equals('5'));
    });

    test('getMonthlyReport builds query parameters and returns MonthlyReportModel', () async {
      final mockApi = MockReportApiClient();
      mockApi.mockMonthlyResponse = {'success': true, 'data': sampleMonthlyJson};

      final repository = ReportRepository(apiClient: mockApi);
      final report = await repository.getMonthlyReport(month: 8, year: 2026);

      expect(report.month, equals(8));
      expect(mockApi.lastQueryParams?['month'], equals('8'));
      expect(mockApi.lastQueryParams?['year'], equals('2026'));
    });
  });

  group('ReportProvider State Tests', () {
    test('fetchReport loads daily report when mode is daily', () async {
      final mockApi = MockReportApiClient();
      mockApi.mockDailyResponse = {'success': true, 'data': sampleDailyJson};

      final repository = ReportRepository(apiClient: mockApi);
      final provider = ReportProvider(repository: repository);

      expect(provider.status, equals(ReportStateStatus.initial));

      await provider.fetchReport();

      expect(provider.status, equals(ReportStateStatus.loaded));
      expect(provider.dailyReport, isNotNull);
      expect(provider.dailyReport!.collection.farmersCount, equals(102));
    });

    test('fetchReport transitions to forbidden status when user lacks view-reports permission', () async {
      final mockApi = MockReportApiClient();
      mockApi.shouldThrowForbidden = true;

      final repository = ReportRepository(apiClient: mockApi);
      final provider = ReportProvider(repository: repository);

      await provider.fetchReport();

      expect(provider.status, equals(ReportStateStatus.forbidden));
      expect(provider.errorMessage, contains('unauthorized'));
    });
  });
}
