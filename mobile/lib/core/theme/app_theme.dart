import 'package:flutter/material.dart';

class AppTheme {
  // Primary Brand Colors (Dairy Emerald Green Palette)
  static const Color primaryColor = Color(0xFF059669);   // Dairy Emerald Green
  static const Color primaryLight = Color(0xFF10B981);   // Light Emerald
  static const Color primaryDark = Color(0xFF047857);    // Dark Emerald
  static const Color accentColor = Color(0xFF065F46);     // Forest Accent

  // Background & Surface Tokens
  static const Color backgroundColor = Color(0xFFF8FAFC); // Slate 50 Light Gray
  static const Color surfaceColor = Colors.white;
  static const Color cardColor = Colors.white;
  static const Color borderLight = Color(0xFFE2E8F0);    // Slate 200

  // Text Colors
  static const Color textPrimary = Color(0xFF0F172A);     // Slate 900
  static const Color textSecondary = Color(0xFF475569);   // Slate 600
  static const Color textMuted = Color(0xFF94A3B8);       // Slate 400

  // State & Indicator Colors
  static const Color errorColor = Color(0xFFEF4444);       // Red 500
  static const Color successColor = Color(0xFF10B981);     // Green 500
  static const Color warningColor = Color(0xFFF59E0B);     // Amber 500
  static const Color infoColor = Color(0xFF3B82F6);        // Blue 500

  // Soft Pastel Tint Backgrounds for Stat Cards
  static const Color pastelGreenBg = Color(0xFFECFDF5);
  static const Color pastelBlueBg = Color(0xFFEFF6FF);
  static const Color pastelAmberBg = Color(0xFFFEF3C7);
  static const Color pastelPurpleBg = Color(0xFFF3E8FF);

  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: primaryColor,
        primary: primaryColor,
        secondary: accentColor,
        surface: surfaceColor,
        error: errorColor,
      ),
      scaffoldBackgroundColor: backgroundColor,
      appBarTheme: const AppBarTheme(
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: false,
        titleTextStyle: TextStyle(
          fontSize: 19,
          fontWeight: FontWeight.w700,
          color: Colors.white,
          letterSpacing: -0.2,
        ),
      ),
      cardTheme: CardThemeData(
        color: cardColor,
        elevation: 0,
        margin: EdgeInsets.zero,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: Color(0xFFF1F5F9), width: 1),
        ),
      ),
      chipTheme: ChipThemeData(
        backgroundColor: const Color(0xFFF1F5F9),
        selectedColor: primaryColor.withValues(alpha: 0.15),
        secondarySelectedColor: primaryColor,
        labelStyle: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: textPrimary),
        secondaryLabelStyle: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: primaryColor),
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
          side: BorderSide.none,
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryColor,
          foregroundColor: Colors.white,
          minimumSize: const Size.fromHeight(52),
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          textStyle: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.w600,
            letterSpacing: 0.2,
          ),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFCBD5E1)),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: primaryColor, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: errorColor),
        ),
        focusedErrorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: errorColor, width: 2),
        ),
        labelStyle: const TextStyle(color: textSecondary, fontSize: 14),
        hintStyle: const TextStyle(color: textMuted, fontSize: 14),
      ),
    );
  }
}

