import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../models/milk_collection_model.dart';

class MilkCollectionCard extends StatelessWidget {
  final MilkCollectionModel collection;
  final VoidCallback onTap;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

  const MilkCollectionCard({
    super.key,
    required this.collection,
    required this.onTap,
    required this.onEdit,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    final farmerName = collection.farmer?.name ?? 'Farmer #${collection.farmerId}';
    final farmerCode = collection.farmer?.farmerCode ?? '';
    final villageName = collection.farmer?.village?.name ?? '';
    final isMorning = collection.shift.toLowerCase() == 'morning';

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
              // Header Row: Farmer Name, Shift Badge & Popup Menu
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
                      size: 22,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          farmerName,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: AppTheme.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Row(
                          children: [
                            if (farmerCode.isNotEmpty) ...[
                              Text(
                                farmerCode,
                                style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: AppTheme.primaryColor,
                                ),
                              ),
                              if (villageName.isNotEmpty)
                                const Text(
                                  '  ·  ',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: AppTheme.textSecondary,
                                  ),
                                ),
                            ],
                            if (villageName.isNotEmpty)
                              Text(
                                villageName,
                                style: const TextStyle(
                                  fontSize: 12,
                                  color: AppTheme.textSecondary,
                                ),
                              ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: isMorning
                          ? const Color(0xFFFFF7ED)
                          : const Color(0xFFF0F9FF),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: isMorning
                            ? const Color(0xFFFFEDD5)
                            : const Color(0xFFE0F2FE),
                      ),
                    ),
                    child: Text(
                      collection.shiftDisplayName,
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: isMorning
                            ? const Color(0xFFC2410C)
                            : const Color(0xFF0369A1),
                      ),
                    ),
                  ),
                  PopupMenuButton<String>(
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
                            Text('Edit Collection'),
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

              // Metrics Grid: Date | Quantity | Fat/SNF | Amount
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: AppTheme.backgroundColor,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _buildMetric('Date', collection.collectionDate),
                    _buildMetric(
                      'Quantity',
                      '${collection.milkQuantity.toStringAsFixed(1)} L',
                    ),
                    _buildMetric(
                      'Fat / SNF',
                      '${collection.fat?.toStringAsFixed(1) ?? '-'}/${collection.snf?.toStringAsFixed(1) ?? '-'}',
                    ),
                    _buildMetric(
                      'Total Amount',
                      '₹ ${collection.amount.toStringAsFixed(2)}',
                      isBold: true,
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

  Widget _buildMetric(String label, String value, {bool isBold = false}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 11,
            color: AppTheme.textSecondary,
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 2),
        Text(
          value,
          style: TextStyle(
            fontSize: 13,
            fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
            color: isBold ? AppTheme.primaryColor : AppTheme.textPrimary,
          ),
        ),
      ],
    );
  }
}
