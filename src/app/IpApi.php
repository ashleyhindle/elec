<?php

declare(strict_types=1);

namespace App;

class IpApi
{
    protected string $apiKey;
    protected string $url = 'https://api.ipgeolocation.io/ipgeo';

    public function __construct()
    {
        $this->apiKey = $_ENV['IPGEOLOCATION_API_KEY'];
    }

    public function get(string $ip): IpApiResponse
    {
        if (empty($ip)) {
            return IpApiResponse::error($ip, 'empty_ip');
        }

        if ($this->isIpInternal($ip)) {
            return IpApiResponse::error($ip, 'internal_or_invalid_ip');
        }

        $url = $this->url . '?' . http_build_query([
            'apiKey' => $this->apiKey,
            'ip' => $ip,
            'fields' => 'city,continent_name,country_name,time_zone,latitude,longitude',
        ]);

        $response = file_get_contents($url);

        return IpApiResponse::fromArray($ip, json_decode($response, true));
    }

    public function isIpInternal(?string $ip = null): bool
    {
        // filter_var returns false if the IP is in a private range, otherwise it returns the IP
        // so if the result is false, the IP is private/internal or invalid
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
