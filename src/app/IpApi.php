<?php

declare(strict_types=1);

namespace App;

class IpApi
{
    protected string $apiKey;
    protected string $url = 'https://pro.ipapi.org/api_json/one.php';

    public function __construct()
    {
        $this->apiKey = $_ENV['IPAPI_API_KEY'];
    }

    public function get(string $ip): array
    {
        if (empty($ip)) {
            return ['error' => 'empty_ip'];
        }

        $url = $this->url . '?' . http_build_query([
            'key' => $this->apiKey,
            'ip' => $ip,
        ]);

        $response = file_get_contents($url);

        /**
         * array(14) {
  ["as"]=>
  string(27) "AS5089 Virgin Media Limited"
  ["city"]=>
  string(7) "Salford"
  ["country"]=>
  string(14) "United Kingdom"
  ["countryCode"]=>
  string(2) "GB"
  ["isp"]=>
  string(12) "Virgin Media"
  ["lat"]=>
  float(53.5082)
  ["lon"]=>
  float(-2.2648)
  ["org"]=>
  string(0) ""
  ["query"]=>
  string(11) "86.2.94.106"
  ["region"]=>
  string(3) "ENG"
  ["regionName"]=>
  string(7) "England"
  ["status"]=>
  string(7) "success"
  ["timezone"]=>
  string(13) "Europe/London"
  ["zip"]=>
  string(2) "M7"
}
         */
        return json_decode($response, true);
    }
}
