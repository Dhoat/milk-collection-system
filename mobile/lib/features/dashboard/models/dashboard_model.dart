class DashboardKpis {
  final int totalFarmers;
  final int activeFarmers;
  final int totalVillages;
  final int activeVillages;
  final double todayQuantity;
  final double todayAmount;

  DashboardKpis({
    required this.totalFarmers,
    required this.activeFarmers,
    required this.totalVillages,
    required this.activeVillages,
    required this.todayQuantity,
    required this.todayAmount,
  });

  factory DashboardKpis.fromJson(Map<String, dynamic> json) {
    return DashboardKpis(
      totalFarmers: (json['total_farmers'] as num?)?.toInt() ?? 0,
      activeFarmers: (json['active_farmers'] as num?)?.toInt() ?? 0,
      totalVillages: (json['total_villages'] as num?)?.toInt() ?? 0,
      activeVillages: (json['active_villages'] as num?)?.toInt() ?? 0,
      todayQuantity: (json['today_quantity'] as num?)?.toDouble() ?? 0.0,
      todayAmount: (json['today_amount'] as num?)?.toDouble() ?? 0.0,
    );
  }
}

class ShiftOverview {
  final double quantity;
  final int farmers;

  ShiftOverview({
    required this.quantity,
    required this.farmers,
  });

  factory ShiftOverview.fromJson(Map<String, dynamic> json) {
    return ShiftOverview(
      quantity: (json['quantity'] as num?)?.toDouble() ?? 0.0,
      farmers: (json['farmers'] as num?)?.toInt() ?? 0,
    );
  }
}

class TodayOverview {
  final ShiftOverview morning;
  final ShiftOverview evening;
  final ShiftOverview total;

  TodayOverview({
    required this.morning,
    required this.evening,
    required this.total,
  });

  factory TodayOverview.fromJson(Map<String, dynamic> json) {
    return TodayOverview(
      morning: ShiftOverview.fromJson(json['morning'] as Map<String, dynamic>? ?? {}),
      evening: ShiftOverview.fromJson(json['evening'] as Map<String, dynamic>? ?? {}),
      total: ShiftOverview.fromJson(json['total'] as Map<String, dynamic>? ?? {}),
    );
  }
}

class CollectionTrendItem {
  final String date;
  final String label;
  final String fullLabel;
  final double litres;

  CollectionTrendItem({
    required this.date,
    required this.label,
    required this.fullLabel,
    required this.litres,
  });

  factory CollectionTrendItem.fromJson(Map<String, dynamic> json) {
    return CollectionTrendItem(
      date: json['date']?.toString() ?? '',
      label: json['label']?.toString() ?? '',
      fullLabel: json['full_label']?.toString() ?? '',
      litres: (json['litres'] as num?)?.toDouble() ?? 0.0,
    );
  }
}

class RecentActivityItemModel {
  final String type;
  final String message;
  final String detail;
  final String timestamp;
  final String icon;

  RecentActivityItemModel({
    required this.type,
    required this.message,
    required this.detail,
    required this.timestamp,
    required this.icon,
  });

  factory RecentActivityItemModel.fromJson(Map<String, dynamic> json) {
    return RecentActivityItemModel(
      type: json['type']?.toString() ?? 'info',
      message: json['message']?.toString() ?? '',
      detail: json['detail']?.toString() ?? '',
      timestamp: json['timestamp']?.toString() ?? '',
      icon: json['icon']?.toString() ?? 'info',
    );
  }
}

class DashboardModel {
  final String today;
  final DashboardKpis kpis;
  final TodayOverview todayOverview;
  final List<CollectionTrendItem> collectionTrend;
  final List<RecentActivityItemModel> recentActivity;

  DashboardModel({
    required this.today,
    required this.kpis,
    required this.todayOverview,
    required this.collectionTrend,
    required this.recentActivity,
  });

  factory DashboardModel.fromJson(Map<String, dynamic> json) {
    final trendList = (json['collectionTrend'] as List<dynamic>?)
            ?.map((item) => CollectionTrendItem.fromJson(item as Map<String, dynamic>))
            .toList() ??
        [];

    final activityList = (json['recentActivity'] as List<dynamic>?)
            ?.map((item) => RecentActivityItemModel.fromJson(item as Map<String, dynamic>))
            .toList() ??
        [];

    return DashboardModel(
      today: json['today']?.toString() ?? '',
      kpis: DashboardKpis.fromJson(json['kpis'] as Map<String, dynamic>? ?? {}),
      todayOverview: TodayOverview.fromJson(json['todayOverview'] as Map<String, dynamic>? ?? {}),
      collectionTrend: trendList,
      recentActivity: activityList,
    );
  }
}
