<?php

namespace App\Services;

abstract class RequestParameterMapping
{
    const OPERATORS_MAPPING = [
        'eq' => '=',
        'ne' => '!=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>='
    ];

    const NUMERIC_OPERATORS = [
        'eq', 'ne', 'lt', 'lte', 'gt', 'gte'
    ];

    const NON_NUMERIC_OPERATORS = [
        'eq', 'ne'
    ];
}
