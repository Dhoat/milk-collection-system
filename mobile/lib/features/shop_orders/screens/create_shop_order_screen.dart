import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_button.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/widgets/error_banner.dart';
import '../../shops/models/shop_model.dart';
import '../../shops/providers/shop_provider.dart';
import '../models/product_model.dart';
import '../providers/shop_order_provider.dart';

class OrderItemEntry {
  ProductModel? product;
  final TextEditingController quantityController = TextEditingController(text: '1');
  final TextEditingController priceController = TextEditingController();

  void dispose() {
    quantityController.dispose();
    priceController.dispose();
  }

  double get quantity => double.tryParse(quantityController.text) ?? 0.0;
  double get unitPrice => double.tryParse(priceController.text) ?? (product?.unitPrice ?? 0.0);
  double get lineTotal => quantity * unitPrice;
}

class CreateShopOrderScreen extends StatefulWidget {
  final ShopModel? initialShop;

  const CreateShopOrderScreen({super.key, this.initialShop});

  @override
  State<CreateShopOrderScreen> createState() => _CreateShopOrderScreenState();
}

class _CreateShopOrderScreenState extends State<CreateShopOrderScreen> {
  final _formKey = GlobalKey<FormState>();

  int? _selectedShopId;
  DateTime _orderDate = DateTime.now();
  String _status = 'pending';
  final TextEditingController _discountController = TextEditingController(text: '0');
  final TextEditingController _notesController = TextEditingController();

  final List<OrderItemEntry> _items = [];

