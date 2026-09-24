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
        return ForgeService::make()
            ->getServers();
    }
}
