<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;
use App\Services\RequestParameterMapping;

class UserQueryFilter extends ApiFilter
{
    protected $allowedParams = [
        'email' => RequestParameterMapping::NON_NUMERIC_OPERATORS,
        'simon_coin_stock' => RequestParameterMapping::NUMERIC_OPERATORS,
        'email_verified_at' => RequestParameterMapping::NUMERIC_OPERATORS
    ];

    protected $columnMap = [
        'email' => 'email',
        'simonCoinStock' => 'simon_coin_stock',
        'emailVerifiedAt' => 'email_verified_at'
    ];
}
