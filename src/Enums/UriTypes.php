<?php

namespace Net7\FilamentTaxonomies\Enums;

use Net7\FilamentTaxonomies\Traits\EnumHelper;

enum UriTypes: string
{
    use EnumHelper;

    case internal = 'internal';
    case external = 'external';

    public function getLabel(): string
    {
        return match ($this) {
            self::internal => 'Internal (Auto-generated)',
            self::external => 'External (Custom)',
        };
    }
}