  @override
  void initState() {
    super.initState();
    _selectedShopId = widget.initialShop?.id;

    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final shopProvider = Provider.of<ShopProvider>(context, listen: false);
      if (shopProvider.allShops.isEmpty) {
        await shopProvider.fetchShops();
      }

      if (!mounted) return;
      final orderProvider = Provider.of<ShopOrderProvider>(context, listen: false);
      await orderProvider.fetchProducts();

      if (_items.isEmpty && orderProvider.products.isNotEmpty) {
        _addItem(defaultProduct: orderProvider.products.first);
      }
    });
  }

  @override
  void dispose() {
    for (var item in _items) {
      item.dispose();
    }
    _discountController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  void _addItem({ProductModel? defaultProduct}) {
    setState(() {
      final entry = OrderItemEntry();
      if (defaultProduct != null) {
        entry.product = defaultProduct;
        entry.priceController.text = defaultProduct.unitPrice.toString();
      }
      _items.add(entry);
    });
  }

  void _removeItem(int index) {
    if (_items.length <= 1) return;
    setState(() {
      _items[index].dispose();
      _items.removeAt(index);
    });
  }

  double get subtotal => _items.fold(0.0, (sum, item) => sum + item.lineTotal);
  double get discount => double.tryParse(_discountController.text) ?? 0.0;
  double get totalAmount => (subtotal - discount).clamp(0.0, double.infinity);

  void _selectDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _orderDate,
      firstDate: DateTime(2025),
      lastDate: DateTime(2030),
    );
    if (picked != null) {
      setState(() => _orderDate = picked);
    }
  }

  void _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedShopId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select a shop.')),
      );
      return;
    }
    if (_items.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please add at least one product item.')),
      );
      return;
    }

    final orderProvider = Provider.of<ShopOrderProvider>(context, listen: false);

    final preparedItems = _items.map((item) {
      return {
        'product_id': item.product!.id,
        'quantity': item.quantity,
        'unit_price': item.unitPrice,
      };
    }).toList();

    final formattedDate =
        '${_orderDate.year}-${_orderDate.month.toString().padLeft(2, '0')}-${_orderDate.day.toString().padLeft(2, '0')}';

    final payload = <String, dynamic>{
      'shop_id': _selectedShopId,
      'order_date': formattedDate,
      'status': _status,
      'discount': discount,
      'notes': _notesController.text.trim().isNotEmpty ? _notesController.text.trim() : null,
      'items': preparedItems,
    };

    final success = await orderProvider.createShopOrder(payload);

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Shop order created successfully!'),
          backgroundColor: AppTheme.primaryColor,
        ),
      );
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final shopProvider = Provider.of<ShopProvider>(context);
    final orderProvider = Provider.of<ShopOrderProvider>(context);
    final shops = shopProvider.allShops;
    final products = orderProvider.products;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Create Shop Order'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (orderProvider.errorMessage != null) ...[
                  ErrorBanner(
                    message: orderProvider.errorMessage!,
                    onDismiss: () => orderProvider.clearMessages(),
                  ),
                  const SizedBox(height: 16),
                ],

                // Shop Dropdown
                DropdownButtonFormField<int>(
                  initialValue: _selectedShopId,
                  decoration: InputDecoration(
                    labelText: 'Select Shop *',
                    prefixIcon: const Icon(Icons.storefront, size: 20),
                    errorText: orderProvider.validationErrors?['shop_id']?.first,
                  ),
                  items: shops
                      .map((s) => DropdownMenuItem<int>(
                            value: s.id,
                            child: Text('${s.name} (${s.shopCode})'),
                          ))
                      .toList(),
                  onChanged: (val) => setState(() => _selectedShopId = val),
                  validator: (val) => val == null ? 'Please select a shop' : null,
                ),
                const SizedBox(height: 16),

                // Order Date & Initial Status Row
                Row(
                  children: [
                    Expanded(
                      child: InkWell(
                        onTap: _selectDate,
                        borderRadius: BorderRadius.circular(12),
                        child: InputDecorator(
                          decoration: const InputDecoration(
                            labelText: 'Order Date *',
                            prefixIcon: Icon(Icons.calendar_today, size: 18),
                          ),
                          child: Text(
                            '${_orderDate.year}-${_orderDate.month.toString().padLeft(2, '0')}-${_orderDate.day.toString().padLeft(2, '0')}',
                            style: const TextStyle(fontSize: 14, color: AppTheme.textPrimary),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: DropdownButtonFormField<String>(
                        initialValue: _status,
                        decoration: const InputDecoration(
                          labelText: 'Initial Status *',
                          prefixIcon: Icon(Icons.flag_outlined, size: 18),
                        ),
                        items: const [
                          DropdownMenuItem(value: 'pending', child: Text('Pending')),
                          DropdownMenuItem(value: 'confirmed', child: Text('Confirmed')),
                          DropdownMenuItem(value: 'preparing', child: Text('Preparing')),
                          DropdownMenuItem(value: 'dispatched', child: Text('Dispatched')),
                          DropdownMenuItem(value: 'delivered', child: Text('Delivered')),
                        ],
                        onChanged: (val) => setState(() => _status = val ?? 'pending'),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),

                // Order Items Section Header
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'Order Items',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: AppTheme.textPrimary,
                      ),
                    ),
                    TextButton.icon(
                      onPressed: () => _addItem(
                        defaultProduct: products.isNotEmpty ? products.first : null,
                      ),
                      icon: const Icon(Icons.add, size: 18),
                      label: const Text('Add Product'),
                    ),
                  ],
                ),
                const SizedBox(height: 8),

                // Dynamic Order Items List
                ListView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: _items.length,
                  itemBuilder: (context, index) {
                    final entry = _items[index];
                    final currentProduct = entry.product;
                    final availableStock = currentProduct?.availableStock ?? 0.0;
                    final hasInsufficientStock =
                        entry.quantity > availableStock && (_status != 'pending');

                    return Card(
                      margin: const EdgeInsets.only(bottom: 12),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                        side: BorderSide(
                          color: hasInsufficientStock ? Colors.red : const Color(0xFFE5E7EB),
                        ),
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(12.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Expanded(
                                  child: DropdownButtonFormField<ProductModel>(
                                    initialValue: currentProduct,
                                    isExpanded: true,
                                    decoration: const InputDecoration(
                                      labelText: 'Product *',
                                      contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                                    ),
                                    items: products
                                        .map((p) => DropdownMenuItem<ProductModel>(
                                              value: p,
                                              child: Text(
                                                '${p.name} (Stock: ${p.availableStock} ${p.unit})',
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ))
                                        .toList(),
                                    onChanged: (p) {
                                      setState(() {
                                        entry.product = p;
                                        if (p != null) {
                                          entry.priceController.text = p.unitPrice.toString();
                                        }
                                      });
                                    },
                                  ),
                                ),
                                if (_items.length > 1)
                                  IconButton(
                                    icon: const Icon(Icons.remove_circle_outline, color: Colors.red),
                                    onPressed: () => _removeItem(index),
                                  ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            Row(
                              children: [
                                Expanded(
                                  child: TextFormField(
                                    controller: entry.quantityController,
                                    keyboardType: const TextInputType.numberWithOptions(decimal: true),
                                    decoration: InputDecoration(
                                      labelText: 'Quantity (${currentProduct?.unit ?? "Unit"}) *',
                                      contentPadding:
                                          const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                                    ),
                                    onChanged: (_) => setState(() {}),
                                    validator: (val) {
                                      final num = double.tryParse(val ?? '');
                                      if (num == null || num <= 0) return 'Required';
                                      return null;
                                    },
                                  ),
                                ),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: TextFormField(
                                    controller: entry.priceController,
                                    keyboardType: const TextInputType.numberWithOptions(decimal: true),
                                    decoration: const InputDecoration(
                                      labelText: 'Unit Price (₹) *',
                                      contentPadding:
                                          EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                                    ),
                                    onChanged: (_) => setState(() {}),
                                    validator: (val) {
                                      final num = double.tryParse(val ?? '');
                                      if (num == null || num < 0) return 'Required';
                                      return null;
                                    },
                                  ),
                                ),
                                const SizedBox(width: 10),
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.end,
                                  children: [
                                    const Text('Line Total', style: TextStyle(fontSize: 10, color: AppTheme.textSecondary)),
                                    Text(
                                      '₹${entry.lineTotal.toStringAsFixed(2)}',
                                      style: const TextStyle(
                                        fontSize: 14,
                                        fontWeight: FontWeight.bold,
                                        color: AppTheme.primaryColor,
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                            if (hasInsufficientStock) ...[
                              const SizedBox(height: 6),
                              Row(
                                children: [
                                  const Icon(Icons.warning_amber_rounded, size: 14, color: Colors.red),
                                  const SizedBox(width: 4),
                                  Text(
                                    'Warning: Requested ${entry.quantity} exceeds available stock (${availableStock.toStringAsFixed(1)})',
                                    style: const TextStyle(fontSize: 11, color: Colors.red, fontWeight: FontWeight.bold),
                                  ),
                                ],
                              ),
                            ],
                          ],
                        ),
                      ),
                    );
                  },
                ),
                const SizedBox(height: 16),

                // Order Calculation Card
                Card(
                  color: AppTheme.backgroundColor,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text('Subtotal:', style: TextStyle(fontSize: 14, color: AppTheme.textSecondary)),
                            Text('₹${subtotal.toStringAsFixed(2)}', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600)),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text('Discount (₹):', style: TextStyle(fontSize: 14, color: AppTheme.textSecondary)),
                            SizedBox(
                              width: 100,
                              child: TextFormField(
                                controller: _discountController,
                                textAlign: TextAlign.right,
                                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                                decoration: const InputDecoration(
                                  isDense: true,
                                  contentPadding: EdgeInsets.symmetric(horizontal: 8, vertical: 6),
                                ),
                                onChanged: (_) => setState(() {}),
                              ),
                            ),
                          ],
                        ),
                        const Divider(height: 20),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text('Grand Total:', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textPrimary)),
                            Text(
                              '₹${totalAmount.toStringAsFixed(2)}',
                              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.primaryColor),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                // Notes Field
                CustomTextField(
                  controller: _notesController,
                  label: 'Notes / Special Instructions',
                  hint: 'e.g. Morning delivery required',
                  maxLines: 2,
                  prefixIcon: const Icon(Icons.note_alt_outlined, size: 20),
                ),
                const SizedBox(height: 28),

                CustomButton(
                  text: 'Submit Shop Order',
                  icon: Icons.check_circle_outline,
                  isLoading: orderProvider.isSaving,
                  onPressed: _handleSubmit,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
