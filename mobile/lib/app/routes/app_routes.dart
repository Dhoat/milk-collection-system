import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/permissions/role_permissions.dart';
import '../../core/widgets/access_restricted_screen.dart';
import '../../features/auth/models/user_model.dart';
import '../../features/auth/providers/auth_provider.dart';
import '../../features/auth/screens/login_screen.dart';
import '../../features/dashboard/screens/home_screen.dart';
import '../../features/deliveries/models/delivery_model.dart';
import '../../features/deliveries/screens/create_delivery_screen.dart';
import '../../features/deliveries/screens/delivery_detail_screen.dart';
import '../../features/deliveries/screens/delivery_list_screen.dart';
import '../../features/deliveries/screens/update_delivery_status_screen.dart';
import '../../features/farmers/models/farmer_model.dart';
import '../../features/farmers/screens/add_edit_farmer_screen.dart';
import '../../features/farmers/screens/farmer_detail_screen.dart';
import '../../features/farmers/screens/farmer_list_screen.dart';
import '../../features/milk_collection/models/milk_collection_model.dart';
import '../../features/milk_collection/screens/add_edit_milk_collection_screen.dart';
import '../../features/milk_collection/screens/milk_collection_detail_screen.dart';
import '../../features/milk_collection/screens/milk_collection_list_screen.dart';
import '../../features/milk_receiving/models/milk_receiving_model.dart';
import '../../features/milk_receiving/screens/add_edit_milk_receiving_screen.dart';
import '../../features/milk_receiving/screens/milk_receiving_detail_screen.dart';
import '../../features/milk_receiving/screens/milk_receiving_list_screen.dart';
import '../../features/milk_stock/screens/milk_stock_screen.dart';
import '../../features/milk_stock/screens/stock_out_screen.dart';
import '../../features/profile/screens/profile_screen.dart';
import '../../features/reports/screens/reports_screen.dart';
import '../../features/shop_orders/models/shop_order_model.dart';
import '../../features/users/screens/add_edit_user_screen.dart';
import '../../features/users/screens/user_detail_screen.dart';
import '../../features/users/screens/user_list_screen.dart';
import '../../features/shop_orders/screens/create_shop_order_screen.dart';
import '../../features/shop_orders/screens/shop_order_detail_screen.dart';
import '../../features/shop_orders/screens/shop_order_list_screen.dart';
import '../../features/shops/models/shop_model.dart';
import '../../features/shops/screens/add_edit_shop_screen.dart';
import '../../features/shops/screens/shop_detail_screen.dart';
import '../../features/shops/screens/shop_list_screen.dart';
import '../../features/villages/models/village_model.dart';
import '../../features/villages/screens/add_edit_village_screen.dart';
import '../../features/villages/screens/village_detail_screen.dart';
import '../../features/villages/screens/village_list_screen.dart';

class AppRoutes {
  static const String login = '/login';
  static const String home = '/home';
  static const String profile = '/profile';

  // Village Module Routes
  static const String villages = '/villages';
  static const String villageCreate = '/villages/create';
  static const String villageDetail = '/villages/detail';
  static const String villageEdit = '/villages/edit';

  // Farmer Module Routes
  static const String farmers = '/farmers';
  static const String farmerCreate = '/farmers/create';
  static const String farmerDetail = '/farmers/detail';
  static const String farmerEdit = '/farmers/edit';

  // Milk Collection Module Routes
  static const String milkCollections = '/milk-collections';
  static const String milkCollectionCreate = '/milk-collections/create';
  static const String milkCollectionDetail = '/milk-collections/detail';
  static const String milkCollectionEdit = '/milk-collections/edit';

  // Milk Receiving Module Routes
  static const String milkReceivings = '/milk-receivings';
  static const String milkReceivingCreate = '/milk-receivings/create';
  static const String milkReceivingDetail = '/milk-receivings/detail';
  static const String milkReceivingEdit = '/milk-receivings/edit';

  // Milk Stock / Inventory Module Routes
  static const String milkStocks = '/milk-stocks';
  static const String milkStockOut = '/milk-stocks/out';

  // Shop Management Module Routes
  static const String shops = '/shops';
  static const String shopCreate = '/shops/create';
  static const String shopDetail = '/shops/detail';
  static const String shopEdit = '/shops/edit';

  // Shop Orders Module Routes
  static const String shopOrders = '/shop-orders';
  static const String shopOrderCreate = '/shop-orders/create';
  static const String shopOrderDetail = '/shop-orders/detail';
  static const String shopOrderEdit = '/shop-orders/edit';

  // Delivery Module Routes
  static const String deliveries = '/deliveries';
  static const String createDelivery = '/deliveries/create';
  static const String deliveryDetail = '/deliveries/detail';
  static const String updateDeliveryStatus = '/deliveries/update-status';

  // Executive Reports Module Routes
  static const String reports = '/reports';

  // User Management Module Routes
  static const String users = '/users';
  static const String userCreate = '/users/create';
  static const String userDetail = '/users/detail';
  static const String userEdit = '/users/edit';

  static Route<dynamic> _protectedRoute(
    Widget Function(BuildContext context) builder,
    RouteSettings settings,
  ) {
    return MaterialPageRoute(
      builder: (context) {
        final authProvider = Provider.of<AuthProvider>(context, listen: false);
        final userRole = authProvider.user?.role;
        final routeName = settings.name ?? '';

        if (!RolePermissions.canAccessRoute(userRole, routeName)) {
          return AccessRestrictedScreen(
            routeName: routeName,
            userRole: userRole,
          );
        }
        return builder(context);
      },
      settings: settings,
    );
  }

