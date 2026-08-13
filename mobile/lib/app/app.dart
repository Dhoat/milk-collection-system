import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../core/network/api_client.dart';
import '../core/storage/secure_token_storage.dart';
import '../core/storage/token_storage_interface.dart';
import '../core/theme/app_theme.dart';
import '../core/widgets/loading_indicator.dart';
import '../features/auth/providers/auth_provider.dart';
import '../features/auth/repositories/auth_repository.dart';
import '../features/auth/repositories/auth_repository_interface.dart';
import '../features/auth/screens/login_screen.dart';
import '../features/dashboard/providers/dashboard_provider.dart';
import '../features/dashboard/repositories/dashboard_repository.dart';
import '../features/dashboard/repositories/dashboard_repository_interface.dart';
import '../features/dashboard/screens/home_screen.dart';
import '../features/deliveries/providers/delivery_provider.dart';
import '../features/deliveries/repositories/delivery_repository.dart';
import '../features/deliveries/repositories/delivery_repository_interface.dart';
import '../features/farmers/providers/farmer_provider.dart';
import '../features/farmers/repositories/farmer_repository.dart';
import '../features/farmers/repositories/farmer_repository_interface.dart';
import '../features/milk_collection/providers/milk_collection_provider.dart';
import '../features/milk_collection/repositories/milk_collection_repository.dart';
import '../features/milk_collection/repositories/milk_collection_repository_interface.dart';
import '../features/milk_receiving/providers/milk_receiving_provider.dart';
import '../features/milk_receiving/repositories/milk_receiving_repository.dart';
import '../features/milk_receiving/repositories/milk_receiving_repository_interface.dart';
import '../features/milk_stock/providers/milk_stock_provider.dart';
import '../features/milk_stock/repositories/milk_stock_repository.dart';
import '../features/milk_stock/repositories/milk_stock_repository_interface.dart';
import '../features/profile/providers/profile_provider.dart';
import '../features/profile/repositories/profile_repository.dart';
import '../features/profile/repositories/profile_repository_interface.dart';
import '../features/reports/providers/report_provider.dart';
import '../features/reports/repositories/report_repository.dart';
import '../features/reports/repositories/report_repository_interface.dart';
import '../features/shop_orders/providers/shop_order_provider.dart';
import '../features/shop_orders/repositories/shop_order_repository.dart';
import '../features/shop_orders/repositories/shop_order_repository_interface.dart';
import '../features/shops/providers/shop_provider.dart';
import '../features/shops/repositories/shop_repository.dart';
import '../features/shops/repositories/shop_repository_interface.dart';
import '../features/villages/providers/village_provider.dart';
import '../features/villages/repositories/village_repository.dart';
import '../features/villages/repositories/village_repository_interface.dart';
import '../features/users/providers/user_provider.dart';
import '../features/users/repositories/user_repository.dart';
import '../features/users/repositories/user_repository_interface.dart';
import 'routes/app_routes.dart';

class MilkCenterApp extends StatelessWidget {
  final ITokenStorage? tokenStorage;
  final ApiClient? apiClient;
  final IAuthRepository? authRepository;
  final IDashboardRepository? dashboardRepository;
  final IProfileRepository? profileRepository;
  final IVillageRepository? villageRepository;
  final IFarmerRepository? farmerRepository;
  final IMilkCollectionRepository? milkCollectionRepository;
  final IMilkReceivingRepository? milkReceivingRepository;
  final IMilkStockRepository? milkStockRepository;
  final IShopRepository? shopRepository;
  final IShopOrderRepository? shopOrderRepository;
  final IDeliveryRepository? deliveryRepository;
  final IReportRepository? reportRepository;
  final IUserRepository? userRepository;

  const MilkCenterApp({
    super.key,
    this.tokenStorage,
    this.apiClient,
    this.authRepository,
    this.dashboardRepository,
    this.profileRepository,
    this.villageRepository,
    this.farmerRepository,
    this.milkCollectionRepository,
    this.milkReceivingRepository,
    this.milkStockRepository,
    this.shopRepository,
    this.shopOrderRepository,
    this.deliveryRepository,
    this.reportRepository,
    this.userRepository,
  });

