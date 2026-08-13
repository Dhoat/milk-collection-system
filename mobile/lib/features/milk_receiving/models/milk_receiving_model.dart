import '../../villages/models/village_model.dart';

class MilkReceivingModel {
  final int id;
  final int villageId;
  final String receivingDate;
  final String shift; // 'morning' or 'evening'
  final double expectedQuantity;
  final double receivedQuantity;
  final double quantityVariance;
  final double quantityVariancePercent;
  final double? expectedFat;
  final double? receivedFat;
  final double? expectedSnf;
  final double? receivedSnf;
  final String status;
  final int? verifiedBy;
  final String? notes;
  final VillageModel? village;
  final String? createdAt;
  final String? updatedAt;

  MilkReceivingModel({
    required this.id,
    required this.villageId,
    required this.receivingDate,
    required this.shift,
    required this.expectedQuantity,
    required this.receivedQuantity,
    required this.quantityVariance,
    required this.quantityVariancePercent,
    this.expectedFat,
    this.receivedFat,
    this.expectedSnf,
    this.receivedSnf,
    required this.status,
    this.verifiedBy,
    this.notes,
    this.village,
    this.createdAt,
    this.updatedAt,
  });

  factory MilkReceivingModel.fromJson(Map<String, dynamic> json) {
    return MilkReceivingModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      villageId: json['village_id'] is int
          ? json['village_id']
          : int.parse(json['village_id'].toString()),
      receivingDate: json['receiving_date'] ?? '',
      shift: json['shift'] ?? 'morning',
      expectedQuantity: json['expected_quantity'] != null
          ? (json['expected_quantity'] as num).toDouble()
          : 0.0,
      receivedQuantity: json['received_quantity'] != null
          ? (json['received_quantity'] as num).toDouble()
          : 0.0,
      quantityVariance: json['quantity_variance'] != null
          ? (json['quantity_variance'] as num).toDouble()
          : 0.0,
      quantityVariancePercent: json['quantity_variance_percent'] != null
          ? (json['quantity_variance_percent'] as num).toDouble()
          : 0.0,
      expectedFat: json['expected_fat'] != null
          ? (json['expected_fat'] as num).toDouble()
          : null,
      receivedFat: json['received_fat'] != null
          ? (json['received_fat'] as num).toDouble()
          : null,
      expectedSnf: json['expected_snf'] != null
          ? (json['expected_snf'] as num).toDouble()
          : null,
      receivedSnf: json['received_snf'] != null
          ? (json['received_snf'] as num).toDouble()
          : null,
      status: json['status'] ?? 'received',
      verifiedBy: json['verified_by'] != null
          ? (json['verified_by'] is int
              ? json['verified_by']
              : int.tryParse(json['verified_by'].toString()))
          : null,
      notes: json['notes'],
      village: json['village'] != null && json['village'] is Map<String, dynamic>
          ? VillageModel.fromJson(json['village'])
          : null,
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'village_id': villageId,
      'receiving_date': receivingDate,
      'shift': shift,
      'expected_quantity': expectedQuantity,
      'received_quantity': receivedQuantity,
      'quantity_variance': quantityVariance,
      'quantity_variance_percent': quantityVariancePercent,
      'expected_fat': expectedFat,
      'received_fat': receivedFat,
      'expected_snf': expectedSnf,
      'received_snf': receivedSnf,
      'status': status,
      'verified_by': verifiedBy,
      'notes': notes,
      if (village != null) 'village': village!.toJson(),
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

  bool get hasDiscrepancy => status.toLowerCase() == 'discrepancy' || quantityVariance.abs() > 0.1;
}
