<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|distinct|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = DB::transaction(function () use ($validated) {

            $productIds = collect($validated['items'])
                ->pluck('product_id')
                ->sort()
                ->values();

            $products = Product::whereIn('id', $productIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $orderItems = [];
            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['product_id']);
                $quantity = $item['quantity'];

                if (!$product || $product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Stok {$product?->name} tidak mencukupi."
                        ]
                    ]);
                }

                $price = (float) $product->price;
                $itemSubtotal = round($price * $quantity, 2);

                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_code' => $product->product_code,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $taxRate = 0.11;
            $rawTax = $subtotal * $taxRate;
            $taxAmount = ceil($rawTax / 100) * 100;
            $grandTotal = $subtotal + $taxAmount;

            // 4. Generate order number
            do {
                $orderNumber = 'INV-' . now()->format('Ymd') . '-' .
                    str_pad(
                        (string) random_int(0, 9999),
                        4,
                        '0',
                        STR_PAD_LEFT
                    );
            } while (Order::where('order_number', $orderNumber)->exists());

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $validated['customer_id'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'status' => 'PENDING',
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);

                $product = $products->get($item['product_id']);
                $product->decrement('stock', $item['quantity']);
            }

            return $order;

        }, 3);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order->load('items'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
