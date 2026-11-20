<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderController extends Controller
{
    protected OrderServiceInterface $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|string|in:pending,processing,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $orders = Order::query()
            ->with(['customer', 'items'])
            ->withCount('items')
            ->when($validated['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($validated['start_date'] ?? null, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($validated['end_date'] ?? null, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->paginate($validated['per_page'] ?? 10);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $orders->items(),
            'meta' => [
                'page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
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

    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->createOrder($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order->load('items'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with('customer', 'items')
            ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'errors' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $order,
        ]);
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

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function pay(Order $order)
    {
        try {
            $order = $this->orderService->pay($order);

            return response()->json([
                'success' => true,
                'message' => 'Order successfully paid.',
                'data' => $order,
            ]);

        } catch (HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }
    }


    public function cancel(Order $order)
    {
        try {
            $order = $this->orderService->cancel($order);

            return response()->json([
                'success' => true,
                'message' => 'Order successfully cancelled.',
                'data' => $order,
            ]);

        } catch (HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }
    }
}
