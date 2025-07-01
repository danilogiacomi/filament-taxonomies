<?php

namespace Net7\FilamentTaxonomies\Enums;

use Net7\FilamentTaxonomies\Traits\EnumHelper;

enum TaxonomyStates: string
{
    use EnumHelper;

    case working = 'working';
    case published = 'published';
    case deleted = 'deleted';

}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
