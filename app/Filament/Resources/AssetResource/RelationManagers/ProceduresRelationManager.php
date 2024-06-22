<?php

namespace App\Filament\Resources\AssetResource\RelationManagers;

use App\Enums\ProcedureEnum;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProceduresRelationManager extends RelationManager
{
    protected static string $relationship = 'procedures';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\Select::make('name')
                    ->label(__('assets.procedure'))
                    ->options(ProcedureEnum::class)
                    ->required(),

                    Forms\Components\RichEditor::make('notes')
                    ->label(__('assets.notes')),
                ]),


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(__('assets.procedure'))
                ->badge()
                ->color('success'),

                Tables\Columns\TextColumn::make('notes')
                ->label(__('assets.notes'))
                ->html(),

                Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
