class UserModel {
  final int id;
  final String name;
  final String email;
  final String role;
  final bool status;
  final DateTime? emailVerifiedAt;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    required this.status,
    this.emailVerifiedAt,
    this.createdAt,
    this.updatedAt,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] as int,
      name: json['name'] as String,
      email: json['email'] as String,
      role: json['role'] as String,
      status: json['status'] is bool
          ? json['status'] as bool
          : json['status'] == 1 || json['status'] == '1',
      emailVerifiedAt: json['email_verified_at'] != null
          ? DateTime.tryParse(json['email_verified_at'].toString())
          : null,
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'].toString())
          : null,
      updatedAt: json['updated_at'] != null
          ? DateTime.tryParse(json['updated_at'].toString())
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'status': status,
      'email_verified_at': emailVerifiedAt?.toIso8601String(),
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  // Role Checks
  bool get isSuperAdmin => role == 'super_admin';
  bool get isManager => role == 'manager';
  bool get isCollectionStaff => role == 'collection_staff';
  bool get isCenterStaff => role == 'center_staff';
  bool get isActive => status == true;

  String get roleDisplayName {
    switch (role) {
      case 'super_admin':
        return 'Super Admin';
      case 'manager':
        return 'Manager';
      case 'collection_staff':
        return 'Collection Staff';
      case 'center_staff':
        return 'Center Staff';
      default:
        return role;
    }
  }

  UserModel copyWith({
    int? id,
    String? name,
    String? email,
    String? role,
    bool? status,
    DateTime? emailVerifiedAt,
    DateTime? createdAt,
    DateTime? updatedAt,
  }) {
    return UserModel(
      id: id ?? this.id,
      name: name ?? this.name,
      email: email ?? this.email,
      role: role ?? this.role,
      status: status ?? this.status,
      emailVerifiedAt: emailVerifiedAt ?? this.emailVerifiedAt,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
    );
  }
}
