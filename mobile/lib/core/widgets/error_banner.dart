import 'package:flutter/material.dart';

class ErrorBanner extends StatelessWidget {
  final String message;
  final VoidCallback? onDismiss;

  const ErrorBanner({
    super.key,
    required this.message,
    this.onDismiss,
  });

  @override
  Widget build(BuildContext context) {
    if (message.isEmpty) return const SizedBox.shrink();

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: const Color(0xFFFEE2E2), // Red 100
        border: Border.all(color: const Color(0xFFFCA5A5)), // Red 300
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          const Icon(
            Icons.error_outline,
            color: Color(0xFFDC2626), // Red 600
            size: 20,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              message,
              style: const TextStyle(
                color: Color(0xFF991B1B), // Red 800
                fontSize: 13.5,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
          if (onDismiss != null)
            InkWell(
              onTap: onDismiss,
              child: const Icon(
                Icons.close,
                color: Color(0xFF991B1B),
                size: 18,
              ),
            ),
        ],
      ),
    );
  }
}
