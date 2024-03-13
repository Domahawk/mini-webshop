<?php

namespace App\Builders;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductBuilder extends Builder
{
    public function productsByLoggedInUser(): self
    {
        return $this->select([
            'products.id',
            'products.name',
            'products.description',
            'products.sku',
            'products.published',
        ])->selectRaw('coalesce(contract_lists.price, price_list_product.price) as price')
            ->leftJoin('contract_lists', 'products.sku', '=', 'contract_lists.sku')
            ->join('price_list_product', 'products.sku', '=', 'price_list_product.sku')
            ->join('price_lists', 'price_list_product.price_list_id', '=', 'price_lists.id')
            ->join('users', 'price_lists.id', '=', 'users.price_list_id')
            ->where('users.id', '=', Auth::id());
    }

    public function productsBySelectedCategory(Category $category): self
    {
        return $this
            ->join('category_product', 'products.id', '=', 'category_product.product_id')
            ->whereIn('category_product.category_id', $this->getAllCategoryChildrenIds($category));
    }

    public function filterByCategory(string $value): self
    {
        return $this->join(
            'category_product',
            'category_product.product_id',
            '=',
            'products.id'
        )
            ->join('categories','category_product.category_id','=','categories.id')
            ->join('categories as parent_categories','categories.parent_id','=','parent_categories.id')
            ->where(function (Builder $query) use ($value) {
                $query->where('categories.name', 'like', "%$value%")
                    ->orWhere('parent_categories.name', 'like', "%$value%");
            });
    }

    public function sortByName(string $value): self
    {
        return $this->orderBy('name', $value);
    }

    public function sortByPrice(string $value): self
    {
        return $this->orderBy('price', $value);
    }

    private function getAllCategoryChildrenIds(Category $category): array
    {
        $categories = [$category->id];

        foreach ($category->children as $child) {
            if ($child->children->isNotEmpty()) {
                $categories = array_merge($categories, $this->getAllCategoryChildrenIds($child));

                continue;
            }

            $categories[] = $child->id;
        }

        return $categories;
    }
}
