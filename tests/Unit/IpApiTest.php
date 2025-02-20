<?php

declare(strict_types=1);

use App\IpApi;
use App\IpApiResponse;

test('ip is internal', function () {
    $ipApi = new IpApi('apikey');
    expect($ipApi->isIpInternal('192.168.1.1'))->toBeTrue();
    expect($ipApi->isIpInternal('10.0.0.1'))->toBeTrue();
    expect($ipApi->isIpInternal('172.16.0.1'))->toBeTrue();
    expect($ipApi->isIpInternal('172.31.255.255'))->toBeTrue();
    expect($ipApi->isIpInternal(''))->toBeTrue();

    expect($ipApi->isIpInternal('172.32.0.1'))->toBeFalse();
    expect($ipApi->isIpInternal('8.8.8.8'))->toBeFalse();
});

test('returns error for empty ip', function () {
    $ipApi = new IpApi('apikey');
    expect($ipApi->get(''))->toBeInstanceOf(IpApiResponse::class)->toMatchObject([
        'error' => 'empty_ip',
    ]);
});

test('returns error for invalid ip', function () {
    $ipApi = new IpApi('apikey');
    expect($ipApi->get('invalid_ip'))->toBeInstanceOf(IpApiResponse::class)->toMatchObject([
        'error' => 'internal_or_invalid_ip',
    ]);

    expect($ipApi->get('256.256.256.256'))->toBeInstanceOf(IpApiResponse::class)->toMatchObject([
        'error' => 'internal_or_invalid_ip',
    ]);
});

test('returns error for internal ip', function () {
    $ipApi = new IpApi('apikey');
    expect($ipApi->get('127.0.0.1'))->toBeInstanceOf(IpApiResponse::class)->toMatchObject([
        'error' => 'internal_or_invalid_ip',
    ]);
});
