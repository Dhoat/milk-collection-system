import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';

class MilkRateCalculator extends StatelessWidget {
  final double quantity;
  final double? fat;
  final double? snf;
  final double rate;
  final bool isFinal;

  const MilkRateCalculator({
    super.key,
    required this.quantity,
    this.fat,
    this.snf,
    required this.rate,
    this.isFinal = false,
  });

  double get estimatedAmount => quantity * rate;

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(14),
        side: BorderSide(
          color: isFinal
              ? AppTheme.primaryColor
              : AppTheme.accentColor.withValues(alpha: 0.5),
          width: 1.5,
        ),
      ),
      color: isFinal
          ? const Color(0xFFECFDF5)
          : const Color(0xFFFFFBEB),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(
                  isFinal ? Icons.verified_outlined : Icons.calculate_outlined,
                  color: isFinal ? AppTheme.primaryColor : const Color(0xFFD97706),
                  size: 20,
                ),
                const SizedBox(width: 8),
                Text(
                  isFinal ? 'Authoritative Calculation' : 'Estimated Preview',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: isFinal ? AppTheme.primaryColor : const Color(0xFFB45309),
                  ),
                ),
                const Spacer(),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  decoration: BoxDecoration(
                    color: isFinal
                        ? AppTheme.primaryColor.withValues(alpha: 0.1)
                        : const Color(0xFFFEF3C7),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    isFinal ? 'Server Verified' : 'Live Estimate',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: isFinal ? AppTheme.primaryColor : const Color(0xFF92400E),
                    ),
                  ),
                ),
              ],
            ),
            const Divider(height: 20),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                _buildStatItem('Quantity', '${quantity.toStringAsFixed(1)} L'),
                _buildStatItem('Fat', fat != null ? '${fat!.toStringAsFixed(1)} %' : 'N/A'),
                _buildStatItem('SNF', snf != null ? '${snf!.toStringAsFixed(1)} %' : 'N/A'),
                _buildStatItem('Rate', '₹ ${rate.toStringAsFixed(2)}'),
              ],
            ),
            const SizedBox(height: 14),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(
                  color: isFinal ? const Color(0xFFA7F3D0) : const Color(0xFFFDE68A),
                ),
              ),
              child: Row(
                children: [
                  const Text(
                    'Total Payable Amount:',
                    style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                  const Spacer(),
                  Text(
                    '₹ ${estimatedAmount.toStringAsFixed(2)}',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: isFinal ? AppTheme.primaryColor : const Color(0xFFD97706),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatItem(String label, String value) {
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
        const SizedBox(height: 2),
        Text(
          value,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.bold,
            color: AppTheme.textPrimary,
          ),
        ),
      ],
    );
  }
}
