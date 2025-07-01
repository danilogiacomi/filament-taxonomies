<?php

namespace Net7\FilamentTaxonomies\Enums;

use Net7\FilamentTaxonomies\Traits\EnumHelper;

enum TaxonomyStates: string
{
    use EnumHelper;

    case working = 'working';
    case published = 'published';

    public function getLabel(): string
    {
        return match($this) {
            self::working => 'Working',
            self::published => 'Published',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::working => 'heroicon-o-wrench-screwdriver',
            self::published => 'heroicon-o-check-circle',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::working => 'warning',
            self::published => 'success',
        };
    }

}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
