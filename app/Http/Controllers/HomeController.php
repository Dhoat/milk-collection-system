<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\Village;
use App\Models\MilkCollection;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the Milk Center public landing homepage.
     */
    public function index()
    {
        // Dynamic business statistics using Eloquent models
        $farmerCount = Farmer::count();
        $villageCount = Village::count();
        $totalMilkCollected = MilkCollection::sum('milk_quantity');
        $productCount = Product::count();

        $stats = [
            'farmers' => $farmerCount > 0 ? $farmerCount : 150,
            'villages' => $villageCount > 0 ? $villageCount : 12,
            'milk_collected' => $totalMilkCollected > 0 ? $totalMilkCollected : 45000,
            'products' => $productCount > 0 ? $productCount : 6,
        ];

        // Default showcase list of 6 dairy products with full specifications
        $defaultProducts = [
            [
                'name' => 'Fresh Milk',
                'category' => 'Fresh Dairy',
                'badge' => 'Pasteurized Whole Milk',
                'description' => 'Farm-fresh, pasteurized whole milk packed with essential vitamins, minerals, calcium, and natural creaminess.',
                'color_theme' => 'emerald',
                'icon_type' => 'bottle',
                'specs' => [
                    ['label' => 'Fat Content', 'val' => '4.5% - 5.0%'],
                    ['label' => 'SNF (Solid Not Fat)', 'val' => '8.5%'],
                    ['label' => 'Processing', 'val' => 'HTST Pasteurized'],
                    ['label' => 'Storage Temp', 'val' => '4°C Cold Chain']
                ]
            ],
            [
                'name' => 'Desi Ghee',
                'category' => 'Clarified Butter',
                'badge' => 'Traditional Slow-Cooked',
                'description' => 'Pure, aromatic golden clarified butter slow-cooked from authentic hand-churned cultured milk butter.',
                'color_theme' => 'amber',
                'icon_type' => 'jar',
                'specs' => [
                    ['label' => 'Purity Level', 'val' => '99.8% Milk Fat'],
                    ['label' => 'Method', 'val' => 'Bilona / Traditional'],
                    ['label' => 'Aroma', 'val' => 'Rich Golden Granular'],
                    ['label' => 'Shelf Life', 'val' => '12 Months']
                ]
            ],
            [
                'name' => 'Paneer (Cottage Cheese)',
                'category' => 'Cheese & Curd',
                'badge' => 'Fresh High Protein',
                'description' => 'Fresh, soft cottage cheese made from pure whole milk. High in protein density, ideal for gourmet cooking.',
                'color_theme' => 'teal',
                'icon_type' => 'block',
                'specs' => [
                    ['label' => 'Protein Content', 'val' => '18g per 100g'],
                    ['label' => 'Moisture', 'val' => 'Soft & Moist Block'],
                    ['label' => 'Additives', 'val' => 'Zero Preservatives'],
                    ['label' => 'Storage', 'val' => 'Chilled 2°C - 4°C']
                ]
            ],
            [
                'name' => 'Fresh Dahi (Yogurt)',
                'category' => 'Cultured Dairy',
                'badge' => 'Natural Probiotic',
                'description' => 'Thick, creamy natural yogurt cultured under controlled temperatures for maximum digestive health & flavor.',
                'color_theme' => 'sky',
                'icon_type' => 'bowl',
                'specs' => [
                    ['label' => 'Culture', 'val' => 'Active L. bulgaricus'],
                    ['label' => 'Consistency', 'val' => 'Thick & Creamy'],
                    ['label' => 'Taste Profile', 'val' => 'Mild & Natural'],
                    ['label' => 'Packaging', 'val' => 'Matka / Sealed Tub']
                ]
            ],
            [
                'name' => 'Sweet & Salted Lassi',
                'category' => 'Dairy Beverage',
                'badge' => 'Authentic Churned',
                'description' => 'Traditional churned yogurt drink, perfectly blended with rock salt or cardamom for a refreshing cooling drink.',
                'color_theme' => 'emerald',
                'icon_type' => 'glass',
                'specs' => [
                    ['label' => 'Base', 'val' => 'Whole Milk Yogurt'],
                    ['label' => 'Variants', 'val' => 'Sweet / Salted / Mango'],
                    ['label' => 'Serving Temp', 'val' => 'Ice Chilled 2°C'],
                    ['label' => 'Volume', 'val' => '250ml / 500ml']
                ]
            ],
            [
                'name' => 'Spiced Buttermilk (Chhaach)',
                'category' => 'Traditional Drink',
                'badge' => 'Digestive Herbal',
                'description' => 'Light, soothing digestive buttermilk seasoned with roasted cumin powder, black salt, and fresh mint leaves.',
                'color_theme' => 'cyan',
                'icon_type' => 'pitcher',
                'specs' => [
                    ['label' => 'Fat Content', 'val' => 'Less than 1.0%'],
                    ['label' => 'Herbs & Spices', 'val' => 'Cumin & Fresh Mint'],
                    ['label' => 'Benefits', 'val' => 'Aids Digestion'],
                    ['label' => 'Packaging', 'val' => 'Sealed Bottle / Pouch']
                ]
            ],
        ];

        // Fetch DB products if any exist in the database
        $dbProducts = Product::where('status', true)->get();
        if ($dbProducts->count() > 0) {
            $products = $dbProducts->map(function ($p) use ($defaultProducts) {
                $matched = collect($defaultProducts)->firstWhere('name', $p->name);
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'category' => ucfirst(str_replace('_', ' ', $p->category ?? ($matched['category'] ?? 'Dairy Product'))),
                    'badge' => $matched['badge'] ?? ('Unit: ' . $p->unit),
                    'description' => $p->notes ?? ($matched['description'] ?? 'Pure, fresh dairy product processed at our central milk center.'),
                    'color_theme' => $matched['color_theme'] ?? 'emerald',
                    'icon_type' => $matched['icon_type'] ?? 'bottle',
                    'specs' => $matched['specs'] ?? [
                        ['label' => 'Product Code', 'val' => $p->product_code ?? 'PRD-' . $p->id],
                        ['label' => 'Category', 'val' => ucfirst(str_replace('_', ' ', $p->category))],
                        ['label' => 'Unit', 'val' => $p->unit],
                        ['label' => 'Available Stock', 'val' => number_format($p->available_stock, 1) . ' ' . $p->unit]
                    ]
                ];
            })->toArray();
        } else {
            $products = $defaultProducts;
        }

        return view('welcome', compact('stats', 'products'));
    }
}
