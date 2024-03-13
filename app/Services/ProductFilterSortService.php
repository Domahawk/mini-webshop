<?php

namespace App\Services;

use App\Builders\ProductBuilder;
use App\Enums\Product\Filter;
use App\Enums\Product\Sort;
use App\Exceptions\BadRequestExceptions\IncorrectFilterTypeException;
use App\Exceptions\BadRequestExceptions\IncorrectSortException;

class ProductFilterSortService
{
    /**
     * @throws IncorrectFilterTypeException
     */
    public function applyFilters(array $filters, ProductBuilder $query): void
    {
        foreach ($filters as $filter => $value) {
            $filterEnum = Filter::create($filter);

            if (!$filterEnum->isCorrectType($value)) {
                throw new IncorrectFilterTypeException($filterEnum->value, $filterEnum->getFilterType());
            }

            match ($filterEnum) {
                Filter::NAME => $query->where('products.name', 'like', "%$value%"),
                Filter::PRICE => $query->having('price', '=', $value),
                Filter::CATEGORY => $query->filterByCategory($value),
                default => $query
            };
        }
    }

    /**
     * @throws IncorrectSortException
     */
    public function applySorts(array $sorts, ProductBuilder $query): void
    {
        foreach ($sorts as $sort => $value) {
            $sortEnum = Sort::create($sort);

            if (!$sortEnum->isCorrectType($value)) {
                throw new IncorrectSortException('asc, desc');
            }

            if ($sortEnum->isUndefined()) {
                continue;
            }

            $query->orderBy(strtolower($sortEnum->name), $value);
        }
    }
}
