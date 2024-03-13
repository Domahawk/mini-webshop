<?php

namespace App\Http\Middleware;

use App\Enums\FilterableSortableRoute;
use App\Interfaces\Enum\FilterSort;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateFiltersAndSorts
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $filterSortRoute = FilterableSortableRoute::create($request->path());

        if ($filterSortRoute->isUndefined()) {
            $request->query->remove('filter');
            $request->query->remove('sort');

            return $next($request);
        }

        $query = $request->query->all();
        $validatedQuery = [];

        foreach ($filterSortRoute->filterSortConfiguration() as $name => $enum) {
            $request->query->remove($name);

            if (empty($enum)) {
                continue;
            }

            if (key_exists($name, $query) && is_array($query[$name]) && !empty($query[$name])) {
                $validatedQuery[$name] = $this->resolve($query[$name], $enum);
            }
        }

        $request->query->add($validatedQuery);

        return $next($request);
    }

    private function resolve(array $values, FilterSort $filterSortEnum): array
    {
        $validated = [];

        foreach ($values as $name => $value) {
            $enum = $filterSortEnum::create($name);

            if ($enum->isUndefined()) {
                continue;
            }

            $validated[$name] = $value;
        }

        return $validated;
    }
}
