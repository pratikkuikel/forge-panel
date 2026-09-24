<?php

namespace App\Filament\Pages;

use App\Models\Site as ModelsSite;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Pages\Page;
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
                    ->url(fn (ModelsSite $record): string => SiteLog::getUrl([
                        'server' => $record->server_id,
                        'site' => $record->id,
                    ]))
                    ->icon('heroicon-o-document-text'),
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
