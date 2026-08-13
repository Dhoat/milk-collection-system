import '../../shops/models/shop_model.dart';
import 'daily_report_model.dart';

class MonthlyCollectionSummaryModel {
  final double totalLitres;
  final int farmersCount;
  final double avgDaily;
  final double totalAmount;
  final double avgFat;
  final double avgSnf;
  final List<VillageBreakdownModel> villageBreakdown;

  MonthlyCollectionSummaryModel({
    required this.totalLitres,
    required this.farmersCount,
    required this.avgDaily,
    required this.totalAmount,
    required this.avgFat,
    required this.avgSnf,
    required this.villageBreakdown,
  });

  factory MonthlyCollectionSummaryModel.fromJson(Map<String, dynamic> json) {
    return MonthlyCollectionSummaryModel(
      totalLitres: json['total_litres'] != null ? double.parse(json['total_litres'].toString()) : 0.0,
      farmersCount: json['farmers_count'] is int ? json['farmers_count'] : int.parse((json['farmers_count'] ?? 0).toString()),
      avgDaily: json['avg_daily'] != null ? double.parse(json['avg_daily'].toString()) : 0.0,
      totalAmount: json['total_amount'] != null ? double.parse(json['total_amount'].toString()) : 0.0,
      avgFat: json['avg_fat'] != null ? double.parse(json['avg_fat'].toString()) : 0.0,
      avgSnf: json['avg_snf'] != null ? double.parse(json['avg_snf'].toString()) : 0.0,
      villageBreakdown: (json['village_breakdown'] as List<dynamic>?)
              ?.map((v) => VillageBreakdownModel.fromJson(v))
              .toList() ??
          [],
    );
  }
}

class MonthlyCenterSummaryModel {
  final double totalReceived;
  final double avgDaily;
  final double openingStock;
  final double stockIn;
  final double stockOut;
  final double closingStock;

  MonthlyCenterSummaryModel({
    required this.totalReceived,
    required this.avgDaily,
    required this.openingStock,
    required this.stockIn,
    required this.stockOut,
    required this.closingStock,
  });

  factory MonthlyCenterSummaryModel.fromJson(Map<String, dynamic> json) {
    return MonthlyCenterSummaryModel(
      totalReceived: json['total_received'] != null ? double.parse(json['total_received'].toString()) : 0.0,
      avgDaily: json['avg_daily'] != null ? double.parse(json['avg_daily'].toString()) : 0.0,
      openingStock: json['opening_stock'] != null ? double.parse(json['opening_stock'].toString()) : 0.0,
      stockIn: json['stock_in'] != null ? double.parse(json['stock_in'].toString()) : 0.0,
      stockOut: json['stock_out'] != null ? double.parse(json['stock_out'].toString()) : 0.0,
      closingStock: json['closing_stock'] != null ? double.parse(json['closing_stock'].toString()) : 0.0,
    );
  }
}

class TopShopSummaryModel {
  final int shopId;
  final int totalOrders;
  final double totalSales;
  final ShopModel? shop;

  TopShopSummaryModel({
    required this.shopId,
    required this.totalOrders,
    required this.totalSales,
    this.shop,
  });

  factory TopShopSummaryModel.fromJson(Map<String, dynamic> json) {
    return TopShopSummaryModel(
      shopId: json['shop_id'] is int ? json['shop_id'] : int.parse(json['shop_id'].toString()),
      totalOrders: json['total_orders'] is int ? json['total_orders'] : int.parse((json['total_orders'] ?? 0).toString()),
      totalSales: json['total_sales'] != null ? double.parse(json['total_sales'].toString()) : 0.0,
      shop: json['shop'] != null && json['shop'] is Map<String, dynamic>
          ? ShopModel.fromJson(json['shop'])
          : null,
    );
  }
}

class MonthlyOrderSummaryModel {
  final int totalCount;
  final double totalValue;
  final Map<String, int> byStatus;
  final List<TopShopSummaryModel> topShops;

  MonthlyOrderSummaryModel({
    required this.totalCount,
    required this.totalValue,
    required this.byStatus,
    required this.topShops,
  });

  factory MonthlyOrderSummaryModel.fromJson(Map<String, dynamic> json) {
    final statusMap = <String, int>{};
    if (json['by_status'] is Map) {
      (json['by_status'] as Map).forEach((key, val) {
        statusMap[key.toString()] = val is int ? val : int.tryParse(val.toString()) ?? 0;
      });
    }

    return MonthlyOrderSummaryModel(
      totalCount: json['total_count'] is int ? json['total_count'] : int.parse((json['total_count'] ?? 0).toString()),
      totalValue: json['total_value'] != null ? double.parse(json['total_value'].toString()) : 0.0,
      byStatus: statusMap,
      topShops: (json['top_shops'] as List<dynamic>?)
              ?.map((s) => TopShopSummaryModel.fromJson(s))
              .toList() ??
          [],
    );
  }
}

class MonthlyReportModel {
  final int month;
  final int year;
  final String startDate;
  final String endDate;
  final int daysInMonth;
  final MonthlyCollectionSummaryModel collection;
  final MonthlyCenterSummaryModel center;
  final MonthlyOrderSummaryModel orders;
  final ProductSummaryModel products;
  final DeliverySummaryModel deliveries;
  final FinancialSummaryModel financial;

  MonthlyReportModel({
    required this.month,
    required this.year,
    required this.startDate,
    required this.endDate,
    required this.daysInMonth,
    required this.collection,
    required this.center,
    required this.orders,
    required this.products,
    required this.deliveries,
    required this.financial,
  });

  factory MonthlyReportModel.fromJson(Map<String, dynamic> json) {
    return MonthlyReportModel(
      month: json['month'] is int ? json['month'] : int.parse((json['month'] ?? 1).toString()),
      year: json['year'] is int ? json['year'] : int.parse((json['year'] ?? 2026).toString()),
      startDate: json['start_date'] ?? '',
      endDate: json['end_date'] ?? '',
      daysInMonth: json['days_in_month'] is int ? json['days_in_month'] : int.parse((json['days_in_month'] ?? 30).toString()),
      collection: MonthlyCollectionSummaryModel.fromJson(json['collection'] ?? {}),
      center: MonthlyCenterSummaryModel.fromJson(json['center'] ?? {}),
      orders: MonthlyOrderSummaryModel.fromJson(json['orders'] ?? {}),
      products: ProductSummaryModel.fromJson(json['products'] ?? {}),
      deliveries: DeliverySummaryModel.fromJson(json['deliveries'] ?? {}),
      financial: FinancialSummaryModel.fromJson(json['financial'] ?? {}),
    );
  }

  String get monthName {
    const months = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ];
    if (month >= 1 && month <= 12) {
      return months[month - 1];
    }
    return 'Month $month';
  }
}
