import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

/// Interactive/Visual Collection Trend Line Chart using CustomPainter
class CollectionTrendChart extends StatelessWidget {
  final List<double> dataPoints;
  final List<String> labels;
  final double height;

  const CollectionTrendChart({
    super.key,
    required this.dataPoints,
    required this.labels,
    this.height = 180,
  });

  @override
  Widget build(BuildContext context) {
    if (dataPoints.isEmpty) {
      return Container(
        height: height,
        alignment: Alignment.center,
        child: const Text(
          'No trend data available',
          style: TextStyle(color: AppTheme.textSecondary, fontSize: 13),
        ),
      );
    }

    return SizedBox(
      height: height,
      width: double.infinity,
      child: CustomPaint(
        painter: _LineChartPainter(
          dataPoints: dataPoints,
          labels: labels,
          lineColor: AppTheme.primaryColor,
          gradientStartColor: AppTheme.primaryColor.withValues(alpha: 0.25),
          gradientEndColor: AppTheme.primaryColor.withValues(alpha: 0.0),
        ),
      ),
    );
  }
}

class _LineChartPainter extends CustomPainter {
  final List<double> dataPoints;
  final List<String> labels;
  final Color lineColor;
  final Color gradientStartColor;
  final Color gradientEndColor;

  _LineChartPainter({
    required this.dataPoints,
    required this.labels,
    required this.lineColor,
    required this.gradientStartColor,
    required this.gradientEndColor,
  });

  @override
  void paint(Canvas canvas, Size size) {
    if (dataPoints.length < 2) return;

    final double paddingLeft = 36.0;
    final double paddingBottom = 28.0;
    final double paddingTop = 16.0;
    final double paddingRight = 16.0;

    final double chartWidth = size.width - paddingLeft - paddingRight;
    final double chartHeight = size.height - paddingTop - paddingBottom;

    final double minVal = dataPoints.reduce((a, b) => a < b ? a : b) * 0.8;
    final double maxVal = dataPoints.reduce((a, b) => a > b ? a : b) * 1.1;
    final double range = (maxVal - minVal) == 0 ? 1 : (maxVal - minVal);

    // Draw horizontal grid lines & Y-axis labels
    final Paint gridPaint = Paint()
      ..color = const Color(0xFFE2E8F0)
      ..strokeWidth = 1
      ..style = PaintingStyle.stroke;

    final TextPainter tp = TextPainter(
      textDirection: TextDirection.ltr,
    );

    const int gridSteps = 3;
    for (int i = 0; i <= gridSteps; i++) {
      final double y = paddingTop + chartHeight - (chartHeight / gridSteps * i);
      final double val = minVal + (range / gridSteps * i);

      // Grid line
      canvas.drawLine(
        Offset(paddingLeft, y),
        Offset(size.width - paddingRight, y),
        gridPaint,
      );

      // Label
      tp.text = TextSpan(
        text: _formatYLabel(val),
        style: const TextStyle(fontSize: 10, color: AppTheme.textMuted, fontWeight: FontWeight.w500),
      );
      tp.layout();
      tp.paint(canvas, Offset(paddingLeft - tp.width - 6, y - tp.height / 2));
    }

    // Calculate Points
    final List<Offset> points = [];
    final double stepX = chartWidth / (dataPoints.length - 1);

    for (int i = 0; i < dataPoints.length; i++) {
      final double x = paddingLeft + (i * stepX);
      final double normalizedY = (dataPoints[i] - minVal) / range;
      final double y = paddingTop + chartHeight - (normalizedY * chartHeight);
      points.add(Offset(x, y));
    }

    // Path creation for line & filled gradient
    final Path linePath = Path();
    linePath.moveTo(points.first.dx, points.first.dy);

    for (int i = 0; i < points.length - 1; i++) {
      final p1 = points[i];
      final p2 = points[i + 1];
      final controlPoint1 = Offset(p1.dx + (p2.dx - p1.dx) / 2, p1.dy);
      final controlPoint2 = Offset(p1.dx + (p2.dx - p1.dx) / 2, p2.dy);
      linePath.cubicTo(controlPoint1.dx, controlPoint1.dy, controlPoint2.dx, controlPoint2.dy, p2.dx, p2.dy);
    }

    // Gradient fill path
    final Path fillPath = Path.from(linePath);
    fillPath.lineTo(points.last.dx, paddingTop + chartHeight);
    fillPath.lineTo(points.first.dx, paddingTop + chartHeight);
    fillPath.close();

    final Paint fillPaint = Paint()
      ..shader = LinearGradient(
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
        colors: [gradientStartColor, gradientEndColor],
      ).createShader(Rect.fromLTWH(0, 0, size.width, size.height));

    canvas.drawPath(fillPath, fillPaint);

    // Draw Line
    final Paint linePaint = Paint()
      ..color = lineColor
      ..strokeWidth = 3
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    canvas.drawPath(linePath, linePaint);

    // Draw Data Point Dots
    final Paint dotOuterPaint = Paint()..color = Colors.white;
    final Paint dotInnerPaint = Paint()..color = lineColor;

    for (int i = 0; i < points.length; i++) {
      canvas.drawCircle(points[i], 5, dotOuterPaint);
      canvas.drawCircle(points[i], 3.5, dotInnerPaint);

      // X-Axis Labels
      if (labels.length > i && (i % ((labels.length / 5).ceil()) == 0 || i == labels.length - 1)) {
        tp.text = TextSpan(
          text: labels[i],
          style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary, fontWeight: FontWeight.w500),
        );
        tp.layout();
        tp.paint(canvas, Offset(points[i].dx - tp.width / 2, size.height - paddingBottom + 8));
      }
    }
  }

  String _formatYLabel(double val) {
    if (val >= 1000) {
      return '${(val / 1000).toStringAsFixed(0)}K';
    }
    return val.toStringAsFixed(0);
  }

  @override
  bool shouldRepaint(covariant _LineChartPainter oldDelegate) => true;
}

