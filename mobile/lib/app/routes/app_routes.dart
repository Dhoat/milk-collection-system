import 'package:flutter/material.dart';

import '../../features/auth/models/user_model.dart';
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

  static Route<dynamic> onGenerateRoute(RouteSettings settings) {
    switch (settings.name) {
      case login:
        return MaterialPageRoute(
          builder: (_) => const LoginScreen(),
          settings: settings,
        );

      case home:
        return MaterialPageRoute(
          builder: (_) => const HomeScreen(),
          settings: settings,
        );

      case profile:
        return MaterialPageRoute(
          builder: (_) => const ProfileScreen(),
          settings: settings,
        );

      case villages:
        return MaterialPageRoute(
          builder: (_) => const VillageListScreen(),
          settings: settings,
        );

      case villageCreate:
        return MaterialPageRoute(
          builder: (_) => const AddEditVillageScreen(),
          settings: settings,
        );

      case villageDetail:
        final village = settings.arguments as VillageModel;
        return MaterialPageRoute(
          builder: (_) => VillageDetailScreen(village: village),
          settings: settings,
        );

      case villageEdit:
        final village = settings.arguments as VillageModel;
        return MaterialPageRoute(
          builder: (_) => AddEditVillageScreen(village: village),
          settings: settings,
        );

      case farmers:
        return MaterialPageRoute(
          builder: (_) => const FarmerListScreen(),
          settings: settings,
        );

      case farmerCreate:
        return MaterialPageRoute(
          builder: (_) => const AddEditFarmerScreen(),
          settings: settings,
        );

      case farmerDetail:
        final farmer = settings.arguments as FarmerModel;
        return MaterialPageRoute(
          builder: (_) => FarmerDetailScreen(farmer: farmer),
          settings: settings,
        );

      case farmerEdit:
        final farmer = settings.arguments as FarmerModel;
        return MaterialPageRoute(
          builder: (_) => AddEditFarmerScreen(farmer: farmer),
          settings: settings,
        );

      case milkCollections:
        return MaterialPageRoute(
          builder: (_) => const MilkCollectionListScreen(),
          settings: settings,
        );

      case milkCollectionCreate:
        final preSelectedFarmer = settings.arguments as FarmerModel?;
        return MaterialPageRoute(
          builder: (_) => AddEditMilkCollectionScreen(preSelectedFarmer: preSelectedFarmer),
          settings: settings,
        );

      case milkCollectionDetail:
        final collection = settings.arguments as MilkCollectionModel;
        return MaterialPageRoute(
          builder: (_) => MilkCollectionDetailScreen(collection: collection),
          settings: settings,
        );

      case milkCollectionEdit:
        final collection = settings.arguments as MilkCollectionModel;
        return MaterialPageRoute(
          builder: (_) => AddEditMilkCollectionScreen(collection: collection),
          settings: settings,
        );

      case milkReceivings:
        return MaterialPageRoute(
          builder: (_) => const MilkReceivingListScreen(),
          settings: settings,
        );

      case milkReceivingCreate:
        return MaterialPageRoute(
          builder: (_) => const AddEditMilkReceivingScreen(),
          settings: settings,
        );

      case milkReceivingDetail:
        final receiving = settings.arguments as MilkReceivingModel;
        return MaterialPageRoute(
          builder: (_) => MilkReceivingDetailScreen(receiving: receiving),
          settings: settings,
        );

      case milkReceivingEdit:
        final receiving = settings.arguments as MilkReceivingModel;
        return MaterialPageRoute(
          builder: (_) => AddEditMilkReceivingScreen(receiving: receiving),
          settings: settings,
        );

      case milkStocks:
        return MaterialPageRoute(
          builder: (_) => const MilkStockScreen(),
          settings: settings,
        );

      case milkStockOut:
        return MaterialPageRoute(
          builder: (_) => const StockOutScreen(),
          settings: settings,
        );

      case shops:
        return MaterialPageRoute(
          builder: (_) => const ShopListScreen(),
          settings: settings,
        );

      case shopCreate:
        return MaterialPageRoute(
          builder: (_) => const AddEditShopScreen(),
          settings: settings,
        );

      case shopDetail:
        final shop = settings.arguments as ShopModel;
        return MaterialPageRoute(
          builder: (_) => ShopDetailScreen(shop: shop),
          settings: settings,
        );

      case shopEdit:
        final shop = settings.arguments as ShopModel;
        return MaterialPageRoute(
          builder: (_) => AddEditShopScreen(shop: shop),
          settings: settings,
        );

      case shopOrders:
        return MaterialPageRoute(
          builder: (_) => const ShopOrderListScreen(),
          settings: settings,
        );

      case shopOrderCreate:
        final initialShop = settings.arguments as ShopModel?;
        return MaterialPageRoute(
          builder: (_) => CreateShopOrderScreen(initialShop: initialShop),
          settings: settings,
        );

      case shopOrderDetail:
        final order = settings.arguments as ShopOrderModel;
        return MaterialPageRoute(
          builder: (_) => ShopOrderDetailScreen(order: order),
          settings: settings,
        );

      case shopOrderEdit:
        final order = settings.arguments as ShopOrderModel;
        return MaterialPageRoute(
          builder: (_) => ShopOrderDetailScreen(order: order),
          settings: settings,
        );

      case deliveries:
        return MaterialPageRoute(
          builder: (_) => const DeliveryListScreen(),
          settings: settings,
        );

      case createDelivery:
        final initialShopOrder = settings.arguments as ShopOrderModel?;
        return MaterialPageRoute(
          builder: (_) => CreateDeliveryScreen(initialShopOrder: initialShopOrder),
          settings: settings,
        );

      case deliveryDetail:
        final delivery = settings.arguments as DeliveryModel;
        return MaterialPageRoute(
          builder: (_) => DeliveryDetailScreen(delivery: delivery),
          settings: settings,
        );

      case updateDeliveryStatus:
        final delivery = settings.arguments as DeliveryModel;
        return MaterialPageRoute(
          builder: (_) => UpdateDeliveryStatusScreen(delivery: delivery),
          settings: settings,
        );

      case reports:
        return MaterialPageRoute(
          builder: (_) => const ReportsScreen(),
          settings: settings,
        );

      case users:
        return MaterialPageRoute(
          builder: (_) => const UserListScreen(),
          settings: settings,
        );

      case userCreate:
        return MaterialPageRoute(
          builder: (_) => const AddEditUserScreen(),
          settings: settings,
        );

      case userDetail:
        final user = settings.arguments as UserModel;
        return MaterialPageRoute(
          builder: (_) => UserDetailScreen(user: user),
          settings: settings,
        );

      case userEdit:
        final user = settings.arguments as UserModel;
        return MaterialPageRoute(
          builder: (_) => AddEditUserScreen(user: user),
          settings: settings,
        );

      default:
        return MaterialPageRoute(
          builder: (_) => const LoginScreen(),
          settings: settings,
        );
    }
  }
}
