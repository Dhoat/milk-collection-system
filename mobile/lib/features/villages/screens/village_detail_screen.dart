import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../../farmers/providers/farmer_provider.dart';
import '../models/village_model.dart';

class VillageDetailScreen extends StatelessWidget {
  final VillageModel village;

  const VillageDetailScreen({super.key, required this.village});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(village.name),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit_outlined),
            tooltip: 'Edit Village',
            onPressed: () {
              Navigator.of(context).pushNamed(
                AppRoutes.villageEdit,
                arguments: village,
              );
            },
          ),
        ],
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header Badge Card
              Card(
                elevation: 3,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                child: Padding(
                  padding: const EdgeInsets.all(20.0),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: AppTheme.primaryColor.withValues(alpha: 0.1),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(
                          Icons.location_city,
                          size: 36,
                          color: AppTheme.primaryColor,
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              village.name,
                              style: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              'Code: ${village.code}',
                              style: const TextStyle(
                                fontSize: 14,
                                color: AppTheme.textSecondary,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: village.status ? const Color(0xFFD1FAE5) : const Color(0xFFFEE2E2),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          village.status ? 'Active' : 'Inactive',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: village.status ? const Color(0xFF065F46) : const Color(0xFF991B1B),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Overview Details
              const Text(
                'Village Details',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: AppTheme.textPrimary,
                ),
              ),
              const SizedBox(height: 12),

              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    children: [
                      ListTile(
                        leading: const Icon(Icons.people_outline, color: AppTheme.primaryColor),
                        title: const Text('Registered Farmers'),
                        subtitle: Text(
                          village.farmersCount != null
                              ? '${village.farmersCount} Farmers in this village'
                              : 'Tap below to view farmers',
                          style: const TextStyle(fontWeight: FontWeight.w600),
                        ),
                      ),
                      const Divider(),
                      ListTile(
                        leading: const Icon(Icons.map_outlined, color: AppTheme.primaryColor),
                        title: const Text('Location Address'),
                        subtitle: Text(
                          village.address != null && village.address!.isNotEmpty
                              ? village.address!
                              : 'No specific address details provided.',
                        ),
                      ),
                      if (village.createdAt != null) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.calendar_today_outlined, color: AppTheme.primaryColor),
                          title: const Text('Created On'),
                          subtitle: Text(village.createdAt!),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // View Village Farmers Action Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    backgroundColor: AppTheme.primaryColor,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () {
                    final farmerProvider = Provider.of<FarmerProvider>(context, listen: false);
                    farmerProvider.setVillageFilter(village.id);
                    Navigator.of(context).pushNamed(AppRoutes.farmers);
                  },
                  icon: const Icon(Icons.people_alt_outlined, color: Colors.white),
                  label: Text(
                    'View Farmers in ${village.name}',
                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
