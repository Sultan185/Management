<?php

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function(LanguageSwitch $switch){
            $switch->locales(['en','ar']);
        });

        FilamentAsset::register([
            Css::make('print.min.css', 'https://printjs-4de6.kxcdn.com/print.min.css'),
            Js::make('print.min.js', 'https://printjs-4de6.kxcdn.com/print.min.js'),

        ]);
    }
}
