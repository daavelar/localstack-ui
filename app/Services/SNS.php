<?php

namespace App\Services;

use Aws\Sns\SnsClient;

class SNS
{
    public static function getClient(): SnsClient
    {
        return new SnsClient([
            'version' => 'latest',
            'region' => config('services.aws.region'),
            'endpoint' => config('services.aws.sns_endpoint'),
            'credentials' => [
                'key' => config('services.aws.key'),
                'secret' => config('services.aws.secret'),
                'token' => config('services.aws.token'),
            ],
        ]);
    }
}
