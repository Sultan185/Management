<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverView extends BaseWidget
{

    protected function getStats(): array
    {
        return [
            Stat::make('Assets Count',Asset::count())
            ->label(ucwords(__('assets.assets_count')))
            ->icon('heroicon-o-rectangle-stack'),

            Stat::make('Categories Count', Category::where('parent_id', null)->count())
            ->label(ucwords(__('categories.categories')))
            ->icon('heroicon-o-tag'),

            /*Stat::make('Categories Count', Category::where('parent_id','!=' ,null)->count())
            ->label(ucwords(__('categories.sub_categories_count')))
            ->icon('heroicon-o-tag'),*/
        ];
    }
}
