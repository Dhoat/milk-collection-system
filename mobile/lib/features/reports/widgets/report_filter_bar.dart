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
      padding: const EdgeInsets.symmetric(horizontal: 14.0, vertical: 12.0),
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
          SizedBox(
            width: double.infinity,
            child: SegmentedButton<ReportMode>(
              style: SegmentedButton.styleFrom(
                visualDensity: VisualDensity.compact,
                padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 0),
              ),
              showSelectedIcon: false,
              segments: const [
                ButtonSegment<ReportMode>(
                  value: ReportMode.daily,
                  label: Text('Daily Report', overflow: TextOverflow.ellipsis),
                  icon: Icon(Icons.today, size: 18),
                ),
                ButtonSegment<ReportMode>(
                  value: ReportMode.monthly,
                  label: Text('Monthly Report', overflow: TextOverflow.ellipsis),
                  icon: Icon(Icons.calendar_month, size: 18),
                ),
              ],
              selected: {provider.mode},
              onSelectionChanged: (newSelection) {
                provider.setMode(newSelection.first);
              },
            ),
          ),
          const SizedBox(height: 10),

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
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                decoration: BoxDecoration(
                  border: Border.all(color: AppTheme.primaryColor.withValues(alpha: 0.3)),
                  borderRadius: BorderRadius.circular(12),
                  color: AppTheme.primaryColor.withValues(alpha: 0.05),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Row(
                        children: [
                          const Icon(Icons.calendar_today, color: AppTheme.primaryColor, size: 18),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              'Date: ${provider.selectedDate.year}-${provider.selectedDate.month.toString().padLeft(2, '0')}-${provider.selectedDate.day.toString().padLeft(2, '0')}',
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                          ),
                        ],
                      ),
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
                    isExpanded: true,
                    initialValue: provider.selectedMonth,
                    decoration: const InputDecoration(
                      labelText: 'Month',
                      prefixIcon: Icon(Icons.event, size: 18),
                      contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                    ),
                    items: const [
                      DropdownMenuItem(value: 1, child: Text('January', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 2, child: Text('February', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 3, child: Text('March', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 4, child: Text('April', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 5, child: Text('May', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 6, child: Text('June', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 7, child: Text('July', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 8, child: Text('August', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 9, child: Text('September', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 10, child: Text('October', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 11, child: Text('November', overflow: TextOverflow.ellipsis)),
                      DropdownMenuItem(value: 12, child: Text('December', overflow: TextOverflow.ellipsis)),
                    ],
                    onChanged: (m) {
                      if (m != null) {
                        provider.setMonthYear(m, provider.selectedYear);
                      }
                    },
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: DropdownButtonFormField<int>(
                    isExpanded: true,
                    initialValue: provider.selectedYear,
                    decoration: const InputDecoration(
                      labelText: 'Year',
                      prefixIcon: Icon(Icons.calendar_month, size: 18),
                      contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                    ),
                    items: [2024, 2025, 2026, 2027, 2028]
                        .map((y) => DropdownMenuItem(value: y, child: Text('$y', overflow: TextOverflow.ellipsis)))
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
