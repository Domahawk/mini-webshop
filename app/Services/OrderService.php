<?php

namespace App\Services;

use App\Exceptions\BadRequestExceptions\FailToCreateOrderException;
use App\Exceptions\BadRequestExceptions\UnsupportedProductException;
use App\Models\Order;
use App\Models\OrderProduct;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly PriceModifierService $modifierService,
        private readonly ProductService $productService,
    ) {}

    /**
     * @throws UnsupportedProductException
     * @throws FailToCreateOrderException
     */
    public function createOrder(array $data): Order
    {
        $products = $this->productService->getAvailableProducts($data);

        DB::beginTransaction();

        try {
            $productData = [];
            $orderTotal = 0;

            /** @var OrderProduct $product */
            foreach ($products as $product) {
                $productData[$product->product_id] = [
                    'amount' => $product->amount,
                    'price' => $product->price,
                ];

                $orderTotal += $product->price * $product->amount;
            }

            $newOrder = Order::create([
                'user_id' => Auth::id(),
                'total' => $orderTotal,
                'vat' => 0,
                'total_vat' => 0,
            ]);

            $this->modifierService->applyPriceModifiers($newOrder, $data['modifiers'] ?? null);

            $newOrder->products()->sync($productData);
            $newOrder->save();
        } catch (Exception $error) {
            DB::rollBack();

            throw new FailToCreateOrderException($error->getMessage());
        }

        DB::commit();


        return $newOrder->load(['orderProducts.product', 'priceModifiers']);
    }
}
