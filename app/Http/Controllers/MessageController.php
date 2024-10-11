<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Services\SNS;
use App\Services\SQS;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $sqs;

    public function __construct()
    {
        $this->sqs = SQS::getClient();
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'arn' => 'required|string',
            'message' => 'required|string',
        ]);

        $sns = SNS::getClient();

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
