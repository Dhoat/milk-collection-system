class VillageBreakdownModel {
  final int villageId;
  final String villageName;
  final double totalLitres;
  final double totalAmount;
  final int farmersCount;

  VillageBreakdownModel({
    required this.villageId,
    required this.villageName,
    required this.totalLitres,
    required this.totalAmount,
    required this.farmersCount,
  });

  factory VillageBreakdownModel.fromJson(Map<String, dynamic> json) {
    return VillageBreakdownModel(
      villageId: json['village_id'] is int ? json['village_id'] : int.parse(json['village_id'].toString()),
      villageName: json['village_name'] ?? '',
      totalLitres: json['total_litres'] != null ? double.parse(json['total_litres'].toString()) : 0.0,
      totalAmount: json['total_amount'] != null ? double.parse(json['total_amount'].toString()) : 0.0,
      farmersCount: json['farmers_count'] is int ? json['farmers_count'] : int.parse(json['farmers_count'].toString()),
    );
  }
}

class CollectionSummaryModel {
  final int farmersCount;
  final double totalLitres;
  final double avgPerFarmer;
  final double totalAmount;
  final double avgFat;
  final double avgSnf;
  final List<VillageBreakdownModel> villageBreakdown;

  CollectionSummaryModel({
    required this.farmersCount,
    required this.totalLitres,
    required this.avgPerFarmer,
    required this.totalAmount,
    required this.avgFat,
    required this.avgSnf,
    required this.villageBreakdown,
  });