  @override
  Widget build(BuildContext context) {
    final effectiveStorage = tokenStorage ?? SecureTokenStorage();
    final effectiveApiClient = apiClient ?? HttpApiClient(tokenStorage: effectiveStorage);
    final effectiveAuthRepo = authRepository ??
        AuthRepository(
          apiClient: effectiveApiClient,
          tokenStorage: effectiveStorage,
        );
    final effectiveDashboardRepo = dashboardRepository ??
        DashboardRepository(apiClient: effectiveApiClient);
    final effectiveProfileRepo = profileRepository ??
        ProfileRepository(apiClient: effectiveApiClient);
    final effectiveVillageRepo = villageRepository ??
        VillageRepository(apiClient: effectiveApiClient);
    final effectiveFarmerRepo = farmerRepository ??
        FarmerRepository(apiClient: effectiveApiClient);
    final effectiveMilkCollectionRepo = milkCollectionRepository ??
        MilkCollectionRepository(apiClient: effectiveApiClient);
    final effectiveMilkReceivingRepo = milkReceivingRepository ??
        MilkReceivingRepository(apiClient: effectiveApiClient);
    final effectiveMilkStockRepo = milkStockRepository ??
        MilkStockRepository(apiClient: effectiveApiClient);
    final effectiveShopRepo = shopRepository ??
        ShopRepository(apiClient: effectiveApiClient);
    final effectiveShopOrderRepo = shopOrderRepository ??
        ShopOrderRepository(apiClient: effectiveApiClient);
    final effectiveDeliveryRepo = deliveryRepository ??
        DeliveryRepository(apiClient: effectiveApiClient);
    final effectiveReportRepo = reportRepository ??
        ReportRepository(apiClient: effectiveApiClient);
    final effectiveUserRepo = userRepository ??
        UserRepository(apiClient: effectiveApiClient);

    return MultiProvider(
      providers: [
        Provider<ITokenStorage>.value(value: effectiveStorage),
        Provider<ApiClient>.value(value: effectiveApiClient),
        Provider<IAuthRepository>.value(value: effectiveAuthRepo),
        Provider<IDashboardRepository>.value(value: effectiveDashboardRepo),
        Provider<IProfileRepository>.value(value: effectiveProfileRepo),
        Provider<IVillageRepository>.value(value: effectiveVillageRepo),
        Provider<IFarmerRepository>.value(value: effectiveFarmerRepo),
        Provider<IMilkCollectionRepository>.value(value: effectiveMilkCollectionRepo),
        Provider<IMilkReceivingRepository>.value(value: effectiveMilkReceivingRepo),
        Provider<IMilkStockRepository>.value(value: effectiveMilkStockRepo),
        Provider<IShopRepository>.value(value: effectiveShopRepo),
        Provider<IShopOrderRepository>.value(value: effectiveShopOrderRepo),
        Provider<IDeliveryRepository>.value(value: effectiveDeliveryRepo),
        Provider<IReportRepository>.value(value: effectiveReportRepo),
        Provider<IUserRepository>.value(value: effectiveUserRepo),
        ChangeNotifierProvider<AuthProvider>(
          create: (_) => AuthProvider(
            authRepository: effectiveAuthRepo,
            tokenStorage: effectiveStorage,
          )..initialize(),
        ),
        ChangeNotifierProvider<DashboardProvider>(
          create: (_) => DashboardProvider(
            dashboardRepository: effectiveDashboardRepo,
          ),
        ),
        ChangeNotifierProvider<ProfileProvider>(
          create: (_) => ProfileProvider(
            profileRepository: effectiveProfileRepo,
          ),
        ),
        ChangeNotifierProvider<VillageProvider>(
          create: (_) => VillageProvider(
            villageRepository: effectiveVillageRepo,
          ),
        ),
        ChangeNotifierProvider<FarmerProvider>(
          create: (_) => FarmerProvider(
            farmerRepository: effectiveFarmerRepo,
          ),
        ),
        ChangeNotifierProvider<MilkCollectionProvider>(
          create: (_) => MilkCollectionProvider(
            repository: effectiveMilkCollectionRepo,
          ),
        ),
        ChangeNotifierProvider<MilkReceivingProvider>(
          create: (_) => MilkReceivingProvider(
            repository: effectiveMilkReceivingRepo,
          ),
        ),
        ChangeNotifierProvider<MilkStockProvider>(
          create: (_) => MilkStockProvider(
            repository: effectiveMilkStockRepo,
          ),
        ),
        ChangeNotifierProvider<ShopProvider>(
          create: (_) => ShopProvider(
            repository: effectiveShopRepo,
          ),
        ),
        ChangeNotifierProvider<ShopOrderProvider>(
          create: (_) => ShopOrderProvider(
            repository: effectiveShopOrderRepo,
          ),
        ),
        ChangeNotifierProvider<DeliveryProvider>(
          create: (_) => DeliveryProvider(
            repository: effectiveDeliveryRepo,
          ),
        ),
        ChangeNotifierProvider<ReportProvider>(
          create: (_) => ReportProvider(
            repository: effectiveReportRepo,
          ),
        ),
        ChangeNotifierProvider<UserProvider>(
          create: (_) => UserProvider(
            repository: effectiveUserRepo,
          ),
        ),
      ],
      child: MaterialApp(
        title: 'Milk Center System',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.lightTheme,
        onGenerateRoute: AppRoutes.onGenerateRoute,
        home: Consumer<AuthProvider>(
          builder: (context, authProvider, _) {
            if (authProvider.isChecking) {
              return const Scaffold(
                body: LoadingIndicator(message: 'Verifying session...'),
              );
            }
            if (authProvider.isAuthenticated) {
              return const HomeScreen();
            }
            return const LoginScreen();
          },
        ),
      ),
    );
  }
}
