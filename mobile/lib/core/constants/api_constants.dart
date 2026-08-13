import 'dart:io' show Platform;
import 'package:flutter/foundation.dart' show kIsWeb;

class ApiConstants {
  static String? _customBaseUrl;

  /// Default Physical Android LAN IP
  static const String physicalAndroidBaseUrl = 'http://192.168.1.22:8000/api';

  /// Android Emulator IP
  static const String emulatorBaseUrl = 'http://10.0.2.2:8000/api';

  /// Localhost IP (Web / Linux / Desktop)
  static const String localhostBaseUrl = 'http://127.0.0.1:8000/api';

  /// Dynamically resolved base API URL.
  /// 1. Uses `--dart-define=API_BASE_URL=...` if provided.
  /// 2. Uses `ApiConstants.baseUrl = ...` setter if configured programmatically.
  /// 3. If `--dart-define=USE_EMULATOR=true` is set, uses Android Emulator URL (`http://10.0.2.2:8000/api`).
  /// 4. Default for Android: `http://192.168.1.22:8000/api` (Physical Android Device LAN IP).
  /// 5. Default for Web / Linux / Desktop: `http://127.0.0.1:8000/api`.
  static String get baseUrl {
    const envUrl = String.fromEnvironment('API_BASE_URL');
    if (envUrl.isNotEmpty) {
      return envUrl;
    }

    if (_customBaseUrl != null && _customBaseUrl!.isNotEmpty) {
      return _customBaseUrl!;
    }

    if (kIsWeb) {
      return localhostBaseUrl;
    }

    try {
      if (Platform.isAndroid) {
        const useEmulator = bool.fromEnvironment('USE_EMULATOR', defaultValue: false);
        if (useEmulator) {
          return emulatorBaseUrl;
        }
        return physicalAndroidBaseUrl;
      }
    } catch (_) {
      // Fallback for unsupported platform inspect
    }
    return localhostBaseUrl;
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
