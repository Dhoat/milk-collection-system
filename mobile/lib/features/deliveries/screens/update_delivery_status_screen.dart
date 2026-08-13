import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/custom_button.dart';
import '../../../core/widgets/custom_text_field.dart';
import '../../../core/widgets/error_banner.dart';
import '../models/delivery_model.dart';
import '../providers/delivery_provider.dart';

class UpdateDeliveryStatusScreen extends StatefulWidget {
  final DeliveryModel delivery;

  const UpdateDeliveryStatusScreen({super.key, required this.delivery});

  @override
  State<UpdateDeliveryStatusScreen> createState() => _UpdateDeliveryStatusScreenState();
}

class _UpdateDeliveryStatusScreenState extends State<UpdateDeliveryStatusScreen> {
  final _formKey = GlobalKey<FormState>();

  late String _selectedStatus;
  late TextEditingController _assignedToController;

  @override
  void initState() {
    super.initState();
    _selectedStatus = widget.delivery.status;
    _assignedToController = TextEditingController(
      text: widget.delivery.assignedTo != null ? widget.delivery.assignedTo.toString() : '',
    );
  }

  @override
  void dispose() {
    _assignedToController.dispose();
    super.dispose();
  }

  void _handleUpdate() async {
    if (!_formKey.currentState!.validate()) return;

    final deliveryProvider = Provider.of<DeliveryProvider>(context, listen: false);

    final assignedTo = _assignedToController.text.trim().isNotEmpty
        ? int.tryParse(_assignedToController.text.trim())
        : null;

    final success = await deliveryProvider.updateDeliveryStatus(
      widget.delivery.id,
      _selectedStatus,
      assignedTo: assignedTo,
    );

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Delivery status updated successfully.'),
          backgroundColor: AppTheme.primaryColor,
        ),
      );
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final deliveryProvider = Provider.of<DeliveryProvider>(context);
    final allowedStatuses = widget.delivery.allowedNextStatuses;

    return Scaffold(
      appBar: AppBar(
        title: Text('Update ${widget.delivery.deliveryNumber}'),
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

                // Current Delivery Overview Card
                Card(
                  color: AppTheme.backgroundColor,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  child: Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          widget.delivery.deliveryNumber,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: AppTheme.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Current Status: ${widget.delivery.statusDisplayName}',
                          style: const TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: AppTheme.primaryColor,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Destination: ${widget.delivery.shop?.name ?? "Shop #${widget.delivery.shopId}"}',
                          style: const TextStyle(fontSize: 13, color: AppTheme.textSecondary),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 20),

                // Status Transition Dropdown
                if (widget.delivery.isTerminalState) ...[
                  const ContainerCard(
                    message: 'This delivery is in a terminal state (Delivered/Cancelled) and cannot be updated further.',
                  ),
                ] else ...[
                  DropdownButtonFormField<String>(
                    initialValue: _selectedStatus,
                    decoration: InputDecoration(
                      labelText: 'New Delivery Status *',
                      prefixIcon: const Icon(Icons.sync_alt, size: 20),
                      errorText: deliveryProvider.validationErrors?['status']?.first,
                    ),
                    items: [
                      DropdownMenuItem(
                        value: widget.delivery.status,
                        child: Text('${widget.delivery.statusDisplayName} (Current)'),
                      ),
                      ...allowedStatuses.map((st) => DropdownMenuItem(
                            value: st,
                            child: Text(_getStatusDisplayName(st)),
                          )),
                    ],
                    onChanged: (val) {
                      if (val != null) {
                        setState(() => _selectedStatus = val);
                      }
                    },
                  ),
                  const SizedBox(height: 20),

                  // Reassign Driver / Staff ID
                  CustomTextField(
                    controller: _assignedToController,
                    label: 'Driver / Staff User ID',
                    hint: 'e.g. 2 (Driver User ID)',
                    keyboardType: TextInputType.number,
                    prefixIcon: const Icon(Icons.person_pin_outlined, size: 20),
                    errorText: deliveryProvider.validationErrors?['assigned_to']?.first,
                  ),
                  const SizedBox(height: 28),

                  CustomButton(
                    text: 'Save Status Update',
                    icon: Icons.check_circle_outline,
                    isLoading: deliveryProvider.isSaving,
                    onPressed: _handleUpdate,
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }

  String _getStatusDisplayName(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return 'Pending Dispatch';
      case 'assigned':
        return 'Driver Assigned';
      case 'out_for_delivery':
        return 'Out for Delivery (In Transit)';
      case 'delivered':
        return 'Delivered to Shop';
      case 'failed':
        return 'Delivery Failed';
      case 'cancelled':
        return 'Cancelled';
      default:
        return status;
    }
  }
}

class ContainerCard extends StatelessWidget {
  final String message;

  const ContainerCard({super.key, required this.message});

  @override
  Widget build(BuildContext context) {
    return Card(
      color: const Color(0xFFF3F4F6),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Text(
          message,
          style: const TextStyle(color: AppTheme.textSecondary, fontWeight: FontWeight.w500),
        ),
      ),
    );
  }
}
