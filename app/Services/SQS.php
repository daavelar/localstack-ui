<?php

namespace App\Services;

use Aws\Credentials\Credentials;
use Aws\Sqs\SqsClient;

class SQS
{
    public static function getClient(): SqsClient
    {
        return new SqsClient([
            'version' => 'latest',
            'region' => config('services.aws.region'),
            'endpoint' => config('services.aws.endpoint'),
            'credentials' => new Credentials(config('services.aws.key'), config('services.aws.secret'))
        ]);
    }

}
