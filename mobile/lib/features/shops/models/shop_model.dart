import '../../villages/models/village_model.dart';

class ShopModel {
  final int id;
  final String shopCode;
  final String name;
  final String ownerName;
  final String phone;
  final String? email;
  final int? villageId;
  final String? area;
  final String? address;
  final bool status;
  final double creditLimit;
  final String? notes;
  final VillageModel? village;
  final String? createdAt;
  final String? updatedAt;

  ShopModel({
    required this.id,
    required this.shopCode,
    required this.name,
    required this.ownerName,
    required this.phone,
    this.email,
    this.villageId,
    this.area,
    this.address,
    required this.status,
    required this.creditLimit,
    this.notes,
    this.village,
    this.createdAt,
    this.updatedAt,
  });

  factory ShopModel.fromJson(Map<String, dynamic> json) {
    return ShopModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      shopCode: json['shop_code'] ?? '',
      name: json['name'] ?? '',
      ownerName: json['owner_name'] ?? '',
      phone: json['phone'] ?? '',
      email: json['email'],
      villageId: json['village_id'] != null
          ? (json['village_id'] is int
              ? json['village_id']
              : int.tryParse(json['village_id'].toString()))
          : null,
      area: json['area'],
      address: json['address'],
      status: json['status'] is bool
          ? json['status']
          : (json['status'] == 1 || json['status'] == '1' || json['status'] == true),
      creditLimit: json['credit_limit'] != null
          ? (json['credit_limit'] as num).toDouble()
          : 0.0,
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
      'shop_code': shopCode,
      'name': name,
      'owner_name': ownerName,
      'phone': phone,
      'email': email,
      'village_id': villageId,
      'area': area,
      'address': address,
      'status': status,
      'credit_limit': creditLimit,
      'notes': notes,
      if (village != null) 'village': village!.toJson(),
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }

  String get statusDisplayName => status ? 'Active' : 'Inactive';
}
