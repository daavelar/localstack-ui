<?php

namespace App\Services;

use Aws\Sqs\SqsClient;

class SQS
{
    public static function getClient(): SqsClient
    {
        return new SqsClient([
            'version' => 'latest',
            'region' => config('services.aws.region'),
            'endpoint' => config('services.aws.sqs_endpoint'),
            'credentials' => [
                'key' => config('services.aws.key'),
                'secret' => config('services.aws.secret'),
                'token' => config('services.aws.token'),
            ],
        ]);
    }

}
