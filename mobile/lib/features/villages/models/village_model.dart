class VillageModel {
  final int id;
  final String name;
  final String code;
  final String? address;
  final bool status;
  final int? farmersCount;
  final String? createdAt;
  final String? updatedAt;

  VillageModel({
    required this.id,
    required this.name,
    required this.code,
    this.address,
    required this.status,
    this.farmersCount,
    this.createdAt,
    this.updatedAt,
  });

  factory VillageModel.fromJson(Map<String, dynamic> json) {
    return VillageModel(
      id: (json['id'] as num).toInt(),
      name: json['name']?.toString() ?? '',
      code: json['code']?.toString() ?? '',
      address: json['address']?.toString(),
      status: json['status'] is bool
          ? json['status'] as bool
          : (json['status'] as num?) == 1,
      farmersCount: (json['farmers_count'] as num?)?.toInt(),
      createdAt: json['created_at']?.toString(),
      updatedAt: json['updated_at']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'code': code,
      'address': address,
      'status': status,
      if (farmersCount != null) 'farmers_count': farmersCount,
      if (createdAt != null) 'created_at': createdAt,
      if (updatedAt != null) 'updated_at': updatedAt,
    };
  }

  Map<String, dynamic> toFormJson() {
    return {
      'name': name,
      'code': code,
      'address': address,
      'status': status,
    };
  }
}
