<?php

namespace Net7\FilamentTaxonomies;

use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Net7\FilamentTaxonomies\Commands\FilamentTaxonomiesCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTaxonomiesServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-taxonomies';

    public static string $viewNamespace = 'filament-taxonomies';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasRoute('api')
            ->hasAssets()
            ->hasViews()
            // ->registerDependencyPublishable()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->publishAssets()
                    // ->publishViews()
                    ->askToRunMigrations()
                    // ->askToPublishAssets()
                    ->askToStarRepoOnGitHub('net7/filament-taxonomies');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__.'/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-taxonomies/{$file->getFilename()}"),
                ], 'filament-taxonomies-stubs');
            }
        }

        // Publish seeders
        if (app()->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/seeders' => database_path('seeders'),
            ], 'filament-taxonomies-seeders');
        }

    }

    protected function getAssetPackageName(): ?string
    {
        return 'Net7/filament-taxonomies';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentTaxonomiesCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_filament-taxonomies_table',
        ];
    }
}
