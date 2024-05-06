<?php

namespace net7\FilamentTaxonomies\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \net7\FilamentTaxonomies\FilamentTaxonomies
 */
class FilamentTaxonomies extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \net7\FilamentTaxonomies\FilamentTaxonomies::class;
    }
}
