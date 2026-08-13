import '../../villages/models/village_model.dart';

class FarmerModel {
  final int id;
  final int villageId;
  final String farmerCode;
  final String name;
  final String? fatherName;
  final String mobile;
  final String? alternateMobile;
  final String? address;
  final String? gender;
  final String? joiningDate;
  final String? bankName;
  final String? accountNumber;
  final String? ifscCode;
  final bool status;
  final VillageModel? village;
  final String? createdAt;
  final String? updatedAt;

  FarmerModel({
    required this.id,
    required this.villageId,
    required this.farmerCode,
    required this.name,
    this.fatherName,
    required this.mobile,
    this.alternateMobile,
    this.address,
    this.gender,
    this.joiningDate,
    this.bankName,
    this.accountNumber,
    this.ifscCode,
    required this.status,
    this.village,
    this.createdAt,
    this.updatedAt,
  });

  factory FarmerModel.fromJson(Map<String, dynamic> json) {
    return FarmerModel(
      id: (json['id'] as num).toInt(),
      villageId: (json['village_id'] as num).toInt(),
      farmerCode: json['farmer_code']?.toString() ?? '',
      name: json['name']?.toString() ?? '',
      fatherName: json['father_name']?.toString(),
      mobile: json['mobile']?.toString() ?? '',
      alternateMobile: json['alternate_mobile']?.toString(),
      address: json['address']?.toString(),
      gender: json['gender']?.toString(),
      joiningDate: json['joining_date']?.toString(),
      bankName: json['bank_name']?.toString(),
      accountNumber: json['account_number']?.toString(),
      ifscCode: json['ifsc_code']?.toString(),
      status: json['status'] is bool
          ? json['status'] as bool
          : (json['status'] as num?) == 1,
      village: json['village'] != null && json['village'] is Map<String, dynamic>
          ? VillageModel.fromJson(json['village'] as Map<String, dynamic>)
          : null,
      createdAt: json['created_at']?.toString(),
      updatedAt: json['updated_at']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'village_id': villageId,
      'farmer_code': farmerCode,
      'name': name,
      'father_name': fatherName,
      'mobile': mobile,
      'alternate_mobile': alternateMobile,
      'address': address,
      'gender': gender,
      'joining_date': joiningDate,
      'bank_name': bankName,
      'account_number': accountNumber,
      'ifsc_code': ifscCode,
      'status': status,
      if (village != null) 'village': village!.toJson(),
      if (createdAt != null) 'created_at': createdAt,
      if (updatedAt != null) 'updated_at': updatedAt,
    };
  }
}
