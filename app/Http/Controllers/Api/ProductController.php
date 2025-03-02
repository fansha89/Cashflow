<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(): JsonResponse
    {
        $products = Product::select('id', 'name', 'price', 'barcode', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Success get products',
            'data' => $products
        ], Response::HTTP_OK);
    }

    /**
     * Download the product template.
     */
    public function downloadTemplate()
    {
        $filePath = storage_path('app/templates/template.xlsx');

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Template file not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->download($filePath, 'Product_Template.xlsx');
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'barcode' => 'nullable|string|unique:products,barcode',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create($validated);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Product creation failed: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::select('id', 'name', 'price', 'barcode', 'created_at')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Product found',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'barcode' => 'sometimes|string|unique:products,barcode,' . $id,
        ]);

        DB::beginTransaction();
        try {
            $product->update($validated);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Product update failed: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Get product by barcode.
     */
    public function showByBarcode(string $barcode): JsonResponse
    {
        $product = Product::select('id', 'name', 'price', 'barcode', 'created_at')
            ->firstWhere('barcode', $barcode);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product found',
            'data' => $product
        ], Response::HTTP_OK);
    }
}
