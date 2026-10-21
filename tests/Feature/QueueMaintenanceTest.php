<?php

it('prunes failed queue jobs older than a week every day', function () {
    $this->artisan('schedule:list')
        ->expectsOutputToContain('queue:prune-failed --hours=168')
        ->expectsOutputToContain('queue:prune-batches')
        ->assertSuccessful();
});

it('leaves api routes to the modules instead of routes/api.php', function () {
    expect(file_get_contents(base_path('routes/api.php')))->not->toContain('Route::');
});
