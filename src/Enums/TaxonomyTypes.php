<?php

namespace Net7\FilamentTaxonomies\Enums;

use Net7\FilamentTaxonomies\Traits\EnumHelper;

enum TaxonomyTypes: string
{
    use EnumHelper;

    case public = 'public';
    case restricted = 'restricted';
    case private = 'private';

    public function getLabel(): string
    {
        return match($this) {
            self::public => 'Public',
            self::restricted => 'Restricted',
            self::private => 'Private',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::public => 'heroicon-o-globe-alt',
            self::restricted => 'heroicon-o-lock-open',
            self::private => 'heroicon-o-lock-closed',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::public => 'success',
            self::restricted => 'warning',
            self::private => 'danger',
        };
    }

}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