/// Shift Performance Donut Chart
class ShiftPerformanceDonutChart extends StatelessWidget {
  final double morningQty;
  final double eveningQty;

  const ShiftPerformanceDonutChart({
    super.key,
    required this.morningQty,
    required this.eveningQty,
  });

  @override
  Widget build(BuildContext context) {
    final total = morningQty + eveningQty;
    final morningPct = total > 0 ? (morningQty / total * 100) : 50.0;
    final eveningPct = total > 0 ? (eveningQty / total * 100) : 50.0;

    return Row(
      children: [
        SizedBox(
          width: 110,
          height: 110,
          child: CustomPaint(
            painter: _DonutChartPainter(
              morningQty: morningQty,
              eveningQty: eveningQty,
            ),
            child: Center(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    '${total.toStringAsFixed(0)} L',
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                  const Text(
                    'Total',
                    style: TextStyle(
                      fontSize: 11,
                      color: AppTheme.textSecondary,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
        const SizedBox(width: 20),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildLegendRow(
                color: const Color(0xFFEA580C),
                label: 'Morning Shift',
                value: '${morningQty.toStringAsFixed(1)} L (${morningPct.toStringAsFixed(1)}%)',
              ),
              const SizedBox(height: 12),
              _buildLegendRow(
                color: const Color(0xFF0284C7),
                label: 'Evening Shift',
                value: '${eveningQty.toStringAsFixed(1)} L (${eveningPct.toStringAsFixed(1)}%)',
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildLegendRow({required Color color, required String label, required String value}) {
    return Row(
      children: [
        Container(
          width: 12,
          height: 12,
          decoration: BoxDecoration(color: color, shape: BoxShape.circle),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w500, color: AppTheme.textSecondary),
              ),
              Text(
                value,
                style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

class _DonutChartPainter extends CustomPainter {
  final double morningQty;
  final double eveningQty;

  _DonutChartPainter({required this.morningQty, required this.eveningQty});

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = size.width / 2 - 8;
    final strokeWidth = 14.0;

    final total = morningQty + eveningQty;
    if (total == 0) {
      final paint = Paint()
        ..color = const Color(0xFFE2E8F0)
        ..style = PaintingStyle.stroke
        ..strokeWidth = strokeWidth;
      canvas.drawCircle(center, radius, paint);
      return;
    }

    final morningSweep = (morningQty / total) * 2 * 3.14159;
    final eveningSweep = (eveningQty / total) * 2 * 3.14159;

    final rect = Rect.fromCircle(center: center, radius: radius);

    final morningPaint = Paint()
      ..color = const Color(0xFFEA580C)
      ..style = PaintingStyle.stroke
      ..strokeWidth = strokeWidth
      ..strokeCap = StrokeCap.round;

    final eveningPaint = Paint()
      ..color = const Color(0xFF0284C7)
      ..style = PaintingStyle.stroke
      ..strokeWidth = strokeWidth
      ..strokeCap = StrokeCap.round;

    // Draw Morning Arc
    canvas.drawArc(rect, -3.14159 / 2, morningSweep - 0.05, false, morningPaint);

    // Draw Evening Arc
    canvas.drawArc(rect, -3.14159 / 2 + morningSweep, eveningSweep - 0.05, false, eveningPaint);
  }

  @override
  bool shouldRepaint(covariant _DonutChartPainter oldDelegate) => true;
}
