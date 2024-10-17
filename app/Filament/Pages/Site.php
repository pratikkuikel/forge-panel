<?php

namespace App\Filament\Pages;

use App\Models\Site as ModelsSite;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Actions\StaticAction;

class Site extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.site';

    protected static bool $shouldRegisterNavigation = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(ModelsSite::getSitesForUser())
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('repository'),
                TextColumn::make('repositoryBranch'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Tables\Actions\Action::make('view log')
                    ->modalContent(function (ModelsSite $record) {
                        $log = $record->getSiteLog($record->server_id);
                        return view('hello-world', compact('log'));
                    })
                    ->modalWidth(MaxWidth::FiveExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
