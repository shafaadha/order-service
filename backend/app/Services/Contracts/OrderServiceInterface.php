<?php

namespace App\Services\Contracts;

use App\Models\Order;

interface OrderServiceInterface
{
    public function createOrder(array $data);

    public function pay(Order $order);

    public function cancel(Order $order);
}
