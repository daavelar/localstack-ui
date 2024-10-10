<?php

namespace App\Console\Commands;

use App\Events\MessageReceived;
use Illuminate\Console\Command;
use Aws\Sqs\SqsClient;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class ConsumeMessages extends Command
{
    protected $signature = 'messages:consume';
    protected $description = 'Consume messages from all SQS queues and save them to the database';

    private $sqs;

    public function __construct()
    {
        parent::__construct();

        $this->sqs = new SqsClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'endpoint' => env('AWS_ENDPOINT'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function handle()
    {
        $this->info("Starting to consume messages from all SQS queues");

        $queues = $this->listQueues();

        $this->info("Running in daemon mode. Press Ctrl+C to stop.");

        while (true) {
            $this->processAllQueues($queues);
            sleep(10);
        }
    }

    private function processAllQueues($queues)
    {
        foreach ($queues as $queueUrl) {
            $this->processQueue($queueUrl);
        }
    }

    private function listQueues()
    {
        try {
            $result = $this->sqs->listQueues();
            return $result['QueueUrls'] ?? [];
        } catch (\Exception $e) {
            $this->error("Error listing queues: " . $e->getMessage());
            return [];
        }
    }

    private function processQueue($queueUrl)
    {
        $this->info("Processing queue: $queueUrl");

        while (true) {
            try {
                $result = $this->sqs->receiveMessage([
                    'QueueUrl' => $queueUrl,
                    'MaxNumberOfMessages' => 10,
                    'WaitTimeSeconds' => 5,
                ]);

                if (empty($result['Messages'])) {
                    $this->info("No more messages in queue: $queueUrl");
                    break;
                }

                foreach ($result['Messages'] as $message) {
                    $this->processMessage($message, $queueUrl);
                }
            } catch (\Exception $e) {
                $this->error("Error processing queue $queueUrl: " . $e->getMessage());
                break;
            }
        }
    }

    private function processMessage($message, $queueUrl)
    {
        $queueUrlParsed = explode('/', $queueUrl);
        try {
            $existingMessage = Message::where('message_id', $message['MessageId'])->first();

            if ($existingMessage) {
                $this->info("Message {$message['MessageId']} already processed. Skipping.");
            } else {
                $createdAt = json_decode($message['Body'], true)['Timestamp'];
                Message::create([
                    'message_id' => $message['MessageId'],
                    'body' => $message['Body'],
                    'attributes' => json_encode($message['Attributes'] ?? []),
                    'queue' => end($queueUrlParsed),
                    'queue_url' => $queueUrl,
                    'receipt_handle' => $message['ReceiptHandle'],
                    'created_at' => $createdAt,
                ]);

                broadcast(new MessageReceived($message['MessageId']));

                $this->info("Processed and saved message: {$message['MessageId']}");
            }
        } catch (\Exception $e) {
            Log::error("Error processing message: " . $e->getMessage());
            $this->error("Error processing message: " . $e->getMessage());
        }
    }
}
