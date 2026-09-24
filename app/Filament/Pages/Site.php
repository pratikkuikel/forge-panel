<?php

namespace App\Filament\Pages;

use App\Models\Site as ModelsSite;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\StaticAction;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Site extends Page implements HasActions, HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.site';

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
            ->recordActions([
                Action::make('view log')
                    ->modalContent(function (ModelsSite $record) {
                        $log = $record->getSiteLog($record->server_id);

                        return view('hello-world', compact('log'));
                    })
                    ->modalWidth(Width::FiveExtraLarge)
                    ->modalCancelAction(fn (StaticAction $action) => $action->label('Close')),
                Action::make('delete log')
                    ->action(function (ModelsSite $record) {
                        $record->deleteSiteLog($record->server_id);
                    })
                    ->color('danger')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                // ...
            ]);
    }
}
