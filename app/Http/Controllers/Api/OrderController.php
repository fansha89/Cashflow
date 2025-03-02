<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $orders = Order::with(['paymentMethod', 'orderProducts.product'])->get();

        $orders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'name' => $order->name,
                'email' => $order->email,
                'phone' => $order->phone,
                'total_price' => $order->total_price,
                'payment_method' => $order->paymentMethod->name ?? '-',
                'items' => $order->orderProducts->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? '-',
                        'quantity' => $item->quantity ?? 0,
                        'unit_price' => $item->unit_price ?? 0
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved ' . $orders->count() . ' orders',
            'data' => $orders
        ], Response::HTTP_OK);
    }

    /**
     * Store a new order.
     */
    public function store(StoreOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();
        $productIds = collect($data['items'])->pluck('product_id');

        // Ambil semua produk dalam 1 query untuk meningkatkan performa
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $totalPriceCalculated = 0;

        foreach ($data['items'] as $item) {
            $product = $products[$item['product_id']] ?? null;
            if (!$product || $product->stock < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock of the product: ' . ($product->name ?? 'Unknown') . ' is not sufficient'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Hitung total harga berdasarkan harga asli produk untuk menghindari manipulasi
            $totalPriceCalculated += $item['quantity'] * $product->price;
        }

        // Validasi apakah total price yang dikirim user sesuai dengan total yang dihitung
        if ($totalPriceCalculated != $data['total_price']) {
            return response()->json([
                'success' => false,
                'message' => 'Total price mismatch. Expected: ' . $totalPriceCalculated
            ], Response::HTTP_BAD_REQUEST);
        }

        // Gunakan transaksi untuk menjaga konsistensi data
        DB::beginTransaction();

        try {
            $order = Order::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'gender' => $data['gender'],
                'birthday' => $data['birthday'],
                'phone' => $data['phone'],
                'total_price' => $data['total_price'],
                'note' => $data['note'],
                'payment_method_id' => $data['payment_method_id'],
            ]);

            foreach ($data['items'] as $item) {
                $order->orderProducts()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ]);
            }

            // Kurangi stok produk dalam batch untuk efisiensi
            $products->chunk(10)->each(function ($chunk) use ($data) {
                foreach ($chunk as $product) {
                    $orderItem = collect($data['items'])->firstWhere('product_id', $product->id);
                    if ($orderItem) {
                        $product->decrement('stock', $orderItem['quantity']);
                    }
                }
            });

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order successfully created',
                'data' => [
                    'id' => $order->id,
                    'name' => $order->name,
                    'email' => $order->email,
                    'phone' => $order->phone,
                    'total_price' => $order->total_price,
                    'payment_method' => $order->paymentMethod->name ?? '-',
                    'items' => $order->orderProducts->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name ?? '-',
                            'quantity' => $item->quantity ?? 0,
                            'unit_price' => $item->unit_price ?? 0
                        ];
                    })
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Order failed: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
