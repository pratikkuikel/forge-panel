<?php

namespace App\Models;

use App\Services\ForgeService;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Server extends Model
{
    use Sushi;

    public function getRows()
    {
        $servers = ForgeService::make()
            ->getServers();

        $servers = collect($servers)->map(function ($server) {
            return [
                'id' => $server->id,
                'name' => $server->name,
                'size' => $server->size,
                'ipAddress' => $server->ipAddress,
            ];
        })
            ->toArray();

        return $servers;
    }
}
