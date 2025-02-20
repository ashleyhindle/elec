<?php

declare(strict_types=1);

namespace App;

class IpApiResponse
{
    public string $ip;
    public string $city = '';
    public string $continent = '';
    public string $country = '';
    public \DateTimeImmutable $currentTime;
    public float $lat = 0;
    public float $lon = 0;
    public string $timezone = '';
    public ?string $error = null;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(string $ip, array $data): self
    {
        $response = new self;
        $response->ip = $ip;
        $response->city = $data['city'] ?? '';
        $response->continent = $data['continent_name'] ?? '';
        $response->country = $data['country_name'] ?? '';
        $response->currentTime = new \DateTimeImmutable($data['time_zone']['current_time'] ?? '');
        $response->lat = (float)($data['latitude'] ?? 0);
        $response->lon = (float)($data['longitude'] ?? 0);
        $response->timezone = $data['time_zone']['name'] ?? '';

        return $response;
    }

    public static function error(string $ip, string $error): self
    {
        $response = new self;
        $response->ip = $ip;
        $response->error = $error;

        return $response;
    }

}
