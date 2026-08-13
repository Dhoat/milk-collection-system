import '../../auth/models/user_model.dart';
import '../../shop_orders/models/shop_order_model.dart';
import '../../shops/models/shop_model.dart';

class DeliveryModel {
  final int id;
  final String deliveryNumber;
  final int shopOrderId;
  final int shopId;
  final String deliveryDate;
  final String status;
  final String? deliveryAddress;
  final String? contactPerson;
  final String? contactPhone;
  final int? assignedTo;
  final int? createdBy;
  final String? notes;
  final String? dispatchedAt;
  final String? deliveredAt;
  final ShopOrderModel? shopOrder;
  final ShopModel? shop;
  final UserModel? assignedStaff;
  final UserModel? creator;
  final String? createdAt;
  final String? updatedAt;

  DeliveryModel({
    required this.id,
    required this.deliveryNumber,
    required this.shopOrderId,
    required this.shopId,
    required this.deliveryDate,
    required this.status,
    this.deliveryAddress,
    this.contactPerson,
    this.contactPhone,
    this.assignedTo,
    this.createdBy,
    this.notes,
    this.dispatchedAt,
    this.deliveredAt,
    this.shopOrder,
    this.shop,
    this.assignedStaff,
    this.creator,
    this.createdAt,
    this.updatedAt,
  });

  factory DeliveryModel.fromJson(Map<String, dynamic> json) {
    return DeliveryModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      deliveryNumber: json['delivery_number'] ?? '',
      shopOrderId: json['shop_order_id'] is int
          ? json['shop_order_id']
          : int.parse(json['shop_order_id'].toString()),
      shopId: json['shop_id'] is int
          ? json['shop_id']
          : int.parse(json['shop_id'].toString()),
      deliveryDate: json['delivery_date'] ?? '',
      status: json['status'] ?? 'pending',
      deliveryAddress: json['delivery_address'],
      contactPerson: json['contact_person'],
      contactPhone: json['contact_phone'],
      assignedTo: json['assigned_to'] != null
          ? (json['assigned_to'] is int
              ? json['assigned_to']
              : int.tryParse(json['assigned_to'].toString()))
          : null,
      createdBy: json['created_by'] != null
          ? (json['created_by'] is int
              ? json['created_by']
              : int.tryParse(json['created_by'].toString()))
          : null,
      notes: json['notes'],
      dispatchedAt: json['dispatched_at'],
      deliveredAt: json['delivered_at'],
      shopOrder: json['shop_order'] != null && json['shop_order'] is Map<String, dynamic>
          ? ShopOrderModel.fromJson(json['shop_order'])
          : null,
      shop: json['shop'] != null && json['shop'] is Map<String, dynamic>
          ? ShopModel.fromJson(json['shop'])
          : null,
      assignedStaff: json['assigned_staff'] != null && json['assigned_staff'] is Map<String, dynamic>
          ? UserModel.fromJson(json['assigned_staff'])
          : null,
      creator: json['creator'] != null && json['creator'] is Map<String, dynamic>
          ? UserModel.fromJson(json['creator'])
          : null,
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'delivery_number': deliveryNumber,
      'shop_order_id': shopOrderId,
      'shop_id': shopId,
      'delivery_date': deliveryDate,
      'status': status,
      'delivery_address': deliveryAddress,
      'contact_person': contactPerson,
      'contact_phone': contactPhone,
      'assigned_to': assignedTo,
      'created_by': createdBy,
      'notes': notes,
      'dispatched_at': dispatchedAt,
      'delivered_at': deliveredAt,
      if (shopOrder != null) 'shop_order': shopOrder!.toJson(),
      if (shop != null) 'shop': shop!.toJson(),
      if (assignedStaff != null) 'assigned_staff': assignedStaff!.toJson(),
      if (creator != null) 'creator': creator!.toJson(),
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }

  String get statusDisplayName {
    switch (status.toLowerCase()) {
      case 'pending':
        return 'Pending Dispatch';
      case 'assigned':
        return 'Driver Assigned';
      case 'out_for_delivery':
        return 'Out for Delivery';
      case 'delivered':
        return 'Delivered';
      case 'failed':
        return 'Delivery Failed';
      case 'cancelled':
        return 'Cancelled';
      default:
        return status;
    }
  }

  bool get isTerminalState => status == 'delivered' || status == 'cancelled';

  /// Allowed status transitions according to Laravel DeliveryService logic
  List<String> get allowedNextStatuses {
    switch (status.toLowerCase()) {
      case 'pending':
        return ['assigned', 'out_for_delivery', 'cancelled'];
      case 'assigned':
        return ['out_for_delivery', 'pending', 'cancelled'];
      case 'out_for_delivery':
        return ['delivered', 'failed', 'cancelled'];
      case 'failed':
        return ['out_for_delivery', 'cancelled'];
      case 'delivered':
      case 'cancelled':
      default:
        return [];
    }
  }
}
