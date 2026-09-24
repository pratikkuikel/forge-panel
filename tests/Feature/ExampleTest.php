<?php

it('redirects guests to the Filament login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('filament.app.auth.login'));
});
