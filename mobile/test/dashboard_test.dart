import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/network/api_client.dart';
import 'package:mobile/features/dashboard/models/dashboard_model.dart';
import 'package:mobile/features/dashboard/providers/dashboard_provider.dart';
import 'package:mobile/features/dashboard/repositories/dashboard_repository.dart';

class MockApiClient implements ApiClient {
  Map<String, dynamic>? mockResponse;
  bool shouldThrow = false;

  @override
  Future<Map<String, dynamic>> delete(String path) async => throw UnimplementedError();

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    if (shouldThrow) throw Exception('API Error');
    return mockResponse ?? {};
  }

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async => throw UnimplementedError();
}

void main() {
  group('Dashboard Model & Repository Unit Tests', () {
    final sampleJson = {
      'today': '2026-08-13',
      'kpis': {
        'total_farmers': 150,
        'active_farmers': 140,
        'total_villages': 12,
        'active_villages': 10,
        'today_quantity': 450.5,
        'today_amount': 22500.0,
      },
      'todayOverview': {
        'morning': {'quantity': 250.0, 'farmers': 80},
        'evening': {'quantity': 200.5, 'farmers': 70},
        'total': {'quantity': 450.5, 'farmers': 150},
      },
      'collectionTrend': [
        {'date': '2026-08-13', 'label': 'Thu', 'full_label': 'Aug 13', 'litres': 450.5}
      ],
      'recentActivity': [
        {'type': 'collection', 'message': 'Collection added', 'detail': '10L', 'timestamp': '2026-08-13', 'icon': 'collection'}
      ]
    };

    test('DashboardModel.fromJson should parse correctly', () {
      final model = DashboardModel.fromJson(sampleJson);

      expect(model.today, '2026-08-13');
      expect(model.kpis.totalFarmers, 150);
      expect(model.kpis.activeFarmers, 140);
      expect(model.kpis.todayQuantity, 450.5);
      expect(model.kpis.todayAmount, 22500.0);
      expect(model.todayOverview.morning.quantity, 250.0);
      expect(model.collectionTrend.length, 1);
      expect(model.recentActivity.first.message, 'Collection added');
    });

    test('DashboardRepository.getDashboard returns DashboardModel on success', () async {
      final mockApi = MockApiClient();
      mockApi.mockResponse = {
        'success': true,
        'data': sampleJson,
      };

      final repo = DashboardRepository(apiClient: mockApi);
      final dashboard = await repo.getDashboard();

      expect(dashboard.kpis.totalFarmers, 150);
      expect(dashboard.todayOverview.evening.farmers, 70);
    });
  });

  group('DashboardProvider State Tests', () {
    test('fetchDashboard transitions state from loading to loaded', () async {
      final mockApi = MockApiClient();
      mockApi.mockResponse = {
        'success': true,
        'data': {
          'today': '2026-08-13',
          'kpis': {
            'total_farmers': 100,
            'active_farmers': 90,
            'total_villages': 10,
            'active_villages': 10,
            'today_quantity': 300.0,
            'today_amount': 15000.0,
          },
          'todayOverview': {
            'morning': {'quantity': 150.0, 'farmers': 50},
            'evening': {'quantity': 150.0, 'farmers': 50},
            'total': {'quantity': 300.0, 'farmers': 100},
          },
          'collectionTrend': [],
          'recentActivity': []
        },
      };

      final repo = DashboardRepository(apiClient: mockApi);
      final provider = DashboardProvider(dashboardRepository: repo);

      expect(provider.status, DashboardStatus.initial);

      final future = provider.fetchDashboard();
      expect(provider.isLoading, isTrue);

      await future;
      expect(provider.isLoaded, isTrue);
      expect(provider.dashboardData?.kpis.totalFarmers, 100);
    });

    test('fetchDashboard handles error gracefully', () async {
      final mockApi = MockApiClient();
      mockApi.shouldThrow = true;

      final repo = DashboardRepository(apiClient: mockApi);
      final provider = DashboardProvider(dashboardRepository: repo);

      await provider.fetchDashboard();

      expect(provider.hasError, isTrue);
      expect(provider.errorMessage, contains('Failed to load dashboard data'));
    });
  });
}