  factory CollectionSummaryModel.fromJson(Map<String, dynamic> json) {
    return CollectionSummaryModel(
      farmersCount: json['farmers_count'] is int ? json['farmers_count'] : int.parse((json['farmers_count'] ?? 0).toString()),
      totalLitres: json['total_litres'] != null ? double.parse(json['total_litres'].toString()) : 0.0,
      avgPerFarmer: json['avg_per_farmer'] != null ? double.parse(json['avg_per_farmer'].toString()) : 0.0,
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

class CenterSummaryModel {
  final double totalReceived;
  final int recordsCount;
  final double diffLitres;

  CenterSummaryModel({
    required this.totalReceived,
    required this.recordsCount,
    required this.diffLitres,
  });

  factory CenterSummaryModel.fromJson(Map<String, dynamic> json) {
    return CenterSummaryModel(
      totalReceived: json['total_received'] != null ? double.parse(json['total_received'].toString()) : 0.0,
      recordsCount: json['records_count'] is int ? json['records_count'] : int.parse((json['records_count'] ?? 0).toString()),
      diffLitres: json['diff_litres'] != null ? double.parse(json['diff_litres'].toString()) : 0.0,
    );
  }
}

class StockSummaryModel {
  final double opening;
  final double stockIn;
  final double stockOut;
  final double closing;

  StockSummaryModel({
    required this.opening,
    required this.stockIn,
    required this.stockOut,
    required this.closing,
  });

  factory StockSummaryModel.fromJson(Map<String, dynamic> json) {
    return StockSummaryModel(
      opening: json['opening'] != null ? double.parse(json['opening'].toString()) : 0.0,
      stockIn: json['in'] != null ? double.parse(json['in'].toString()) : 0.0,
      stockOut: json['out'] != null ? double.parse(json['out'].toString()) : 0.0,
      closing: json['closing'] != null ? double.parse(json['closing'].toString()) : 0.0,
    );
  }
}

class OrderSummaryModel {
  final int totalCount;
  final double totalValue;
  final Map<String, int> byStatus;

  OrderSummaryModel({
    required this.totalCount,
    required this.totalValue,
    required this.byStatus,
  });

  factory OrderSummaryModel.fromJson(Map<String, dynamic> json) {
    final statusMap = <String, int>{};
    if (json['by_status'] is Map) {
      (json['by_status'] as Map).forEach((key, val) {
        statusMap[key.toString()] = val is int ? val : int.tryParse(val.toString()) ?? 0;
      });
    }

    return OrderSummaryModel(
      totalCount: json['total_count'] is int ? json['total_count'] : int.parse((json['total_count'] ?? 0).toString()),
      totalValue: json['total_value'] != null ? double.parse(json['total_value'].toString()) : 0.0,
      byStatus: statusMap,
    );
  }
}

class DeliverySummaryModel {
  final int totalCount;
  final Map<String, int> byStatus;

  DeliverySummaryModel({
    required this.totalCount,
    required this.byStatus,
  });

  factory DeliverySummaryModel.fromJson(Map<String, dynamic> json) {
    final statusMap = <String, int>{};
    if (json['by_status'] is Map) {
      (json['by_status'] as Map).forEach((key, val) {
        statusMap[key.toString()] = val is int ? val : int.tryParse(val.toString()) ?? 0;
      });
    }

    return DeliverySummaryModel(
      totalCount: json['total_count'] is int ? json['total_count'] : int.parse((json['total_count'] ?? 0).toString()),
      byStatus: statusMap,
    );
  }
}

class ProductSalesModel {
  final int productId;
  final String productName;
  final String unit;
  final double totalQty;
  final double totalSales;

  ProductSalesModel({
    required this.productId,
    required this.productName,
    required this.unit,
    required this.totalQty,
    required this.totalSales,
  });

  factory ProductSalesModel.fromJson(Map<String, dynamic> json) {
    return ProductSalesModel(
      productId: json['product_id'] is int ? json['product_id'] : int.parse(json['product_id'].toString()),
      productName: json['product_name'] ?? '',
      unit: json['unit'] ?? '',
      totalQty: json['total_qty'] != null ? double.parse(json['total_qty'].toString()) : 0.0,
      totalSales: json['total_sales'] != null ? double.parse(json['total_sales'].toString()) : 0.0,
    );
  }
}

class ProductSummaryModel {
  final double totalUnits;
  final List<ProductSalesModel> items;

  ProductSummaryModel({
    required this.totalUnits,
    required this.items,
  });

  factory ProductSummaryModel.fromJson(Map<String, dynamic> json) {
    return ProductSummaryModel(
      totalUnits: json['total_units'] != null ? double.parse(json['total_units'].toString()) : 0.0,
      items: (json['items'] as List<dynamic>?)
              ?.map((p) => ProductSalesModel.fromJson(p))
              .toList() ??
          [],
    );
  }
}

class FinancialSummaryModel {
  final double totalSales;
  final double milkExpense;
  final double netBalance;

  FinancialSummaryModel({
    required this.totalSales,
    required this.milkExpense,
    required this.netBalance,
  });

  factory FinancialSummaryModel.fromJson(Map<String, dynamic> json) {
    return FinancialSummaryModel(
      totalSales: json['total_sales'] != null ? double.parse(json['total_sales'].toString()) : 0.0,
      milkExpense: json['milk_expense'] != null ? double.parse(json['milk_expense'].toString()) : 0.0,
      netBalance: json['net_balance'] != null ? double.parse(json['net_balance'].toString()) : 0.0,
    );
  }
}

class DailyReportModel {
  final String date;
  final CollectionSummaryModel collection;
  final CenterSummaryModel center;
  final StockSummaryModel stock;
  final OrderSummaryModel orders;
  final DeliverySummaryModel deliveries;
  final ProductSummaryModel products;
  final FinancialSummaryModel financial;

  DailyReportModel({
    required this.date,
    required this.collection,
    required this.center,
    required this.stock,
    required this.orders,
    required this.deliveries,
    required this.products,
    required this.financial,
  });

  factory DailyReportModel.fromJson(Map<String, dynamic> json) {
    return DailyReportModel(
      date: json['date'] ?? '',
      collection: CollectionSummaryModel.fromJson(json['collection'] ?? {}),
      center: CenterSummaryModel.fromJson(json['center'] ?? {}),
      stock: StockSummaryModel.fromJson(json['stock'] ?? {}),
      orders: OrderSummaryModel.fromJson(json['orders'] ?? {}),
      deliveries: DeliverySummaryModel.fromJson(json['deliveries'] ?? {}),
      products: ProductSummaryModel.fromJson(json['products'] ?? {}),
      financial: FinancialSummaryModel.fromJson(json['financial'] ?? {}),
    );
  }
}
