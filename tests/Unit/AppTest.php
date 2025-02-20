<?php

declare(strict_types=1);

use App\App;
use Laravel\Prompts\Key;

test('keys work correctly', function () {
    $db = new SQLite3(realpath(__DIR__ . '/../../whc.db'));
    $app = new App($db, '127.0.0.1');
    $app->getSitesNear(5, 52, 6);
    $app->listenForKeys();

    expect($app->siteIndex)->toBe(0);

    $app->emit('key', Key::RIGHT_ARROW);
    expect($app->siteIndex)->toBe(1);

    $app->emit('key', Key::RIGHT_ARROW);
    expect($app->siteIndex)->toBe(2);

    $app->emit('key', Key::DOWN_ARROW);
    expect($app->siteIndex)->toBe(5);

    $app->emit('key', Key::LEFT_ARROW);
    expect($app->siteIndex)->toBe(4);

    $app->emit('key', Key::LEFT_ARROW);
    expect($app->siteIndex)->toBe(3);

    $app->emit('key', Key::LEFT_ARROW);
    expect($app->siteIndex)->toBe(2);

    $app->emit('key', Key::LEFT_ARROW);
    expect($app->siteIndex)->toBe(1);

    $app->emit('key', Key::LEFT_ARROW);
    expect($app->siteIndex)->toBe(0);

    $app->emit('key', Key::LEFT_ARROW);
    expect($app->siteIndex)->toBe(5);
});

test('q quits the app', function () {
    $db = new SQLite3(realpath(__DIR__ . '/../../whc.db'));
    $app = new App($db, '127.0.0.1');
    App::fake();

    $app->terminal()->shouldReceive('exit')->once();
    $app->listenForKeys();
    $app->emit('key', 'q');
});

test('category filter works correctly', function () {
    $db = new SQLite3(realpath(__DIR__ . '/../../whc.db'));
    $app = new App($db, '127.0.0.1');
    $app->categories = ['Cultural'];
    $app->getSitesNear(5, 52, 6);
    $app->listenForKeys();

    expect(collect($app->closestSites)->pluck('category')->unique()->toArray())->toBe(['Cultural']);
});
