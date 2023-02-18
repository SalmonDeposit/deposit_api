<?php

namespace App\Filters;

use App\Services\RequestParameterMapping;
use Illuminate\Http\Request;

class ApiFilter
{
    protected $allowedParams = [];

    protected $columnMap = [];

    public function transform(Request $request): array
    {
        $query = [];

        foreach ($this->allowedParams as $allowedParam => $operators) {
            $queryField = $request->query($allowedParam);
            if (!isset($queryField)) {
                continue;
            }

            $column = $this->columnMap[$allowedParam] ?? $allowedParam;

            foreach ($operators as $operator) {
                if (isset($queryField[$operator])) {
                    $query[] = [$column, RequestParameterMapping::OPERATORS_MAPPING[$operator], $queryField[$operator]];
                }
            }
        }

        return $query;
    }
}
