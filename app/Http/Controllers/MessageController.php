<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Aws\Sns\SnsClient;
use Aws\Sqs\SqsClient;
use Illuminate\Http\Request;

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

    public function sendMessage(Request $request)
    {
        $request->validate([
            'arn' => 'required|string',
            'message' => 'required|string',
        ]);

        $sns = new SnsClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'endpoint' => env('AWS_ENDPOINT'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'token' => env('AWS_SESSION_TOKEN'),
            ],
        ]);

        try {
            $result = $sns->publish([
                'Message' => $request->message,
                'TopicArn' => $request->arn,
            ]);

            return response()->json(['message' => 'Message sent successfully', 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
