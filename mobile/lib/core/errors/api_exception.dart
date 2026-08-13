class ApiException implements Exception {
  final String message;
  final int? statusCode;
  final Map<String, dynamic>? errors;

  ApiException({
    required this.message,
    this.statusCode,
    this.errors,
  });

  @override
  String toString() => message;

  /// Extract field-specific validation message if available
  String? getFieldError(String field) {
    if (errors == null || !errors!.containsKey(field)) return null;
    final fieldErrors = errors![field];
    if (fieldErrors is List && fieldErrors.isNotEmpty) {
      return fieldErrors.first.toString();
    }
    return fieldErrors?.toString();
  }
}

class UnauthorizedException extends ApiException {
  UnauthorizedException({super.message = 'Unauthenticated access. Please log in again.'})
      : super(statusCode: 401);
}

class ForbiddenException extends ApiException {
  ForbiddenException({super.message = 'You do not have permission to perform this action.'})
      : super(statusCode: 403);
}

class ValidationException extends ApiException {
  ValidationException({
    required super.message,
    required Map<String, dynamic> errors,
  }) : super(statusCode: 422, errors: errors);
}

class NotFoundException extends ApiException {
  NotFoundException({super.message = 'The requested resource was not found.'})
      : super(statusCode: 404);
}

class ServerException extends ApiException {
  ServerException({super.message = 'An unexpected server error occurred. Please try again.'})
      : super(statusCode: 500);
}

class NetworkException extends ApiException {
  NetworkException({super.message = 'Network connection failed. Please check your internet connection.'})
      : super(statusCode: 0);
}
