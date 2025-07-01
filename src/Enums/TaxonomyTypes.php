<?php

namespace Net7\FilamentTaxonomies\Enums;

use Net7\FilamentTaxonomies\Traits\EnumHelper;

enum TaxonomyTypes: string
{
    use EnumHelper;

    case public = 'public';
    case restricted = 'restricted';
    case private = 'private';

}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
