import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../providers/report_provider.dart';

class ReportFilterBar extends StatelessWidget {
  const ReportFilterBar({super.key});

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<ReportProvider>(context);
    final isDaily = provider.mode == ReportMode.daily;

    return Container(
      padding: const EdgeInsets.all(16.0),
      decoration: BoxDecoration(
        color: AppTheme.surfaceColor,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        children: [
          // Daily vs Monthly Mode Segmented Control
          SegmentedButton<ReportMode>(
            segments: const [
              ButtonSegment<ReportMode>(
                value: ReportMode.daily,
                label: Text('Daily Report'),
                icon: Icon(Icons.today),
              ),
              ButtonSegment<ReportMode>(
                value: ReportMode.monthly,
                label: Text('Monthly Report'),
                icon: Icon(Icons.calendar_month),
              ),
            ],
            selected: {provider.mode},
            onSelectionChanged: (newSelection) {
              provider.setMode(newSelection.first);
            },
          ),
          const SizedBox(height: 12),

          // Date or Month/Year Selector Row
          if (isDaily) ...[
            InkWell(
              onTap: () async {
                final picked = await showDatePicker(
                  context: context,
                  initialDate: provider.selectedDate,
                  firstDate: DateTime(2020),
                  lastDate: DateTime(2030),
                );
                if (picked != null) {
                  provider.setDate(picked);
                }
              },
              borderRadius: BorderRadius.circular(12),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                decoration: BoxDecoration(
                  border: Border.all(color: AppTheme.primaryColor.withValues(alpha: 0.3)),
                  borderRadius: BorderRadius.circular(12),
                  color: AppTheme.primaryColor.withValues(alpha: 0.05),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.calendar_today, color: AppTheme.primaryColor, size: 20),
                        const SizedBox(width: 10),
                        Text(
                          'Date: ${provider.selectedDate.year}-${provider.selectedDate.month.toString().padLeft(2, '0')}-${provider.selectedDate.day.toString().padLeft(2, '0')}',
                          style: const TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                            color: AppTheme.textPrimary,
                          ),
                        ),
                      ],
                    ),
                    const Icon(Icons.arrow_drop_down, color: AppTheme.primaryColor),
                  ],
                ),
              ),
            ),
          ] else ...[
            Row(
              children: [
                Expanded(
                  child: DropdownButtonFormField<int>(
                    initialValue: provider.selectedMonth,
                    decoration: const InputDecoration(
                      labelText: 'Month',
                      prefixIcon: Icon(Icons.event, size: 20),
                    ),
                    items: const [
                      DropdownMenuItem(value: 1, child: Text('January')),
                      DropdownMenuItem(value: 2, child: Text('February')),
                      DropdownMenuItem(value: 3, child: Text('March')),
                      DropdownMenuItem(value: 4, child: Text('April')),
                      DropdownMenuItem(value: 5, child: Text('May')),
                      DropdownMenuItem(value: 6, child: Text('June')),
                      DropdownMenuItem(value: 7, child: Text('July')),
                      DropdownMenuItem(value: 8, child: Text('August')),
                      DropdownMenuItem(value: 9, child: Text('September')),
                      DropdownMenuItem(value: 10, child: Text('October')),
                      DropdownMenuItem(value: 11, child: Text('November')),
                      DropdownMenuItem(value: 12, child: Text('December')),
                    ],
                    onChanged: (m) {
                      if (m != null) {
                        provider.setMonthYear(m, provider.selectedYear);
                      }
                    },
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: DropdownButtonFormField<int>(
                    initialValue: provider.selectedYear,
                    decoration: const InputDecoration(
                      labelText: 'Year',
                      prefixIcon: Icon(Icons.calendar_month, size: 20),
                    ),
                    items: [2024, 2025, 2026, 2027, 2028]
                        .map((y) => DropdownMenuItem(value: y, child: Text('$y')))
                        .toList(),
                    onChanged: (y) {
                      if (y != null) {
                        provider.setMonthYear(provider.selectedMonth, y);
                      }
                    },
                  ),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }
}
