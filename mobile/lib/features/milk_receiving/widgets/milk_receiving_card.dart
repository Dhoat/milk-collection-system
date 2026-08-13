import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../models/milk_receiving_model.dart';

class MilkReceivingCard extends StatelessWidget {
  final MilkReceivingModel receiving;
  final VoidCallback onTap;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

  const MilkReceivingCard({
    super.key,
    required this.receiving,
    required this.onTap,
    required this.onEdit,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    final villageName = receiving.village?.name ?? 'Village #${receiving.villageId}';
    final isMorning = receiving.shift.toLowerCase() == 'morning';
    final hasDiscrepancy = receiving.hasDiscrepancy;

    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
      elevation: 2,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CircleAvatar(
                    radius: 20,
                    backgroundColor: isMorning
                        ? const Color(0xFFFFEDD5)
                        : const Color(0xFFE0F2FE),
                    child: Icon(
                      isMorning ? Icons.wb_sunny : Icons.nights_stay,
                      color: isMorning
                          ? const Color(0xFFEA580C)
                          : const Color(0xFF0284C7),
                      size: 20,
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          villageName,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                            color: AppTheme.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          '${receiving.receivingDate}  ·  ${receiving.shiftDisplayName}',
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            fontSize: 12,
                            color: AppTheme.textSecondary,
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 6),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: hasDiscrepancy
                          ? const Color(0xFFFEE2E2)
                          : const Color(0xFFD1FAE5),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: hasDiscrepancy
                            ? const Color(0xFFFECACA)
                            : const Color(0xFFA7F3D0),
                      ),
                    ),
                    child: Text(
                      hasDiscrepancy ? 'Discrepancy' : 'Received',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: hasDiscrepancy
                            ? const Color(0xFF991B1B)
                            : const Color(0xFF065F46),
                      ),
                    ),
                  ),
                  PopupMenuButton<String>(
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(),
                    onSelected: (value) {
                      if (value == 'edit') onEdit();
                      if (value == 'delete') onDelete();
                    },
                    itemBuilder: (context) => [
                      const PopupMenuItem(
                        value: 'edit',
                        child: Row(
                          children: [
                            Icon(Icons.edit_outlined, size: 18, color: AppTheme.primaryColor),
                            SizedBox(width: 8),
                            Text('Edit Record'),
                          ],
                        ),
                      ),
                      const PopupMenuItem(
                        value: 'delete',
                        child: Row(
                          children: [
                            Icon(Icons.delete_outline, size: 18, color: Colors.red),
                            SizedBox(width: 8),
                            Text('Delete Record', style: TextStyle(color: Colors.red)),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 12),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                decoration: BoxDecoration(
                  color: AppTheme.backgroundColor,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Row(
                  children: [
                    Expanded(
                      flex: 2,
                      child: _buildMetric(
                        'Expected Qty',
                        '${receiving.expectedQuantity.toStringAsFixed(1)} L',
                      ),
                    ),
                    Expanded(
                      flex: 2,
                      child: _buildMetric(
                        'Received Qty',
                        '${receiving.receivedQuantity.toStringAsFixed(1)} L',
                        isBold: true,
                      ),
                    ),
                    Expanded(
                      flex: 2,
                      child: _buildMetric(
                        'Variance',
                        '${receiving.quantityVariance >= 0 ? "+" : ""}${receiving.quantityVariance.toStringAsFixed(1)} L',
                        color: hasDiscrepancy ? Colors.red : AppTheme.primaryColor,
                      ),
                    ),
                    Expanded(
                      flex: 2,
                      child: _buildMetric(
                        'Fat / SNF',
                        '${receiving.receivedFat?.toStringAsFixed(1) ?? "-"}/${receiving.receivedSnf?.toStringAsFixed(1) ?? "-"}',
                        alignRight: true,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildMetric(String label, String value, {bool isBold = false, Color? color, bool alignRight = false}) {
    return Column(
      crossAxisAlignment: alignRight ? CrossAxisAlignment.end : CrossAxisAlignment.start,
      mainAxisSize: MainAxisSize.min,
      children: [
        Text(
          label,
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          style: const TextStyle(
            fontSize: 10,
            color: AppTheme.textSecondary,
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 2),
        FittedBox(
          fit: BoxFit.scaleDown,
          alignment: alignRight ? Alignment.centerRight : Alignment.centerLeft,
          child: Text(
            value,
            style: TextStyle(
              fontSize: 13,
              fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
              color: color ?? (isBold ? AppTheme.primaryColor : AppTheme.textPrimary),
            ),
          ),
        ),
      ],
    );
  }
}
