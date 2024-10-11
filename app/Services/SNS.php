<?php

namespace App\Services;

use Aws\Credentials\Credentials;
use Aws\Sns\SnsClient;

class SNS
{
    public static function getClient(): SnsClient
    {
        return new SnsClient([
            'version' => 'latest',
            'region' => config('services.aws.region'),
            'endpoint' => config('services.aws.endpoint'),
            'credentials' => new Credentials(config('services.aws.key'), config('services.aws.secret'))
        ]);
    }
}
