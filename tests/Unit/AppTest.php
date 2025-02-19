<?php

declare(strict_types=1);

test('ip is internal', function () {
    $app = new App\App('192.168.1.1');
    expect($app->isIpInternal('192.168.1.1'))->toBeTrue();
    expect($app->isIpInternal('10.0.0.1'))->toBeTrue();
    expect($app->isIpInternal('172.16.0.1'))->toBeTrue();
    expect($app->isIpInternal('172.31.255.255'))->toBeTrue();
    expect($app->isIpInternal(''))->toBeTrue();

    expect($app->isIpInternal('172.32.0.1'))->toBeFalse();
    expect($app->isIpInternal('8.8.8.8'))->toBeFalse();
});
