class ProductModel {
  final int id;
  final String productCode;
  final String name;
  final String category;
  final String unit;
  final double unitPrice;
  final double stockQuantity;
  final double availableStock;
  final bool status;
  final String? notes;

  ProductModel({
    required this.id,
    required this.productCode,
    required this.name,
    required this.category,
    required this.unit,
    required this.unitPrice,
    required this.stockQuantity,
    required this.availableStock,
    required this.status,
    this.notes,
  });

  factory ProductModel.fromJson(Map<String, dynamic> json) {
    return ProductModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      productCode: json['product_code'] ?? '',
      name: json['name'] ?? '',
      category: json['category'] ?? '',
      unit: json['unit'] ?? 'Litre',
      unitPrice: json['unit_price'] != null
          ? (json['unit_price'] as num).toDouble()
          : 0.0,
      stockQuantity: json['stock_quantity'] != null
          ? (json['stock_quantity'] as num).toDouble()
          : 0.0,
      availableStock: json['available_stock'] != null
          ? (json['available_stock'] as num).toDouble()
          : 0.0,
      status: json['status'] is bool
          ? json['status']
          : (json['status'] == 1 || json['status'] == '1' || json['status'] == true),
      notes: json['notes'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'product_code': productCode,
      'name': name,
      'category': category,
      'unit': unit,
      'unit_price': unitPrice,
      'stock_quantity': stockQuantity,
      'available_stock': availableStock,
      'status': status,
      'notes': notes,
    };
  }

  bool get isRawMilk => category.toLowerCase() == 'raw_milk';
}
