import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_button.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/widgets/error_banner.dart';
import '../../shop_orders/models/shop_order_model.dart';
import '../../shop_orders/providers/shop_order_provider.dart';
import '../providers/delivery_provider.dart';

class CreateDeliveryScreen extends StatefulWidget {
  final ShopOrderModel? initialShopOrder;

  const CreateDeliveryScreen({super.key, this.initialShopOrder});

  @override
  State<CreateDeliveryScreen> createState() => _CreateDeliveryScreenState();
}

class _CreateDeliveryScreenState extends State<CreateDeliveryScreen> {
  final _formKey = GlobalKey<FormState>();

  int? _selectedShopOrderId;
  DateTime _deliveryDate = DateTime.now();
  String _status = 'pending';
  final TextEditingController _assignedToController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();
  final TextEditingController _contactPersonController = TextEditingController();
  final TextEditingController _contactPhoneController = TextEditingController();
  final TextEditingController _notesController = TextEditingController();

  @override
  void initState() {
    super.initState();
    if (widget.initialShopOrder != null) {
      _applyOrderDetails(widget.initialShopOrder!);
    }

    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final orderProvider = Provider.of<ShopOrderProvider>(context, listen: false);
      if (orderProvider.allOrders.isEmpty) {
        await orderProvider.fetchShopOrders();
      }
    });
  }

  void _applyOrderDetails(ShopOrderModel order) {
    _selectedShopOrderId = order.id;
    if (order.shop != null) {
      _addressController.text = order.shop!.address ?? '';
      _contactPersonController.text = order.shop!.ownerName;
      _contactPhoneController.text = order.shop!.phone;
    }
  }

  @override
  void dispose() {
    _assignedToController.dispose();
    _addressController.dispose();
    _contactPersonController.dispose();
    _contactPhoneController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  void _selectDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _deliveryDate,
      firstDate: DateTime(2025),
      lastDate: DateTime(2030),
    );
    if (picked != null) {
      setState(() => _deliveryDate = picked);
    }
  }

  void _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedShopOrderId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select a shop order for dispatch.')),
      );
      return;
    }

    final deliveryProvider = Provider.of<DeliveryProvider>(context, listen: false);

    final formattedDate =
        '${_deliveryDate.year}-${_deliveryDate.month.toString().padLeft(2, '0')}-${_deliveryDate.day.toString().padLeft(2, '0')}';

    final payload = <String, dynamic>{
      'shop_order_id': _selectedShopOrderId,
      'delivery_date': formattedDate,
      'status': _status,
      'assigned_to': _assignedToController.text.trim().isNotEmpty
          ? int.tryParse(_assignedToController.text.trim())
          : null,
      'delivery_address': _addressController.text.trim().isNotEmpty
          ? _addressController.text.trim()
          : null,
      'contact_person': _contactPersonController.text.trim().isNotEmpty
          ? _contactPersonController.text.trim()
          : null,
      'contact_phone': _contactPhoneController.text.trim().isNotEmpty
          ? _contactPhoneController.text.trim()
          : null,
      'notes': _notesController.text.trim().isNotEmpty ? _notesController.text.trim() : null,
    };

    final success = await deliveryProvider.createDelivery(payload);

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Delivery dispatch created successfully!'),
          backgroundColor: AppTheme.primaryColor,
        ),
      );
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final orderProvider = Provider.of<ShopOrderProvider>(context);
    final deliveryProvider = Provider.of<DeliveryProvider>(context);
    final orders = orderProvider.allOrders;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dispatch Order Delivery'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (deliveryProvider.errorMessage != null) ...[
                  ErrorBanner(
                    message: deliveryProvider.errorMessage!,
                    onDismiss: () => deliveryProvider.clearMessages(),
                  ),
                  const SizedBox(height: 16),
                ],

                // Shop Order Dropdown
                DropdownButtonFormField<int>(
                  initialValue: _selectedShopOrderId,
                  decoration: InputDecoration(
                    labelText: 'Select Shop Order *',
                    prefixIcon: const Icon(Icons.shopping_bag_outlined, size: 20),
                    errorText: deliveryProvider.validationErrors?['shop_order_id']?.first,
                  ),
                  items: orders
                      .map((o) => DropdownMenuItem<int>(
                            value: o.id,
                            child: Text(
                              '${o.orderNumber} - ${o.shop?.name ?? "Shop #${o.shopId}"} (₹${o.totalAmount.toStringAsFixed(2)})',
                              overflow: TextOverflow.ellipsis,
                            ),
                          ))
                      .toList(),
                  onChanged: (val) {
                    setState(() {
                      _selectedShopOrderId = val;
                      final selectedOrder = orders.firstWhere((o) => o.id == val);
                      _applyOrderDetails(selectedOrder);
                    });
                  },
                  validator: (val) => val == null ? 'Please select an order' : null,
                ),
                const SizedBox(height: 16),

                // Delivery Date & Initial Status Row
                Row(
                  children: [
                    Expanded(
                      child: InkWell(
                        onTap: _selectDate,
                        borderRadius: BorderRadius.circular(12),
                        child: InputDecorator(
                          decoration: const InputDecoration(
                            labelText: 'Delivery Date *',
                            prefixIcon: Icon(Icons.calendar_today, size: 18),
                          ),
                          child: Text(
                            '${_deliveryDate.year}-${_deliveryDate.month.toString().padLeft(2, '0')}-${_deliveryDate.day.toString().padLeft(2, '0')}',
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
                          DropdownMenuItem(value: 'assigned', child: Text('Assigned')),
                          DropdownMenuItem(value: 'out_for_delivery', child: Text('Out for Delivery')),
                        ],
                        onChanged: (val) => setState(() => _status = val ?? 'pending'),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Assigned Staff User ID
                CustomTextField(
                  controller: _assignedToController,
                  label: 'Assigned Driver / Staff User ID',
                  hint: 'e.g. 2 (Leave blank if unassigned)',
                  keyboardType: TextInputType.number,
                  prefixIcon: const Icon(Icons.person_pin_outlined, size: 20),
                  errorText: deliveryProvider.validationErrors?['assigned_to']?.first,
                ),
                const SizedBox(height: 16),

                // Address & Contact Information Section
                const Text(
                  'Delivery Destination & Contact',
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.bold,
                    color: AppTheme.textPrimary,
                  ),
                ),
                const SizedBox(height: 10),

                CustomTextField(
                  controller: _addressController,
                  label: 'Delivery Address',
                  hint: 'Default shop address will be used if blank',
                  prefixIcon: const Icon(Icons.location_on_outlined, size: 20),
                  errorText: deliveryProvider.validationErrors?['delivery_address']?.first,
                ),
                const SizedBox(height: 16),

                Row(
                  children: [
                    Expanded(
                      child: CustomTextField(
                        controller: _contactPersonController,
                        label: 'Contact Person',
                        hint: 'Shop manager name',
                        prefixIcon: const Icon(Icons.person_outline, size: 20),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: CustomTextField(
                        controller: _contactPhoneController,
                        label: 'Contact Phone',
                        hint: 'Mobile number',
                        keyboardType: TextInputType.phone,
                        prefixIcon: const Icon(Icons.phone_outlined, size: 20),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                CustomTextField(
                  controller: _notesController,
                  label: 'Dispatch Notes / Driver Instructions',
                  hint: 'e.g. Deliver before 10 AM, check refrigeration',
                  maxLines: 2,
                  prefixIcon: const Icon(Icons.note_alt_outlined, size: 20),
                ),
                const SizedBox(height: 28),

                CustomButton(
                  text: 'Create Dispatch Record',
                  icon: Icons.local_shipping_outlined,
                  isLoading: deliveryProvider.isSaving,
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
