<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Aws\Sqs\SqsClient;

class MessageController extends Controller
{
    protected $sqs;

    public function __construct()
    {
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

    public function destroy($id)
    {
        $message = Message::findOrFail($id);

        $this->sqs->deleteMessage([
            'QueueUrl' => $message->queue_url,
            'ReceiptHandle' => $message->receipt_handle,
        ]);

        $message->delete();

        return response()->noContent();
    }
}
