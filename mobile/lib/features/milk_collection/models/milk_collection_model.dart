import '../../farmers/models/farmer_model.dart';

class MilkCollectionModel {
  final int id;
  final int farmerId;
  final String collectionDate;
  final String shift; // 'morning' or 'evening'
  final double milkQuantity;
  final double? fat;
  final double? snf;
  final double rate;
  final double amount;
  final String? notes;
  final FarmerModel? farmer;
  final String? createdAt;
  final String? updatedAt;

  MilkCollectionModel({
    required this.id,
    required this.farmerId,
    required this.collectionDate,
    required this.shift,
    required this.milkQuantity,
    this.fat,
    this.snf,
    required this.rate,
    required this.amount,
    this.notes,
    this.farmer,
    this.createdAt,
    this.updatedAt,
  });

  factory MilkCollectionModel.fromJson(Map<String, dynamic> json) {
    return MilkCollectionModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      farmerId: json['farmer_id'] is int
          ? json['farmer_id']
          : int.parse(json['farmer_id'].toString()),
      collectionDate: json['collection_date'] ?? '',
      shift: json['shift'] ?? 'morning',
      milkQuantity: json['milk_quantity'] != null
          ? (json['milk_quantity'] as num).toDouble()
          : 0.0,
      fat: json['fat'] != null ? (json['fat'] as num).toDouble() : null,
      snf: json['snf'] != null ? (json['snf'] as num).toDouble() : null,
      rate: json['rate'] != null ? (json['rate'] as num).toDouble() : 0.0,
      amount: json['amount'] != null ? (json['amount'] as num).toDouble() : 0.0,
      notes: json['notes'],
      farmer: json['farmer'] != null && json['farmer'] is Map<String, dynamic>
          ? FarmerModel.fromJson(json['farmer'])
          : null,
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'farmer_id': farmerId,
      'collection_date': collectionDate,
      'shift': shift,
      'milk_quantity': milkQuantity,
      'fat': fat,
      'snf': snf,
      'rate': rate,
      'amount': amount,
      'notes': notes,
      if (farmer != null) 'farmer': farmer!.toJson(),
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }

  String get shiftDisplayName {
    switch (shift.toLowerCase()) {
      case 'morning':
        return 'Morning Shift';
      case 'evening':
        return 'Evening Shift';
      default:
        return shift;
    }
  }
}
