<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderService implements OrderServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $productIds = collect($data['items'])
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

            foreach ($data['items'] as $index => $item) {
                $product = $products->get($item['product_id']);
                $quantity = $item['quantity'];

                if (!$product || $product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        "items.{$index}.quantity" => [
                            "Stok {$product?->name} tersisa {$product?->stock}, diminta {$quantity}"
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

            $taxAmount = ceil(($subtotal * 0.11) / 100) * 100;
            $grandTotal = $subtotal + $taxAmount;

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
                'customer_id' => $data['customer_id'],
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
    }

    public function pay(Order $order): Order
    {
        return DB::transaction(function () use ($order) {

            $order = Order::where('id', $order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->status !== 'PENDING') {
                throw new HttpException(409, 'Order cannot be paid.');
            }

            $order->update([
                'status' => 'PAID',
            ]);

            return $order->fresh();
        });
    }

    public function cancel(Order $order): Order
    {
        return DB::transaction(function () use ($order) {

            $order = Order::where('id', $order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($order->status, ['PENDING', 'PAID'])) {
                throw new HttpException(409, 'Order cannot be cancelled.');
            }

            $items = $order->items()
                ->orderBy('product_id')
                ->get();

            foreach ($items as $item) {
                $product = Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $product->increment('stock', $item->quantity);
            }

            $order->update([
                'status' => 'CANCELLED',
            ]);

            return $order->fresh();
        });
    }
}
