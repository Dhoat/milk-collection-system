import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../models/shop_model.dart';

class ShopCard extends StatelessWidget {
  final ShopModel shop;
  final VoidCallback onTap;
  final VoidCallback onEdit;
  final VoidCallback onToggleStatus;
  final VoidCallback onDelete;

  const ShopCard({
    super.key,
    required this.shop,
    required this.onTap,
    required this.onEdit,
    required this.onToggleStatus,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    final villageName = shop.village?.name ?? (shop.area ?? 'Local Area');

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
                    radius: 22,
                    backgroundColor: shop.status
                        ? AppTheme.primaryColor.withValues(alpha: 0.1)
                        : Colors.grey.withValues(alpha: 0.2),
                    child: Icon(
                      Icons.storefront,
                      color: shop.status ? AppTheme.primaryColor : Colors.grey,
                      size: 24,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          shop.name,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: AppTheme.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          'Code: ${shop.shopCode}  ·  Owner: ${shop.ownerName}',
                          style: const TextStyle(
                            fontSize: 12,
                            color: AppTheme.textSecondary,
                          ),
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: shop.status
                          ? const Color(0xFFD1FAE5)
                          : const Color(0xFFF3F4F6),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: shop.status
                            ? const Color(0xFFA7F3D0)
                            : const Color(0xFFE5E7EB),
                      ),
                    ),
                    child: Text(
                      shop.statusDisplayName,
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: shop.status
                            ? const Color(0xFF065F46)
                            : const Color(0xFF6B7280),
                      ),
                    ),
                  ),
                  PopupMenuButton<String>(
                    onSelected: (value) {
                      if (value == 'edit') onEdit();
                      if (value == 'toggle') onToggleStatus();
                      if (value == 'delete') onDelete();
                    },
                    itemBuilder: (context) => [
                      const PopupMenuItem(
                        value: 'edit',
                        child: Row(
                          children: [
                            Icon(Icons.edit_outlined, size: 18, color: AppTheme.primaryColor),
                            SizedBox(width: 8),
                            Text('Edit Shop'),
                          ],
                        ),
                      ),
                      PopupMenuItem(
                        value: 'toggle',
                        child: Row(
                          children: [
                            Icon(
                              shop.status ? Icons.block_outlined : Icons.check_circle_outline,
                              size: 18,
                              color: shop.status ? Colors.orange : Colors.green,
                            ),
                            const SizedBox(width: 8),
                            Text(shop.status ? 'Deactivate Shop' : 'Activate Shop'),
                          ],
                        ),
                      ),
                      const PopupMenuItem(
                        value: 'delete',
                        child: Row(
                          children: [
                            Icon(Icons.delete_outline, size: 18, color: Colors.red),
                            SizedBox(width: 8),
                            Text('Delete Shop', style: TextStyle(color: Colors.red)),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 12),

              Row(
                children: [
                  const Icon(Icons.phone_outlined, size: 14, color: AppTheme.textSecondary),
                  const SizedBox(width: 4),
                  Text(
                    shop.phone,
                    style: const TextStyle(fontSize: 13, color: AppTheme.textPrimary),
                  ),
                  const SizedBox(width: 16),
                  const Icon(Icons.location_on_outlined, size: 14, color: AppTheme.textSecondary),
                  const SizedBox(width: 4),
                  Expanded(
                    child: Text(
                      villageName,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 13, color: AppTheme.textPrimary),
                    ),
                  ),
                  if (shop.creditLimit > 0) ...[
                    const SizedBox(width: 8),
                    Text(
                      'Limit: ₹${shop.creditLimit.toStringAsFixed(0)}',
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: AppTheme.primaryColor,
                      ),
                    ),
                  ],
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
