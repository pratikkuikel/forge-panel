<?php

use App\Services\ForgeService;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    config([
        'cache.default' => 'array',
        'forge.cache_ttl' => 86400,
    ]);

    Cache::flush();
});

it('caches Forge servers and sites for the configured lifetime', function () {
    $server = ['id' => 456, 'name' => 'Server'];
    $site = ['id' => 123, 'name' => 'example.com'];

    $forge = Mockery::mock(ForgeService::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $forge->shouldReceive('organizationSlug')->times(4)->andReturn('test-org');
    $forge->shouldReceive('fetchServers')->once()->with('test-org')->andReturn([$server]);
    $forge->shouldReceive('fetchSites')->once()->with('test-org', 456)->andReturn([$site]);

    expect($forge->getServers())->toEqual([$server])
        ->and($forge->getServers())->toEqual([$server])
        ->and($forge->getSites(456))->toEqual([$site])
        ->and($forge->getSites(456))->toEqual([$site]);
});
