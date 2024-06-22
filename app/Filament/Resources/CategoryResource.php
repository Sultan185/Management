<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getBreadcrumb(): string
    {
        return __('categories.categories');
    }

    // public static function getNavigationBadge(): ?string
    // {

    //     return static::getModel()::where('parent_id', null)->count();
    // }

    public static function getNavigationGroup(): ?string
    {
        return __('settings.settings');
    }


    public static function getNavigationLabel(): string
    {
        return __('categories.categories');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make()->schema([
                        TextInput::make('name')->label(__('categories.name'))->required()->unique()->maxLength(100)->live(onBlur:true),
                        //Select::make('parent_id')->label(__('categories.main_category'))->relationship('main_category','name')->helperText("dont't use the same category as a parent"),
                    ]),
                ]),
                /*Group::make()->schema([
                    Repeater::make('sub_categories')->label(__('categories.sub_categories'))->relationship()->schema([
                            TextInput::make('name')->label(__('categories.name'))->required()->unique()->maxLength(100)->live(onBlur:true)
                    ]),
                ]),*/


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->
                label(__('categories.name')),

                /*TextColumn::make('sub_categories.name')
                ->label(__('categories.sub_categories'))
                ->toggleable()
                ->default('-')
                ->badge(),*/

                /*TextColumn::make('main_category.name')
                ->label(__('categories.main_category'))
                ->toggleable()
                ->default('-')
                ->badge()
                ->color('success'),*/

                TextColumn::make('assets')
                    ->label(__('assets.assets_count'))
                    ->state(function (Model $record): string {
                        return $record->assets()->get()->count();
                    })
                    ->toggleable()
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->headerActions([
                ExportAction::make(),
                Action::make('printBarcode')
                ->icon('heroicon-o-printer')
                ->label(__('categories.print'))
                ->extraAttributes([
                        'x-on:click' => new HtmlString(<<<JS
                            () => {
                                document.querySelector('table').id = 'table';
                                printJS({printable:'table',type:'html',css:'css/filament-categories-table.css'});
                            }
                        JS),
                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
