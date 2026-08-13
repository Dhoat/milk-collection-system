import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../models/milk_stock_model.dart';

class MilkStockCard extends StatelessWidget {
  final MilkStockModel transaction;

  const MilkStockCard({super.key, required this.transaction});

  @override
  Widget build(BuildContext context) {
    final isStockIn = transaction.isStockIn;
    final themeColor = isStockIn ? const Color(0xFF059669) : const Color(0xFFDC2626);

    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 5),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(14.0),
        child: Row(
          children: [
            CircleAvatar(
              radius: 20,
              backgroundColor: themeColor.withValues(alpha: 0.1),
              child: Icon(
                isStockIn ? Icons.arrow_downward : Icons.arrow_upward,
                color: themeColor,
                size: 20,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    transaction.sourceOrReason.isNotEmpty
                        ? transaction.sourceOrReason
                        : (isStockIn ? 'Stock IN' : 'Stock OUT'),
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    '${transaction.transactionDate}  ·  ${isStockIn ? "Receiving Entry" : "Manual Stock Out"}',
                    style: const TextStyle(
                      fontSize: 12,
                      color: AppTheme.textSecondary,
                    ),
                  ),
                  if (transaction.notes != null && transaction.notes!.isNotEmpty) ...[
                    const SizedBox(height: 4),
                    Text(
                      transaction.notes!,
                      style: const TextStyle(
                        fontSize: 11,
                        fontStyle: FontStyle.italic,
                        color: AppTheme.textSecondary,
                      ),
                    ),
                  ],
                ],
              ),
            ),
            const SizedBox(width: 8),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: themeColor.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    '${isStockIn ? "+" : "-"}${transaction.quantity.toStringAsFixed(1)} L',
                    style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                      color: themeColor,
                    ),
                  ),
                ),
                if (transaction.fat != null || transaction.snf != null) ...[
                  const SizedBox(height: 4),
                  Text(
                    'Fat ${transaction.fat?.toStringAsFixed(1) ?? "-"}% | SNF ${transaction.snf?.toStringAsFixed(1) ?? "-"}%',
                    style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary),
                  ),
                ],
              ],
            ),
          ],
        ),
      ),
    );
  }
}
