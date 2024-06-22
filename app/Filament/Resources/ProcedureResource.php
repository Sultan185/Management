<?php

namespace App\Filament\Resources;

use App\Enums\ProcedureEnum;
use App\Filament\Resources\ProcedureResource\Pages;
use App\Filament\Resources\ProcedureResource\RelationManagers;
use App\Models\Asset;
use App\Models\Procedure;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcedureResource extends Resource
{
    protected static ?string $model = Procedure::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getBreadcrumb(): string
    {
        return __('assets.procedures');
    }

    public static function getNavigationLabel(): string
    {
        return __('assets.procedures');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Select::make('asset_id')
                    ->label(__('assets.name'))
                    ->relationship('asset','name')
                    ->searchable()
                    ->preload()
                    ->required(),

                    Select::make('name')
                    ->label(__('assets.procedure'))
                    ->options(ProcedureEnum::class)
                    ->required(),

                    RichEditor::make('notes')
                    ->label(__('assets.notes')),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(__('assets.procedure'))
                ->badge()->color('success'),

                Tables\Columns\TextColumn::make('asset.name')
                ->label(__('assets.name'))
                ->searchable(),

                Tables\Columns\TextColumn::make('asset.barcode_number')
                ->label(__('assets.barcode'))
                ->searchable(),

                Tables\Columns\TextColumn::make('notes')
                ->label(__('assets.notes'))
                ->html()
                ->toggleable()
                ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProcedures::route('/'),
            'create' => Pages\CreateProcedure::route('/create'),
            'edit' => Pages\EditProcedure::route('/{record}/edit'),
        ];
    }
}
