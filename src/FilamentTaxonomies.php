<?php

namespace Net7\FilamentTaxonomies;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource;
use Net7\FilamentTaxonomies\Filament\Resources\TermResource;

class FilamentTaxonomies implements Plugin
{
    public function getId(): string
    {
        return 'filament-taxonomies';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                TaxonomyResource::class,
                TermResource::class,
            ])
            ->pages([
                //
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
