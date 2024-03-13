<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryController extends Controller
{
    public function showCategoryProducts(Category $category): LengthAwarePaginator
    {
        return Product::query()
            ->productsByLoggedInUser()
            ->productsBySelectedCategory($category)
            ->paginate(5);
    }
}
