<?php

namespace App\Services;

use App\Exceptions\BadRequestExceptions\FailToCreateOrderException;
use App\Exceptions\BadRequestExceptions\UnsupportedProductException;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * @throws UnsupportedProductException
     */
    public function getAvailableProducts(array $data): array
    {
        $productIds = collect();

        foreach ($data['products'] as $productData) {
            $productIds->put($productData['productId'], $productData['amount']);
        }

        $products = Product::query()
            ->productsByLoggedInUser()
            ->whereIn('products.id', $productIds->keys())
            ->get()
            ->mapWithKeys(fn (Product $product) => [$product->id => $product]);

        $missingProductIds = $productIds->keys()->filter(fn (string $id) => !$products->has($id));

        if ($missingProductIds->isNotEmpty()) {
            throw new UnsupportedProductException($missingProductIds->implode(', '));
        }

        $orderProducts = [];

        foreach ($productIds as $productId => $amount) {
            $orderProducts[] = new OrderProduct([
                'product_id' => $productId,
                'price' => $products->get($productId)->price,
                'amount' => $amount,
            ]);
        }

        return $orderProducts;
    }
}
