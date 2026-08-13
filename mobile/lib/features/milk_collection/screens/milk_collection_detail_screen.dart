import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../models/milk_collection_model.dart';
import '../providers/milk_collection_provider.dart';
import '../widgets/milk_rate_calculator.dart';

class MilkCollectionDetailScreen extends StatelessWidget {
  final MilkCollectionModel collection;

  const MilkCollectionDetailScreen({super.key, required this.collection});

  void _confirmDelete(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Milk Collection Record?'),
        content: Text(
          'Are you sure you want to delete this milk collection entry of ${collection.milkQuantity.toStringAsFixed(1)}L for ${collection.farmer?.name ?? "Farmer #${collection.farmerId}"}? This action cannot be undone.',
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
              final provider = Provider.of<MilkCollectionProvider>(context, listen: false);
              final success = await provider.deleteMilkCollection(collection.id);
              if (success && context.mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Milk collection record deleted.'),
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
    final farmerName = collection.farmer?.name ?? 'Farmer #${collection.farmerId}';
    final farmerCode = collection.farmer?.farmerCode ?? '';
    final villageName = collection.farmer?.village?.name ?? '';
    final isMorning = collection.shift.toLowerCase() == 'morning';

    return Scaffold(
      appBar: AppBar(
        title: const Text('Collection Details'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit_outlined),
            tooltip: 'Edit Collection',
            onPressed: () {
              Navigator.of(context).pushNamed(
                AppRoutes.milkCollectionEdit,
                arguments: collection,
              );
            },
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline, color: Colors.red),
            tooltip: 'Delete Collection',
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
                              farmerName,
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            const SizedBox(height: 2),
                            if (farmerCode.isNotEmpty)
                              Text(
                                'Code: $farmerCode',
                                style: const TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: AppTheme.primaryColor,
                                ),
                              ),
                            if (villageName.isNotEmpty) ...[
                              const SizedBox(height: 2),
                              Text(
                                'Village: $villageName',
                                style: const TextStyle(
                                  fontSize: 12,
                                  color: AppTheme.textSecondary,
                                ),
                              ),
                            ],
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: isMorning
                              ? const Color(0xFFFFF7ED)
                              : const Color(0xFFF0F9FF),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: isMorning
                                ? const Color(0xFFFFEDD5)
                                : const Color(0xFFE0F2FE),
                          ),
                        ),
                        child: Text(
                          collection.shiftDisplayName,
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: isMorning
                                ? const Color(0xFFC2410C)
                                : const Color(0xFF0369A1),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Authoritative Calculation Rate Card Widget
              MilkRateCalculator(
                quantity: collection.milkQuantity,
                fat: collection.fat,
                snf: collection.snf,
                rate: collection.rate,
                isFinal: true,
              ),
              const SizedBox(height: 20),

              // Collection Details Table Card
              const Text(
                'Collection Record Info',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: AppTheme.textPrimary,
                ),
              ),
              const SizedBox(height: 10),
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    children: [
                      ListTile(
                        leading: const Icon(Icons.calendar_today, color: AppTheme.primaryColor),
                        title: const Text('Collection Date'),
                        subtitle: Text(collection.collectionDate),
                      ),
                      const Divider(),
                      ListTile(
                        leading: const Icon(Icons.water_drop, color: AppTheme.primaryColor),
                        title: const Text('Milk Quantity'),
                        subtitle: Text('${collection.milkQuantity.toStringAsFixed(2)} Litres'),
                      ),
                      const Divider(),
                      ListTile(
                        leading: const Icon(Icons.percent, color: AppTheme.primaryColor),
                        title: const Text('Fat Content'),
                        subtitle: Text(collection.fat != null ? '${collection.fat!.toStringAsFixed(1)}%' : 'Not specified'),
                      ),
                      const Divider(),
                      ListTile(
                        leading: const Icon(Icons.pie_chart, color: AppTheme.primaryColor),
                        title: const Text('SNF Content'),
                        subtitle: Text(collection.snf != null ? '${collection.snf!.toStringAsFixed(1)}%' : 'Not specified'),
                      ),
                      const Divider(),
                      ListTile(
                        leading: const Icon(Icons.currency_rupee, color: AppTheme.primaryColor),
                        title: const Text('Applied Rate per Litre'),
                        subtitle: Text('₹ ${collection.rate.toStringAsFixed(2)}'),
                      ),
                      const Divider(),
                      ListTile(
                        leading: const Icon(Icons.payments_outlined, color: AppTheme.primaryColor),
                        title: const Text('Total Calculated Amount'),
                        subtitle: Text(
                          '₹ ${collection.amount.toStringAsFixed(2)}',
                          style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryColor),
                        ),
                      ),
                      if (collection.notes != null && collection.notes!.isNotEmpty) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.note_alt_outlined, color: AppTheme.primaryColor),
                          title: const Text('Remarks / Notes'),
                          subtitle: Text(collection.notes!),
                        ),
                      ],
                      if (collection.createdAt != null) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.access_time, color: AppTheme.primaryColor),
                          title: const Text('Recorded At'),
                          subtitle: Text(collection.createdAt!),
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
                          AppRoutes.milkCollectionEdit,
                          arguments: collection,
                        );
                      },
                      icon: const Icon(Icons.edit_outlined),
                      label: const Text('Edit Entry'),
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
}
