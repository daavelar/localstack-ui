<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws\Sns\SnsClient;
use Illuminate\Support\Str;

class ProduceMessages extends Command
{
    protected $signature = 'messages:produce {--count=1 : Number of messages to produce}';
    protected $description = 'Produce test messages and send them to a selected SNS topic';

    private $sns;

    public function __construct()
    {
        parent::__construct();

        $awsConfig = [
            'version' => 'latest',
            'region'  => config('aws.region', 'us-east-1'),
            'endpoint' => config('aws.endpoint', 'http://localhost:4566'),
            'credentials' => [
                'key'    => config('aws.key', 'test'),
                'secret' => config('aws.secret', 'test'),
            ],
        ];

        $this->sns = new SnsClient($awsConfig);
    }

    public function handle()
    {
        $topics = $this->getTopics();

        if (empty($topics)) {
            $this->error("No topics found. Please create a topic first.");
            return;
        }

        $topicChoices = array_map(function ($topic) {
            return $topic['TopicArn'];
        }, $topics);

        $selectedTopicArn = $this->choice(
            'Select a topic to publish messages to:',
            $topicChoices
        );

        $count = $this->option('count');

        $this->info("Producing {$count} message(s) to topic: {$selectedTopicArn}");

        for ($i = 0; $i < $count; $i++) {
            $this->produceMessage($selectedTopicArn);
        }

        $this->info("Finished producing messages.");
    }

    private function getTopics()
    {
        try {
            $result = $this->sns->listTopics();
            return $result['Topics'];
        } catch (\Exception $e) {
            $this->error("Failed to fetch topics: " . $e->getMessage());
            return [];
        }
    }

    private function produceMessage($topicArn)
    {
        $message = [
            'id' => Str::uuid()->toString(),
            'timestamp' => now()->toIso8601String(),
            'data' => 'This is a test message: ' . Str::random(10),
        ];

        try {
            $result = $this->sns->publish([
                'TopicArn' => $topicArn,
                'Message' => json_encode($message),
            ]);

            $this->info("Message sent. MessageId: " . $result['MessageId']);
        } catch (\Exception $e) {
            $this->error("Failed to send message: " . $e->getMessage());
        }
    }
}
