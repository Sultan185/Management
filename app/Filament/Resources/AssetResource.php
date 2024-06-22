<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Infolists\Components\BarcodeEntry;
use App\Models\Asset;
use App\Utils\GenerateBarcodeNumber;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action as ComponentsActionsAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action as TablesActionsAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getBreadcrumb(): string
    {
        return __('assets.assets');
    }
    public static function getNavigationLabel(): string
    {
        return __('assets.assets');
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')->label(__('assets.name'))
                        ->required()
                        ->maxLength(100)
                        ->live(onBlur:true)
                        ->reactive()
                        ->afterStateUpdated(function (Set $set, ?string $state, Get $get){
                            $barcode = $get('barcode_number');
                            if (!$barcode) {
                                $generated_code = GenerateBarcodeNumber::get();
                                $barcode_number = GenerateBarcodeNumber::ean13_check_digit($generated_code);

                                $set('barcode_number', $barcode_number);
                            }
                        }
                        ),
                    TextInput::make('barcode_number')->label(__('assets.barcode'))->unique('assets','barcode_number',ignoreRecord:true)->readOnly()->helperText('number is generated automatically'),
                    Select::make('category_id')->label(__('assets.category'))->relationship('category','name')->required(),
                    TextInput::make('location')->label(__('assets.location'))->required()->maxLength(100),
                    TextInput::make('responsible')->label(__('assets.responsible'))->required()->maxLength(100),
                    DatePicker::make('purchase_date')->label(__('assets.purchase_date'))->date()->required(),
                    RichEditor::make('purchase_information')->label(__('assets.purchase_information'))->columnSpanFull(),
                    RichEditor::make('additional_information')->label(__('assets.additional_information'))->columnSpanFull(),


                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label(__('assets.name'))
                ->searchable(),

                TextColumn::make('barcode_number')
                ->label(__('assets.barcode'))
                ->searchable(),

                TextColumn::make('category.name')
                ->label(__('assets.category'))
                ->searchable()
                ->badge()
                ->color('success'),

                /*TextColumn::make('category.main_category.name')
                ->label(__('assets.main_category'))
                ->searchable()
                ->badge()
                ->default('-')
                ->toggleable(),*/

                TextColumn::make('location')
                ->label(__('assets.location'))
                ->searchable()
                ->toggleable(),

                TextColumn::make('responsible')
                ->label(__('assets.responsible'))
                ->searchable()
                ->toggleable(),

                TextColumn::make('purchase_date')
                ->label(__('assets.purchase_date'))
                ->date()
                ->toggleable(),

                TextColumn::make('created_at')
                ->dateTime()
                ->toggleable(
                    fn()=>auth()->user()->hasRole('super_admin'),
                    isToggledHiddenByDefault:true
                    )
                ->visible(fn()=>auth()->user()->hasRole('super_admin')),

                TextColumn::make('updated_at')
                ->dateTime()
                ->toggleable(
                    fn()=>auth()->user()->hasRole('super_admin'),
                    isToggledHiddenByDefault:true
                    )
                ->visible(fn()=>auth()->user()->hasRole('super_admin')),


            ])
            ->filters([
                SelectFilter::make('categories')->label(__('assets.category'))
                ->relationship('category','name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->headerActions([
                ExportAction::make(),

                TablesActionsAction::make('printReport')
                ->icon('heroicon-o-printer')
                ->label(__('assets.print'))
                ->extraAttributes([
                        'x-on:click' => new HtmlString(<<<JS
                            () => {
                                document.querySelector('table').id = 'table';
                                var locale = document.getElementsByTagName('html')[0].getAttribute('lang');
                                printJS({printable:'table',type:'html', css:'/css/filament-assets-table-'+locale+'.css'});
                            }
                        JS),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {

        return $infolist->schema([
            Actions::make([
                ComponentsActionsAction::make('printBarcode')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->label(__('assets.print_barcode'))
                ->extraAttributes([
                        'x-on:click' => new HtmlString(<<<JS
                            () => {
                            printJS('barcode','html')
                            }
                        JS),
                ]),

                ComponentsActionsAction::make('printReport')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->label(__('assets.print'))
                ->extraAttributes([
                        'x-on:click' => new HtmlString(<<<JS
                            () => {
                                if(document.querySelector('#asset-info')){
                                    printJS({printable:'asset-info',type:'html', css:'/css/filament-asset-info-en.css'});
                                }
                                if(document.querySelector('#maalomat-alasl')){
                                    printJS({printable:'maalomat-alasl',type:'html', css:'/css/filament-asset-info-ar.css'});

                                }
                            }
                        JS),
                ]),
                ])->columnSpanFull(),
            ComponentsSection::make(__('assets.asset_info'))->schema([

                BarcodeEntry::make('barcode')->label(__('assets.barcode')),

                TextEntry::make('name')->label(__('assets.name')),

                TextEntry::make('category.name')->label(__('assets.category')),

                TextEntry::make('location')->label(__('assets.location')),

                TextEntry::make('responsible')->label(__('assets.responsible')),

                TextEntry::make('purchase_date')->label(__('assets.purchase_date')),

                TextEntry::make('purchase_information')
                ->html()
                ->default('-')
                ->columnSpanFull()
                ->label(__('assets.purchase_information')),

                TextEntry::make('additional_information')
                ->html()
                ->columnSpanFull()
                ->default('-')
                ->label(__('assets.additional_information')),

            ])->columns(2),
        ]);
    }
    public static function getRelations(): array
    {
        return [
            RelationManagers\ProceduresRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'view' => Pages\ViewAsset::route('/{record}'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
