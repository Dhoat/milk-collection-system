import '../../milk_receiving/models/milk_receiving_model.dart';

class MilkStockModel {
  final int id;
  final String transactionDate;
  final String type; // 'in' or 'out'
  final String itemType; // 'raw_milk', etc.
  final double quantity;
  final double? fat;
  final double? snf;
  final int? milkReceivingId;
  final int? createdBy;
  final String sourceOrReason;
  final String? notes;
  final MilkReceivingModel? milkReceiving;
  final String? createdAt;
  final String? updatedAt;

  MilkStockModel({
    required this.id,
    required this.transactionDate,
    required this.type,
    required this.itemType,
    required this.quantity,
    this.fat,
    this.snf,
    this.milkReceivingId,
    this.createdBy,
    required this.sourceOrReason,
    this.notes,
    this.milkReceiving,
    this.createdAt,
    this.updatedAt,
  });

  factory MilkStockModel.fromJson(Map<String, dynamic> json) {
    return MilkStockModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      transactionDate: json['transaction_date'] ?? '',
      type: json['type'] ?? 'in',
      itemType: json['item_type'] ?? 'raw_milk',
      quantity: json['quantity'] != null
          ? (json['quantity'] as num).toDouble()
          : 0.0,
      fat: json['fat'] != null ? (json['fat'] as num).toDouble() : null,
      snf: json['snf'] != null ? (json['snf'] as num).toDouble() : null,
      milkReceivingId: json['milk_receiving_id'] != null
          ? (json['milk_receiving_id'] is int
              ? json['milk_receiving_id']
              : int.tryParse(json['milk_receiving_id'].toString()))
          : null,
      createdBy: json['created_by'] != null
          ? (json['created_by'] is int
              ? json['created_by']
              : int.tryParse(json['created_by'].toString()))
          : null,
      sourceOrReason: json['source_or_reason'] ?? '',
      notes: json['notes'],
      milkReceiving: json['milk_receiving'] != null &&
              json['milk_receiving'] is Map<String, dynamic>
          ? MilkReceivingModel.fromJson(json['milk_receiving'])
          : null,
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'transaction_date': transactionDate,
      'type': type,
      'item_type': itemType,
      'quantity': quantity,
      'fat': fat,
      'snf': snf,
      'milk_receiving_id': milkReceivingId,
      'created_by': createdBy,
      'source_or_reason': sourceOrReason,
      'notes': notes,
      if (milkReceiving != null) 'milk_receiving': milkReceiving!.toJson(),
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }

  bool get isStockIn => type.toLowerCase() == 'in';
}

class MilkStockSummaryModel {
  final double openingStock;
  final double todayReceived;
  final double todayStockOut;
  final double availableStock;

  MilkStockSummaryModel({
    required this.openingStock,
    required this.todayReceived,
    required this.todayStockOut,
    required this.availableStock,
  });

  factory MilkStockSummaryModel.fromJson(Map<String, dynamic> json) {
    return MilkStockSummaryModel(
      openingStock: json['opening_stock'] != null
          ? (json['opening_stock'] as num).toDouble()
          : 0.0,
      todayReceived: json['today_received'] != null
          ? (json['today_received'] as num).toDouble()
          : 0.0,
      todayStockOut: json['today_stock_out'] != null
          ? (json['today_stock_out'] as num).toDouble()
          : 0.0,
      availableStock: json['available_stock'] != null
          ? (json['available_stock'] as num).toDouble()
          : 0.0,
    );
  }
}
