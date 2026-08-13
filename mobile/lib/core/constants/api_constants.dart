import 'dart:io' show Platform;
import 'package:flutter/foundation.dart' show kIsWeb;

class ApiConstants {
  static String? _customBaseUrl;

  /// Dynamically resolved base API URL.
  /// - Android Emulator: 'http://10.0.2.2:8000/api'
  /// - Linux / Web / iOS Simulator: 'http://127.0.0.1:8000/api'
  /// - Can be overridden via setter: `ApiConstants.baseUrl = 'http://192.168.1.100:8000/api'`
  static String get baseUrl {
    if (_customBaseUrl != null && _customBaseUrl!.isNotEmpty) {
      return _customBaseUrl!;
    }
    if (kIsWeb) {
      return 'http://127.0.0.1:8000/api';
    }
    try {
      if (Platform.isAndroid) {
        return 'http://10.0.2.2:8000/api';
      }
    } catch (_) {
      // Fallback for unsupported platform inspect
    }
    return 'http://127.0.0.1:8000/api';
  }

  static set baseUrl(String value) {
    _customBaseUrl = value;
  }

  // Request Timeouts
  static const Duration connectTimeout = Duration(seconds: 15);
  static const Duration receiveTimeout = Duration(seconds: 15);

  // Authentication Endpoints
  static const String login = '/login';
  static const String me = '/me';
  static const String logout = '/logout';

  // Self-Service Profile Endpoints
  static const String profile = '/profile';
  static const String profilePassword = '/profile/password';

  // Dashboard Endpoint
  static const String dashboard = '/dashboard';

  // Village & Farmer Collection Endpoints
  static const String villages = '/villages';
  static const String farmers = '/farmers';
  static const String milkCollections = '/milk-collections';

  // Milk Processing & Stock Endpoints
  static const String milkReceivings = '/milk-receivings';
  static const String milkReceivingsSummary = '/milk-receivings/summary';
  static const String milkStocks = '/milk-stocks';
  static const String milkStocksOut = '/milk-stocks/out';

  // Shop & Distribution Endpoints
  static const String shops = '/shops';
  static const String shopOrders = '/shop-orders';
  static const String products = '/products';
  static const String deliveries = '/deliveries';

  // Executive Reports Endpoints
  static const String dailyReport = '/reports/daily';
  static const String monthlyReport = '/reports/monthly';

  // User Management Endpoints
  static const String users = '/users';
}
