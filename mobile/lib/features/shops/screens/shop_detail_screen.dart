import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../app/routes/app_routes.dart';
import '../../../core/theme/app_theme.dart';
import '../models/shop_model.dart';
import '../providers/shop_provider.dart';

class ShopDetailScreen extends StatelessWidget {
  final ShopModel shop;

  const ShopDetailScreen({super.key, required this.shop});

  void _confirmDelete(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Shop?'),
        content: Text('Are you sure you want to delete ${shop.name} (${shop.shopCode})?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.of(ctx).pop();
              final provider = Provider.of<ShopProvider>(context, listen: false);
              final success = await provider.deleteShop(shop.id);
              if (success && context.mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Shop deleted.'),
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
    final villageName = shop.village?.name ?? 'Not Assigned';

    return Scaffold(
      appBar: AppBar(
        title: Text(shop.name),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit_outlined),
            tooltip: 'Edit Shop',
            onPressed: () {
              Navigator.of(context).pushNamed(
                AppRoutes.shopEdit,
                arguments: shop,
              );
            },
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline, color: Colors.red),
            tooltip: 'Delete Shop',
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
                        backgroundColor: shop.status
                            ? AppTheme.primaryColor.withValues(alpha: 0.1)
                            : Colors.grey.withValues(alpha: 0.2),
                        child: Icon(
                          Icons.storefront,
                          size: 32,
                          color: shop.status ? AppTheme.primaryColor : Colors.grey,
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              shop.name,
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            Text(
                              'Code: ${shop.shopCode}',
                              style: const TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: AppTheme.primaryColor,
                              ),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              'Owner: ${shop.ownerName}',
                              style: const TextStyle(
                                fontSize: 13,
                                color: AppTheme.textSecondary,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: shop.status
                              ? const Color(0xFFD1FAE5)
                              : const Color(0xFFF3F4F6),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: shop.status
                                ? const Color(0xFFA7F3D0)
                                : const Color(0xFFE5E7EB),
                          ),
                        ),
                        child: Text(
                          shop.statusDisplayName,
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: shop.status
                                ? const Color(0xFF065F46)
                                : const Color(0xFF6B7280),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Contact & Location Info Card
              Card(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    children: [
                      ListTile(
                        leading: const Icon(Icons.phone_outlined, color: AppTheme.primaryColor),
                        title: const Text('Phone Number'),
                        subtitle: Text(shop.phone),
                      ),
                      if (shop.email != null && shop.email!.isNotEmpty) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.email_outlined, color: AppTheme.primaryColor),
                          title: const Text('Email Address'),
                          subtitle: Text(shop.email!),
                        ),
                      ],
                      const Divider(),
                      ListTile(
                        leading: const Icon(Icons.location_city, color: AppTheme.primaryColor),
                        title: const Text('Village / Area'),
                        subtitle: Text('$villageName ${shop.area != null ? "(${shop.area})" : ""}'),
                      ),
                      if (shop.address != null && shop.address!.isNotEmpty) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.home_outlined, color: AppTheme.primaryColor),
                          title: const Text('Full Address'),
                          subtitle: Text(shop.address!),
                        ),
                      ],
                      if (shop.creditLimit > 0) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.credit_card, color: AppTheme.primaryColor),
                          title: const Text('Approved Credit Limit'),
                          subtitle: Text('₹${shop.creditLimit.toStringAsFixed(2)}'),
                        ),
                      ],
                      if (shop.notes != null && shop.notes!.isNotEmpty) ...[
                        const Divider(),
                        ListTile(
                          leading: const Icon(Icons.note_alt_outlined, color: AppTheme.primaryColor),
                          title: const Text('Remarks / Notes'),
                          subtitle: Text(shop.notes!),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Action Buttons
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: () {
                        Navigator.of(context).pushNamed(
                          AppRoutes.shopOrderCreate,
                          arguments: shop,
                        );
                      },
                      icon: const Icon(Icons.add_shopping_cart),
                      label: const Text('New Shop Order'),
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
