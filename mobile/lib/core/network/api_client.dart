// ignore_for_file: avoid_print

import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';

import '../constants/api_constants.dart';
import '../errors/api_exception.dart';
import '../storage/token_storage_interface.dart';

abstract class ApiClient {
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters});
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body});
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body});
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body});
  Future<Map<String, dynamic>> delete(String path);
}

class HttpApiClient implements ApiClient {
  final http.Client _client;
  final ITokenStorage tokenStorage;

  HttpApiClient({
    http.Client? client,
    required this.tokenStorage,
  }) : _client = client ?? _createDefaultClient();

  static http.Client _createDefaultClient() {
    if (kIsWeb) {
      return http.Client();
    }
    final ioHttpClient = HttpClient();
    ioHttpClient.findProxy = (uri) => 'DIRECT';
    ioHttpClient.badCertificateCallback = (X509Certificate cert, String host, int port) => true;
    ioHttpClient.connectionTimeout = ApiConstants.connectTimeout;
    return IOClient(ioHttpClient);
  }

  Uri _buildUri(String path, [Map<String, String>? queryParameters]) {
    final cleanBase = ApiConstants.baseUrl.endsWith('/')
        ? ApiConstants.baseUrl.substring(0, ApiConstants.baseUrl.length - 1)
        : ApiConstants.baseUrl;
    final cleanPath = path.startsWith('/') ? path : '/$path';
    final uri = Uri.parse('$cleanBase$cleanPath');
    if (queryParameters != null && queryParameters.isNotEmpty) {
      return uri.replace(queryParameters: queryParameters);
    }
    return uri;
  }

  Future<Map<String, String>> _buildHeaders() async {
    final headers = <String, String>{
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };

    final token = await tokenStorage.getToken();
    if (token != null && token.isNotEmpty) {
      headers['Authorization'] = 'Bearer $token';
    }

    return headers;
  }

  @override
  Future<Map<String, dynamic>> get(String path, {Map<String, String>? queryParameters}) async {
    final uri = _buildUri(path, queryParameters);
    return _sendRequest('GET', uri, (headers) async {
      return await _client
          .get(uri, headers: headers)
          .timeout(ApiConstants.connectTimeout);
    });
  }

  @override
  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    final uri = _buildUri(path);
    return _sendRequest('POST', uri, body: body, (headers) async {
      final encodedBody = body != null ? jsonEncode(body) : null;
      return await _client
          .post(uri, headers: headers, body: encodedBody)
          .timeout(ApiConstants.connectTimeout);
    });
  }

  @override
  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async {
    final uri = _buildUri(path);
    return _sendRequest('PUT', uri, body: body, (headers) async {
      final encodedBody = body != null ? jsonEncode(body) : null;
      return await _client
          .put(uri, headers: headers, body: encodedBody)
          .timeout(ApiConstants.connectTimeout);
    });
  }

  @override
  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? body}) async {
    final uri = _buildUri(path);
    return _sendRequest('PATCH', uri, body: body, (headers) async {
      final encodedBody = body != null ? jsonEncode(body) : null;
      return await _client
          .patch(uri, headers: headers, body: encodedBody)
          .timeout(ApiConstants.connectTimeout);
    });
  }

  @override
  Future<Map<String, dynamic>> delete(String path) async {
    final uri = _buildUri(path);
    return _sendRequest('DELETE', uri, (headers) async {
      return await _client
          .delete(uri, headers: headers)
          .timeout(ApiConstants.connectTimeout);
    });
  }

  Future<Map<String, dynamic>> _sendRequest(
    String method,
    Uri uri,
    Future<http.Response> Function(Map<String, String> headers) requestFn, {
    Map<String, dynamic>? body,
  }) async {
    final headers = await _buildHeaders();
    final tag = pathTag(uri.path);

    final safeBody = body != null ? Map<String, dynamic>.from(body) : null;
    if (safeBody != null && safeBody.containsKey('password')) {
      safeBody['password'] = '******';
    }

    print('[$tag] Runtime URL: $uri');
    print('[$tag] Method: $method');
    print('[$tag] Headers: $headers');
    print('[$tag] Body (MASK PASSWORD): $safeBody');
    print('[$tag] Request started');

    try {
      final response = await requestFn(headers);
      print('[$tag] Response status: ${response.statusCode}');
      print('[$tag] Response body: ${response.body}');
      return _processResponse(response);
    } on SocketException catch (e) {
      print('[$tag] Exception type: SocketException');
      print('[$tag] Exception message: ${e.message} (Host: ${e.address?.host})');
      throw NetworkException(
        message: 'Network connection failed: ${e.message} (Host: ${e.address?.host ?? uri.host})',
      );
    } on TimeoutException catch (e) {
      print('[$tag] Exception type: TimeoutException');
      print('[$tag] Exception message: ${e.message ?? 'Timeout'} after ${ApiConstants.connectTimeout.inSeconds}s (Target: $uri)');
      throw NetworkException(
        message: 'Server connection timed out (${ApiConstants.connectTimeout.inSeconds}s). Target: $uri',
      );
    } on http.ClientException catch (e) {
      print('[$tag] Exception type: ClientException');
      print('[$tag] Exception message: ${e.message} (URL: ${e.uri})');
      throw NetworkException(
        message: 'HTTP Client Error: ${e.message} (URL: ${e.uri})',
      );
    } on ApiException catch (e) {
      print('[$tag] Exception type: ${e.runtimeType}');
      print('[$tag] Exception message: [${e.statusCode}] ${e.message}');
      rethrow;
    } catch (e, stack) {
      print('[$tag] Exception type: ${e.runtimeType}');
      print('[$tag] Exception message: ${e.toString()}');
      if (kDebugMode) {
        print('[$tag] StackTrace: $stack');
      }
      throw ApiException(message: 'Unexpected network error: ${e.toString()}');
    }
  }

  String pathTag(String path) {
    if (path.contains('login')) return 'LOGIN';
    if (path.contains('me')) return 'ME';
    if (path.contains('logout')) return 'LOGOUT';
    return 'API';
  }

  Map<String, dynamic> _processResponse(http.Response response) {
    Map<String, dynamic> responseData = {};
    if (response.body.isNotEmpty) {
      try {
        final decoded = jsonDecode(response.body);
        if (decoded is Map<String, dynamic>) {
          responseData = decoded;
        }
      } catch (_) {
        // Fallback for non-json response bodies
      }
    }

    final message = responseData['message']?.toString() ?? 'An error occurred';

    switch (response.statusCode) {
      case 200:
      case 201:
        return responseData;

      case 401:
        throw UnauthorizedException(
          message: responseData['message']?.toString() ?? 'Invalid credentials.',
        );

      case 403:
        throw ForbiddenException(
          message: responseData['message']?.toString() ?? 'Action unauthorized.',
        );

      case 422:
        final rawErrors = responseData['errors'];
        Map<String, dynamic> errorsMap = {};
        if (rawErrors is Map<String, dynamic>) {
          errorsMap = rawErrors;
        }
        throw ValidationException(
          message: message,
          errors: errorsMap,
        );

      case 404:
        throw NotFoundException(
          message: responseData['message']?.toString() ?? 'Requested resource not found.',
        );

      default:
        throw ServerException(
          message: message.isNotEmpty ? message : 'Server returned error status code: ${response.statusCode}',
        );
    }
  }
}

