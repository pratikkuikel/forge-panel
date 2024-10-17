<?php

namespace App\Models;

use App\Role;
use App\Services\ForgeService;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Site extends Model
{
    use Sushi;

    protected static $server_id;

    protected static $site_ids = [];

    public static function getSitesForServer($value)
    {
        self::$server_id = $value;
        return static::query();
    }

    public static function getSitesForUser()
    {
        if (auth()->user()->role === Role::ADMIN->value) {
            return static::query();
        }

        self::$site_ids = auth()->user()->sites;

        return static::query();
    }

    public function getRows()
    {
        $sites = ForgeService::make()
            ->getAllSites(static::$site_ids);

        return $sites;
    }

    public function getSiteLog($server_id)
    {
        return ForgeService::make()
            ->getSiteLog($server_id, $this->id);
    }
}
