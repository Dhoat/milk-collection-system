import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class AccessRestrictedScreen extends StatelessWidget {
  final String routeName;
  final String? userRole;

  const AccessRestrictedScreen({
    super.key,
    required this.routeName,
    this.userRole,
  });

  @override
  Widget build(BuildContext context) {
    final roleDisplay = userRole ?? 'unassigned';

    return Scaffold(
      appBar: AppBar(
        title: const Text('Access Restricted'),
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.amber.shade50,
                    shape: BoxShape.circle,
                  ),
                  child: Icon(
                    Icons.security_outlined,
                    size: 64,
                    color: Colors.amber.shade800,
                  ),
                ),
                const SizedBox(height: 24),
                const Text(
                  'Access Restricted',
                  style: TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.bold,
                    color: AppTheme.textPrimary,
                  ),
                ),
                const SizedBox(height: 12),
                Text(
                  'Your account role ($roleDisplay) is not authorized to access this module ($routeName).',
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    fontSize: 14,
                    color: AppTheme.textSecondary,
                    height: 1.4,
                  ),
                ),
                const SizedBox(height: 32),
                ElevatedButton.icon(
                  onPressed: () {
                    if (Navigator.of(context).canPop()) {
                      Navigator.of(context).pop();
                    } else {
                      Navigator.of(context).pushReplacementNamed('/dashboard');
                    }
                  },
                  icon: const Icon(Icons.home),
                  label: const Text('Return to Dashboard'),
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
