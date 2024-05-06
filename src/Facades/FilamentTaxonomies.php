<?php

namespace Net7\FilamentTaxonomies\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Net7\FilamentTaxonomies\FilamentTaxonomies
 */
class FilamentTaxonomies extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Net7\FilamentTaxonomies\FilamentTaxonomies::class;
    }
}
