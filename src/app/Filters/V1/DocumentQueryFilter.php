<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;
use App\Services\RequestParameterMapping;

class DocumentQueryFilter extends ApiFilter
{
    protected $allowedParams = [
        'size' => RequestParameterMapping::NUMERIC_OPERATORS,
        'type' => RequestParameterMapping::NON_NUMERIC_OPERATORS
    ];

    protected $columnMap = [
        'size' => 'size',
        'type' => 'type'
    ];
}
