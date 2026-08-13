<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of active products for shop order creation.
     *
     * GET /api/products
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::where('status', true)->get();

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => ProductResource::collection($products),
        ], 200);
    }
}