  static Route<dynamic> onGenerateRoute(RouteSettings settings) {
    switch (settings.name) {
      case login:
        return MaterialPageRoute(
          builder: (_) => const LoginScreen(),
          settings: settings,
        );

      case home:
        return _protectedRoute((_) => const HomeScreen(), settings);

      case profile:
        return _protectedRoute((_) => const ProfileScreen(), settings);

      case villages:
        return _protectedRoute((_) => const VillageListScreen(), settings);

      case villageCreate:
        return _protectedRoute((_) => const AddEditVillageScreen(), settings);

      case villageDetail:
        final village = settings.arguments as VillageModel;
        return _protectedRoute((_) => VillageDetailScreen(village: village), settings);

      case villageEdit:
        final village = settings.arguments as VillageModel;
        return _protectedRoute((_) => AddEditVillageScreen(village: village), settings);

      case farmers:
        return _protectedRoute((_) => const FarmerListScreen(), settings);

      case farmerCreate:
        return _protectedRoute((_) => const AddEditFarmerScreen(), settings);

      case farmerDetail:
        final farmer = settings.arguments as FarmerModel;
        return _protectedRoute((_) => FarmerDetailScreen(farmer: farmer), settings);

      case farmerEdit:
        final farmer = settings.arguments as FarmerModel;
        return _protectedRoute((_) => AddEditFarmerScreen(farmer: farmer), settings);

      case milkCollections:
        return _protectedRoute((_) => const MilkCollectionListScreen(), settings);

      case milkCollectionCreate:
        final preSelectedFarmer = settings.arguments as FarmerModel?;
        return _protectedRoute((_) => AddEditMilkCollectionScreen(preSelectedFarmer: preSelectedFarmer), settings);

      case milkCollectionDetail:
        final collection = settings.arguments as MilkCollectionModel;
        return _protectedRoute((_) => MilkCollectionDetailScreen(collection: collection), settings);

      case milkCollectionEdit:
        final collection = settings.arguments as MilkCollectionModel;
        return _protectedRoute((_) => AddEditMilkCollectionScreen(collection: collection), settings);

      case milkReceivings:
        return _protectedRoute((_) => const MilkReceivingListScreen(), settings);

      case milkReceivingCreate:
        return _protectedRoute((_) => const AddEditMilkReceivingScreen(), settings);

      case milkReceivingDetail:
        final receiving = settings.arguments as MilkReceivingModel;
        return _protectedRoute((_) => MilkReceivingDetailScreen(receiving: receiving), settings);

      case milkReceivingEdit:
        final receiving = settings.arguments as MilkReceivingModel;
        return _protectedRoute((_) => AddEditMilkReceivingScreen(receiving: receiving), settings);

      case milkStocks:
        return _protectedRoute((_) => const MilkStockScreen(), settings);

      case milkStockOut:
        return _protectedRoute((_) => const StockOutScreen(), settings);

      case shops:
        return _protectedRoute((_) => const ShopListScreen(), settings);

      case shopCreate:
        return _protectedRoute((_) => const AddEditShopScreen(), settings);

      case shopDetail:
        final shop = settings.arguments as ShopModel;
        return _protectedRoute((_) => ShopDetailScreen(shop: shop), settings);

      case shopEdit:
        final shop = settings.arguments as ShopModel;
        return _protectedRoute((_) => AddEditShopScreen(shop: shop), settings);

      case shopOrders:
        return _protectedRoute((_) => const ShopOrderListScreen(), settings);

      case shopOrderCreate:
        final initialShop = settings.arguments as ShopModel?;
        return _protectedRoute((_) => CreateShopOrderScreen(initialShop: initialShop), settings);

      case shopOrderDetail:
        final order = settings.arguments as ShopOrderModel;
        return _protectedRoute((_) => ShopOrderDetailScreen(order: order), settings);

      case shopOrderEdit:
        final order = settings.arguments as ShopOrderModel;
        return _protectedRoute((_) => ShopOrderDetailScreen(order: order), settings);

      case deliveries:
        return _protectedRoute((_) => const DeliveryListScreen(), settings);

      case createDelivery:
        final initialShopOrder = settings.arguments as ShopOrderModel?;
        return _protectedRoute((_) => CreateDeliveryScreen(initialShopOrder: initialShopOrder), settings);

      case deliveryDetail:
        final delivery = settings.arguments as DeliveryModel;
        return _protectedRoute((_) => DeliveryDetailScreen(delivery: delivery), settings);

      case updateDeliveryStatus:
        final delivery = settings.arguments as DeliveryModel;
        return _protectedRoute((_) => UpdateDeliveryStatusScreen(delivery: delivery), settings);

      case reports:
        return _protectedRoute((_) => const ReportsScreen(), settings);

      case users:
        return _protectedRoute((_) => const UserListScreen(), settings);

      case userCreate:
        return _protectedRoute((_) => const AddEditUserScreen(), settings);

      case userDetail:
        final user = settings.arguments as UserModel;
        return _protectedRoute((_) => UserDetailScreen(user: user), settings);

      case userEdit:
        final user = settings.arguments as UserModel;
        return _protectedRoute((_) => AddEditUserScreen(user: user), settings);

      default:
        return MaterialPageRoute(
          builder: (_) => const LoginScreen(),
          settings: settings,
        );
    }
  }
}
