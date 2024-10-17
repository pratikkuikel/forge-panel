<?php

use App\Filament\Pages\Site;
use Illuminate\Support\Facades\Route;
use App\Services\ForgeService;

Route::get('/', function () {
    return redirect()->route('filament.app.auth.login');
    $forge = ForgeService::make();
    // dd(Server::all()->pluck('name', 'id'));
    dd($forge->getAllSites());
    // dd($forge->getServers());
    // 799013
    // dd($forge->server(799013));
    // dd($forge->getSites(799013));
});

Route::get('app/site/{server_id}', Site::class)
    ->middleware('auth')
    ->name('sites-for-server');

Route::get('login', function () {
    return redirect()->route('filament.app.auth.login');
})->name('login');
