import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/app/app.dart';
import 'package:mobile/core/storage/secure_token_storage.dart';
import 'auth_provider_test.dart';

void main() {
  testWidgets('Renders Login Screen when unauthenticated', (WidgetTester tester) async {
    final tokenStorage = InMemoryTokenStorage();
    final mockAuthRepo = MockAuthRepository();

    await tester.pumpWidget(
      MilkCenterApp(
        tokenStorage: tokenStorage,
        authRepository: mockAuthRepo,
      ),
    );

    await tester.pumpAndSettle();

    expect(find.text('Dairy Management'), findsOneWidget);
    expect(find.text('Milk Center Management System'), findsOneWidget);
    expect(find.text('Email Address'), findsOneWidget);
    expect(find.text('Password'), findsOneWidget);
    expect(find.byType(ElevatedButton), findsOneWidget);
  });
}
