<?php

namespace App\Filament\Pages;

use App\Services\ForgeService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class SiteLog extends Page
{
    protected static ?string $slug = 'site/{server}/{site}/log';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.site-log';

    public int $server;

    public int $site;

    /** @var array<string, mixed> */
    public array $siteDetails;

    public string $log;

    public function mount(string $server, string $site): void
    {
        abort_unless(ctype_digit($server) && ctype_digit($site), 404);

        $this->server = (int) $server;
        $this->site = (int) $site;

        abort_unless(auth()->user()?->canAccessForgeSite($this->site), 403);

        $forge = ForgeService::make();
        $siteDetails = collect($forge->getAllSites([$this->site]))
            ->first(fn (array $candidate): bool => (
                ((int) $candidate['id'] === $this->site) &&
                ((int) $candidate['server_id'] === $this->server)
            ));

        abort_unless($siteDetails, 404);

        $this->siteDetails = $siteDetails;
        $this->refreshLog($forge);
    }

    public function getTitle(): string|Htmlable
    {
        return "Application log: {$this->siteDetails['name']}";
    }

    public function getSubheading(): ?string
    {
        return 'Fetched live from Laravel Forge';
    }

    /** @return array<Action> */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to sites')
                ->url(Site::getUrl())
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
            Action::make('delete_log')
                ->label('Delete log')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->action(function (): void {
                    ForgeService::make()->deleteSiteLog($this->server, $this->site);
                    $this->log = '';

                    Notification::make()
                        ->title('Application log deleted')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function refreshLog(?ForgeService $forge = null): void
    {
        $log = ($forge ?? ForgeService::make())->getSiteLog($this->server, $this->site);
        $this->log = (string) ($log['content'] ?? '');
    }
}
