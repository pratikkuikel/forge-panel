<?php

use App\Filament\Pages\Site as SitePage;
use App\Models\User;
use App\Role;
use App\Services\ForgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('creates the initial admin with a securely prompted password', function () {
    $this->artisan('app:add-admin', ['email' => 'admin@example.com'])
        ->expectsQuestion('Password (minimum 12 characters)', 'SecurePass1!')
        ->assertSuccessful();

    $admin = User::firstOrFail();

    expect($admin->role)->toBe(Role::ADMIN->value)
        ->and($admin->password)->not->toBe('SecurePass1!');
});

it('authorizes Forge sites assigned to teammates', function () {
    $teammate = User::factory()->create([
        'role' => Role::TEAMMATE->value,
        'sites' => [123],
    ]);

    expect($teammate->canAccessForgeSite(123))->toBeTrue()
        ->and($teammate->canAccessForgeSite(456))->toBeFalse();
});

it('allows administrators to access any Forge site', function () {
    $admin = User::factory()->create(['role' => Role::ADMIN->value]);

    expect($admin->canAccessForgeSite(456))->toBeTrue();
});

it('renders the sites page with Filament components', function () {
    $forge = Mockery::mock(ForgeService::class);
    $forge->shouldReceive('getAllSites')->once()->andReturn([]);

    $this->app->instance(ForgeService::class, $forge);

    $admin = User::factory()->create(['role' => Role::ADMIN->value]);

    $this->actingAs($admin)
        ->get(route('filament.app.pages.site'))
        ->assertOk();
});

it('opens the site log modal', function () {
    $forge = Mockery::mock(ForgeService::class);
    $forge->shouldReceive('getAllSites')->andReturn([
        [
            'id' => 123,
            'server_id' => 456,
            'name' => 'example.com',
            'repository' => 'example/repository',
            'repositoryBranch' => 'main',
        ],
    ]);
    $forge->shouldReceive('getSiteLog')->once()->with(456, 123)->andReturn([
        'content' => 'Example log',
    ]);

    $this->app->instance(ForgeService::class, $forge);

    $admin = User::factory()->create(['role' => Role::ADMIN->value]);

    $this->actingAs($admin);

    Livewire::test(SitePage::class)
        ->mountTableAction('view log', 123)
        ->assertMountedActionModalSee(['Example log', 'Close']);
});
