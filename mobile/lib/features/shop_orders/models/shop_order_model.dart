import '../../shops/models/shop_model.dart';
import 'product_model.dart';

class ShopOrderItemModel {
  final int id;
  final int shopOrderId;
  final int productId;
  final String productName;
  final String unit;
  final double quantity;
  final double unitPrice;
  final double lineTotal;
  final ProductModel? product;

  ShopOrderItemModel({
    required this.id,
    required this.shopOrderId,
    required this.productId,
    required this.productName,
    required this.unit,
    required this.quantity,
    required this.unitPrice,
    required this.lineTotal,
    this.product,
  });

  factory ShopOrderItemModel.fromJson(Map<String, dynamic> json) {
    return ShopOrderItemModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      shopOrderId: json['shop_order_id'] is int
          ? json['shop_order_id']
          : int.parse(json['shop_order_id'].toString()),
      productId: json['product_id'] is int
          ? json['product_id']
          : int.parse(json['product_id'].toString()),
      productName: json['product_name'] ?? '',
      unit: json['unit'] ?? '',
      quantity: json['quantity'] != null
          ? (json['quantity'] as num).toDouble()
          : 0.0,
      unitPrice: json['unit_price'] != null
          ? (json['unit_price'] as num).toDouble()
          : 0.0,
      lineTotal: json['line_total'] != null
          ? (json['line_total'] as num).toDouble()
          : 0.0,
      product: json['product'] != null && json['product'] is Map<String, dynamic>
          ? ProductModel.fromJson(json['product'])
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'shop_order_id': shopOrderId,
      'product_id': productId,
      'product_name': productName,
      'unit': unit,
      'quantity': quantity,
      'unit_price': unitPrice,
      'line_total': lineTotal,
      if (product != null) 'product': product!.toJson(),
    };
  }
}

class ShopOrderModel {
  final int id;
  final String orderNumber;
  final int shopId;
  final String orderDate;
  final String status;
  final double subtotal;
  final double discount;
  final double totalAmount;
  final bool stockDeducted;
  final int? createdBy;
  final String? notes;
  final ShopModel? shop;
  final List<ShopOrderItemModel> items;
  final String? createdAt;
  final String? updatedAt;

  ShopOrderModel({
    required this.id,
    required this.orderNumber,
    required this.shopId,
    required this.orderDate,
    required this.status,
    required this.subtotal,
    required this.discount,
    required this.totalAmount,
    required this.stockDeducted,
    this.createdBy,
    this.notes,
    this.shop,
    required this.items,
    this.createdAt,
    this.updatedAt,
  });

  factory ShopOrderModel.fromJson(Map<String, dynamic> json) {
    return ShopOrderModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      orderNumber: json['order_number'] ?? '',
      shopId: json['shop_id'] is int
          ? json['shop_id']
          : int.parse(json['shop_id'].toString()),
      orderDate: json['order_date'] ?? '',
      status: json['status'] ?? 'pending',
      subtotal: json['subtotal'] != null
          ? (json['subtotal'] as num).toDouble()
          : 0.0,
      discount: json['discount'] != null
          ? (json['discount'] as num).toDouble()
          : 0.0,
      totalAmount: json['total_amount'] != null
          ? (json['total_amount'] as num).toDouble()
          : 0.0,
      stockDeducted: json['stock_deducted'] is bool
          ? json['stock_deducted']
          : (json['stock_deducted'] == 1 || json['stock_deducted'] == '1' || json['stock_deducted'] == true),
      createdBy: json['created_by'] != null
          ? (json['created_by'] is int
              ? json['created_by']
              : int.tryParse(json['created_by'].toString()))
          : null,
      notes: json['notes'],
      shop: json['shop'] != null && json['shop'] is Map<String, dynamic>
          ? ShopModel.fromJson(json['shop'])
          : null,
      items: json['items'] != null && json['items'] is List
          ? (json['items'] as List)
              .map((i) => ShopOrderItemModel.fromJson(i))
              .toList()
          : [],
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'order_number': orderNumber,
      'shop_id': shopId,
      'order_date': orderDate,
      'status': status,
      'subtotal': subtotal,
      'discount': discount,
      'total_amount': totalAmount,
      'stock_deducted': stockDeducted,
      'created_by': createdBy,
      'notes': notes,
      if (shop != null) 'shop': shop!.toJson(),
      'items': items.map((i) => i.toJson()).toList(),
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }

  double get totalQuantity => items.fold(0.0, (sum, i) => sum + i.quantity);

  String get statusDisplayName {
    switch (status.toLowerCase()) {
      case 'pending':
        return 'Pending';
      case 'confirmed':
        return 'Confirmed';
      case 'preparing':
        return 'Preparing';
      case 'dispatched':
        return 'Dispatched';
      case 'delivered':
        return 'Delivered';
      case 'cancelled':
        return 'Cancelled';
      default:
        return status;
    }
  }
}
