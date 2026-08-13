import 'package:flutter/material.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../models/farmer_model.dart';

class FarmerDetailScreen extends StatelessWidget {
  final FarmerModel farmer;

  const FarmerDetailScreen({super.key, required this.farmer});

  @override
  Widget build(BuildContext context) {
    final villageName = farmer.village?.name ?? 'Village #${farmer.villageId}';

    return Scaffold(
      appBar: AppBar(
        title: Text(farmer.name),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit_outlined),
            tooltip: 'Edit Farmer',
            onPressed: () {
              Navigator.of(context).pushNamed(
                AppRoutes.farmerEdit,
                arguments: farmer,
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
              // Header Card
              Card(
                elevation: 3,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                child: Padding(
                  padding: const EdgeInsets.all(20.0),
                  child: Row(
                    children: [
                      CircleAvatar(
                        radius: 30,
                        backgroundColor: AppTheme.primaryColor.withValues(alpha: 0.1),
                        child: const Icon(
                          Icons.person,
                          size: 34,
                          color: AppTheme.primaryColor,
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              farmer.name,
                              style: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            if (farmer.fatherName != null && farmer.fatherName!.isNotEmpty) ...[
                              const SizedBox(height: 2),
                              Text(
                                "S/O: ${farmer.fatherName}",
                                style: const TextStyle(
                                  fontSize: 13,
                                  color: AppTheme.textSecondary,
                                ),
                              ),
                            ],
                            const SizedBox(height: 6),
                            Text(
                              'Code: ${farmer.farmerCode}',
                              style: const TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: AppTheme.primaryColor,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: farmer.status ? const Color(0xFFD1FAE5) : const Color(0xFFFEE2E2),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          farmer.status ? 'Active' : 'Inactive',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: farmer.status ? const Color(0xFF065F46) : const Color(0xFF991B1B),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // General Information Card
              const Text(
                'Personal & Contact Details',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
              ),
              const SizedBox(height: 10),
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    children: [
                      ListTile(
                        leading: const Icon(Icons.location_city, color: AppTheme.primaryColor),
                        title: const Text('Assigned Village'),
                        subtitle: Text(
                          villageName,
                          style: const TextStyle(fontWeight: FontWeight.w600, color: AppTheme.primaryColor),
                        ),
                      ),
                      const Divider(),
                      ListTile(
                        leading: const Icon(Icons.phone, color: AppTheme.primaryColor),
                        title: const Text('Mobile Contact'),
                        subtitle: Text(farmer.mobile),
                      ),
                      if (farmer.alternateMobile != null && farmer.alternateMobile!.isNotEmpty) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.phone_android, color: AppTheme.primaryColor),
                          title: const Text('Alternate Mobile'),
                          subtitle: Text(farmer.alternateMobile!),
                        ),
                      ],
                      if (farmer.address != null && farmer.address!.isNotEmpty) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.home_outlined, color: AppTheme.primaryColor),
                          title: const Text('Address'),
                          subtitle: Text(farmer.address!),
                        ),
                      ],
                      if (farmer.joiningDate != null) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.calendar_today_outlined, color: AppTheme.primaryColor),
                          title: const Text('Registration Date'),
                          subtitle: Text(farmer.joiningDate!),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Bank Account Card
              const Text(
                'Financial & Payment Info',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
              ),
              const SizedBox(height: 10),
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    children: [
                      ListTile(
                        leading: const Icon(Icons.account_balance, color: AppTheme.primaryColor),
                        title: const Text('Bank Name'),
                        subtitle: Text(farmer.bankName ?? 'Not specified'),
                      ),
                      const Divider(),
                      ListTile(
                        leading: const Icon(Icons.numbers, color: AppTheme.primaryColor),
                        title: const Text('Account Number'),
                        subtitle: Text(farmer.accountNumber ?? 'Not specified'),
                      ),
                      const Divider(),
                      ListTile(
                        leading: const Icon(Icons.code, color: AppTheme.primaryColor),
                        title: const Text('IFSC Code'),
                        subtitle: Text(farmer.ifscCode ?? 'Not specified'),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Collect Milk Action Button (Module 3 placeholder)
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    backgroundColor: const Color(0xFF059669),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () {
                    Navigator.of(context).pushNamed(
                      AppRoutes.milkCollectionCreate,
                      arguments: farmer,
                    );
                  },
                  icon: const Icon(Icons.add_circle_outline, color: Colors.white),
                  label: const Text(
                    'Collect Milk from Farmer',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white),
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
