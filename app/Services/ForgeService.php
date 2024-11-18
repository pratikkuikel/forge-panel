<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Laravel\Forge\Forge;

class ForgeService
{
    protected $forge;

    public function __construct()
    {
        $this->initialize();
    }

    protected function initialize()
    {
        $this->forge = new Forge(config('forge.token'));
    }

    public static function make()
    {
        $static = app(static::class);

        return $static;
    }

    public function getServers()
    {
        return $this->forge->servers();
    }

    public function getAllSites($site_ids = [])
    {
        $sites = [];

        $servers = $this
            ->getServers();

        foreach ($servers as $server) {

            $temp = $this->getSites($server->id);

            $mappedSites = collect($temp)->map(function ($site) use ($server) {
                return [
                    'id' => $site->id,
                    'server_id' => $server->id,
                    'name' => $site->name,
                    'repository' => $site->repository,
                    'repositoryBranch' => $site->repositoryBranch,
                ];
            })
                ->reject(function ($site) use ($site_ids) {
                    // Include sites with matching ids and excludes those that don't match
                    if (!empty($site_ids)) {
                        return !in_array($site['id'], $site_ids);
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
        return $this->forge->sites($server_id);
    }

    public function site($server_id, $site_id)
    {
        return $this->forge->site($server_id, $site_id);
    }

    public function getSiteLog($server_id, $site_id)
    {
        return $this->site($server_id, $site_id)->siteLog();
    }
}
