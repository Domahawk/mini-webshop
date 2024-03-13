<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestExceptions\UnsupportedProductException;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * @throws UnsupportedProductException
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $order = $this->orderService->createOrder($data);

        return $order;
    }

    public function show(Order $order)
    {
        //
    }
}
