import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../models/milk_receiving_model.dart';
import '../providers/milk_receiving_provider.dart';

class MilkReceivingDetailScreen extends StatelessWidget {
  final MilkReceivingModel receiving;

  const MilkReceivingDetailScreen({super.key, required this.receiving});

  void _confirmDelete(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Milk Receiving Record?'),
        content: Text(
          'Are you sure you want to delete this receiving record of ${receiving.receivedQuantity.toStringAsFixed(1)}L from ${receiving.village?.name ?? "Village #${receiving.villageId}"}? This will automatically adjust stock inventory.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.of(ctx).pop();
              final provider = Provider.of<MilkReceivingProvider>(context, listen: false);
              final success = await provider.deleteMilkReceiving(receiving.id);
              if (success && context.mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Milk receiving record deleted.'),
                    backgroundColor: Colors.red,
                  ),
                );
                Navigator.of(context).pop();
              }
            },
            child: const Text('Delete', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final villageName = receiving.village?.name ?? 'Village #${receiving.villageId}';
    final villageCode = receiving.village?.code ?? '';
    final isMorning = receiving.shift.toLowerCase() == 'morning';
    final hasDiscrepancy = receiving.hasDiscrepancy;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Receiving Batch Details'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit_outlined),
            tooltip: 'Edit Record',
            onPressed: () {
              Navigator.of(context).pushNamed(
                AppRoutes.milkReceivingEdit,
                arguments: receiving,
              );
            },
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline, color: Colors.red),
            tooltip: 'Delete Record',
            onPressed: () => _confirmDelete(context),
          ),
        ],
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header Card
              Card(
                elevation: 3,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                child: Padding(
                  padding: const EdgeInsets.all(20.0),
                  child: Row(
                    children: [
                      CircleAvatar(
                        radius: 28,
                        backgroundColor: isMorning
                            ? const Color(0xFFFFEDD5)
                            : const Color(0xFFE0F2FE),
                        child: Icon(
                          isMorning ? Icons.wb_sunny : Icons.nights_stay,
                          size: 30,
                          color: isMorning
                              ? const Color(0xFFEA580C)
                              : const Color(0xFF0284C7),
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              villageName,
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            if (villageCode.isNotEmpty)
                              Text(
                                'Code: $villageCode',
                                style: const TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: AppTheme.primaryColor,
                                ),
                              ),
                            const SizedBox(height: 2),
                            Text(
                              '${receiving.receivingDate} · ${receiving.shiftDisplayName}',
                              style: const TextStyle(
                                fontSize: 12,
                                color: AppTheme.textSecondary,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: hasDiscrepancy
                              ? const Color(0xFFFEE2E2)
                              : const Color(0xFFD1FAE5),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: hasDiscrepancy
                                ? const Color(0xFFFECACA)
                                : const Color(0xFFA7F3D0),
                          ),
                        ),
                        child: Text(
                          hasDiscrepancy ? 'Discrepancy' : 'Received',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: hasDiscrepancy
                                ? const Color(0xFF991B1B)
                                : const Color(0xFF065F46),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Quantity Comparison Card
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Quantity Comparison & Audit',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          _buildDetailStat(
                            'Expected Quantity',
                            '${receiving.expectedQuantity.toStringAsFixed(2)} L',
                          ),
                          _buildDetailStat(
                            'Received Quantity',
                            '${receiving.receivedQuantity.toStringAsFixed(2)} L',
                            isBold: true,
                          ),
                          _buildDetailStat(
                            'Variance',
                            '${receiving.quantityVariance >= 0 ? "+" : ""}${receiving.quantityVariance.toStringAsFixed(2)} L',
                            color: hasDiscrepancy ? Colors.red : AppTheme.primaryColor,
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Fat & SNF Quality Comparison Card
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Milk Quality Comparison (Fat / SNF)',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          _buildDetailStat(
                            'Expected Fat',
                            receiving.expectedFat != null ? '${receiving.expectedFat!.toStringAsFixed(2)}%' : 'N/A',
                          ),
                          _buildDetailStat(
                            'Received Fat',
                            receiving.receivedFat != null ? '${receiving.receivedFat!.toStringAsFixed(2)}%' : 'N/A',
                            isBold: true,
                          ),
                          _buildDetailStat(
                            'Expected SNF',
                            receiving.expectedSnf != null ? '${receiving.expectedSnf!.toStringAsFixed(2)}%' : 'N/A',
                          ),
                          _buildDetailStat(
                            'Received SNF',
                            receiving.receivedSnf != null ? '${receiving.receivedSnf!.toStringAsFixed(2)}%' : 'N/A',
                            isBold: true,
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Remarks & Audit Info Card
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    children: [
                      if (receiving.notes != null && receiving.notes!.isNotEmpty)
                        ListTile(
                          leading: const Icon(Icons.note_alt_outlined, color: AppTheme.primaryColor),
                          title: const Text('Remarks / Notes'),
                          subtitle: Text(receiving.notes!),
                        ),
                      if (receiving.createdAt != null) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.access_time, color: AppTheme.primaryColor),
                          title: const Text('Recorded At'),
                          subtitle: Text(receiving.createdAt!),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Action Buttons Row
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: () {
                        Navigator.of(context).pushNamed(
                          AppRoutes.milkReceivingEdit,
                          arguments: receiving,
                        );
                      },
                      icon: const Icon(Icons.edit_outlined),
                      label: const Text('Edit Record'),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.red,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: () => _confirmDelete(context),
                      icon: const Icon(Icons.delete_outline, color: Colors.white),
                      label: const Text('Delete', style: TextStyle(color: Colors.white)),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDetailStat(String label, String value, {bool isBold = false, Color? color}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 12,
            color: AppTheme.textSecondary,
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: TextStyle(
            fontSize: 14,
            fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
            color: color ?? (isBold ? AppTheme.primaryColor : AppTheme.textPrimary),
          ),
        ),
      ],
    );
  }
}
