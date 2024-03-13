<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestExceptions\IncorrectFilterTypeException;
use App\Exceptions\BadRequestExceptions\IncorrectSortException;
use App\Models\Product;
use App\Services\ProductFilterSortService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductFilterSortService $filterSortService,
    ) {}

    public function index(): LengthAwarePaginator
    {
        return Product::query()->productsByLoggedInUser()->paginate(5);
    }

    /**
     * @throws IncorrectFilterTypeException
     * @throws IncorrectSortException
     */
    public function filterProducts(Request $request): array
    {
        $query = Product::query()->productsByLoggedInUser();
        $this->filterSortService->applyFilters($request->query->all('filter'), $query);
        $this->filterSortService->applySorts($request->query->all('sort'), $query);

        return $query->get()->toArray();
    }

    public function show(Product $product): array
    {
        $userProduct = Product::query()
            ->productsByLoggedInUser()
            ->where('product.id', '=', $product->id)
            ->get();

        if ($userProduct->isEmpty()) {
            return [
                'message' => 'No products with that id'
            ];
        }

        return $userProduct->first()->load(['categories'])->toArray();
    }
}
