<?php

namespace Net7\FilamentTaxonomies\Traits;

use Illuminate\Support\Str;

trait EnumHelper
{
    public static function options(): array
    {
        $cases = static::cases();
        $options = [];
        foreach ($cases as $case) {
            $options[$case->name] = Str::title($case->value);
        }

        return $options;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }
}
