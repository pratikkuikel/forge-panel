<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Forge;
use RuntimeException;

class ForgeService
{
    protected Forge $forge;

    protected ?string $organizationSlug = null;

    public function __construct()
    {
        $this->initialize();
    }

    protected function initialize(): void
    {
        $this->forge = new Forge(config('forge.token'));
        $this->organizationSlug = config('forge.organization');
    }

    public static function make()
    {
        $static = app(static::class);

        return $static;
    }

    public function getServers()
    {
        return $this->all($this->forge->servers($this->organizationSlug()));
    }

    public function getAllSites($site_ids = [])
    {
        $sites = [];
        $siteIds = array_map('strval', $site_ids);

        $servers = $this
            ->getServers();

        foreach ($servers as $server) {

            $temp = $this->getSites($server->id);

            $mappedSites = collect($temp)->map(function ($site) use ($server) {
                return [
                    'id' => $site->id,
                    'server_id' => $server->id,
                    'name' => $site->name,
                    'repository' => is_array($site->repository)
                        ? ($site->repository['url'] ?? null)
                        : $site->repository,
                    'repositoryBranch' => is_array($site->repository)
                        ? ($site->repository['branch'] ?? null)
                        : null,
                ];
            })
                ->reject(function ($site) use ($siteIds) {
                    // Include sites with matching ids and excludes those that don't match
                    if (! empty($siteIds)) {
                        return ! in_array((string) $site['id'], $siteIds, true);
                    }

                    return false;
                })
                ->toArray();

            $sites = [...$sites, ...$mappedSites];
        }

        return $sites;
    }

    public function getSiteNames($site_ids = [])
    {
        return Arr::pluck($this->getAllSites($site_ids), 'name', 'id');
    }

    public function getSites($server_id)
    {
        return $this->all(
            $this->forge->serverSites($this->organizationSlug(), (int) $server_id)
        );
    }

    public function site($server_id, $site_id)
    {
        return $this->forge->organizationSite($this->organizationSlug(), (int) $site_id);
    }

    public function getSiteLog($server_id, $site_id)
    {
        return [
            'content' => $this->forge->siteApplicationLog(
                $this->organizationSlug(),
                (int) $server_id,
                (int) $site_id,
            ),
        ];
    }

    public function deleteSiteLog($server_id, $site_id)
    {
        return $this->forge->deleteSiteApplicationLog(
            $this->organizationSlug(),
            (int) $server_id,
            (int) $site_id,
        );
    }

    protected function organizationSlug(): string
    {
        if (filled($this->organizationSlug)) {
            return $this->organizationSlug;
        }

        $organizations = $this->forge->organizations()->items();

        if (count($organizations) !== 1) {
            throw new RuntimeException(
                'Set FORGE_ORGANIZATION to the Forge organization slug when the token can access zero or multiple organizations.'
            );
        }

        return $this->organizationSlug = $organizations[0]->slug;
    }

    protected function all(CursorPaginator $paginator): array
    {
        return iterator_to_array($paginator->lazy(), false);
    }
}
