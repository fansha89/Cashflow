<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class PaymentMethodController extends Controller
{
    /**
     * Retrieve all payment methods.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $paymentMethods = PaymentMethod::all();

        return response()->json([
            'success' => true,
            'message' => 'Found ' . $paymentMethods->count() . ' Payment Methods',
            'data' => $paymentMethods->toArray()
        ], Response::HTTP_OK);
    }
}
