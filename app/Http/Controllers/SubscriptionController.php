<?php

namespace App\Http\Controllers;

use App\Events\SubscriptionDeleted;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Http\Requests\DeleteSubscriptionRequest;
use App\Services\SNS;
use Aws\Arn\Arn;
use Aws\Arn\ArnParser;
use Aws\Sns\Exception\SnsException;
use Aws\Sns\SnsClient;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends Controller
{
    protected $sns;

    public function __construct()
    {
        $this->sns = SNS::getClient();
    }

    /**
     * @OA\Get(
     *     path="/api/subscriptions",
     *     tags={"Subscriptions"},
     *     summary="Listar todas as subscrições",
     *     description="Retorna uma lista de todas as subscrições SNS/SQS",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de subscrições",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="queue", type="string", example="my-queue-name"),
     *                 @OA\Property(property="topic", type="string", example="my-topic-name")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Mensagem de erro")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $result = $this->sns->listSubscriptions();
            $subscriptions = $result['Subscriptions'];


            $formattedSubscriptions = array_map(function ($subscription) {
                $arnParsed = ArnParser::parse($subscription['SubscriptionArn']);
                return [
                    'arn_resource' => explode(':', $arnParsed->getResource())[1],
                    'queue' => $this->extractQueueName($subscription['Endpoint']),
                    'topic' => $this->extractTopicName($subscription['TopicArn']),
                ];
            }, $subscriptions);

            return response()->json($formattedSubscriptions);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function extractQueueName($queueArn): string
    {
        $parts = explode(':', $queueArn);
        return end($parts);
    }

    private function extractTopicName($topicArn): string
    {
        $parts = explode(':', $topicArn);
        return end($parts);
    }

    /**
     * @OA\Get(
     *     path="/api/subscriptions/{topic}",
     *     summary="List subscriptions by topic",
     *     tags={"Subscriptions"},
     *     @OA\Parameter(
     *         name="topic",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(type="string"))
     *     )
     * )
     */
    public function topic($topic)
    {
        $result = $this->sns->listSubscriptionsByTopic([
            'TopicArn' => 'arn:aws:sns:us-east-1:000000000000:' . $topic,
        ]);

        if (empty($result['Subscriptions'])) {
            return response()->json([]);
        }

        $subscriptions = array_map(function ($subscription) {
            return $subscription['SubscriptionArn'];
        }, $result['Subscriptions']);

        return response()->json($subscriptions);
    }

    /**
     * @OA\Post(
     *     path="/api/subscriptions",
     *     tags={"Subscriptions"},
     *     summary="Create a new subscription",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="queue", type="object",
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="url", type="string"),
     *                 @OA\Property(property="arn", type="string")
     *             ),
     *             @OA\Property(property="topic", type="object",
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="url", type="string"),
     *                 @OA\Property(property="arn", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Subscription created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="subscription", type="object",
     *                 @OA\Property(property="queue", type="string"),
     *                 @OA\Property(property="topic", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function store(CreateSubscriptionRequest $request)
    {
        try {
            $existingSubscriptions = $this->sns->listSubscriptionsByTopic([
                'TopicArn' => $request->input('topic'),
            ]);

            foreach ($existingSubscriptions['Subscriptions'] as $subscription) {
                if ($subscription['Protocol'] === 'sqs' && $subscription['Endpoint'] === $request->input('queue')) {
                    return response()->json('Subscription already exists', Response::HTTP_BAD_REQUEST);
                }
            }

            $this->sns->subscribe([
                'TopicArn' => $request->input('topic'),
                'Protocol' => 'sqs',
                'Endpoint' => $request->input('queue'),
            ]);

            return response()->json(['message' => 'Subscription created successfully', Response::HTTP_CREATED]);
        } catch (SnsException $e) {
            return response()->json([$e->getMessage()], 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/subscriptions/{subscriptionArn}",
     *     tags={"Subscriptions"},
     *     summary="Delete a subscription",
     *     @OA\Parameter(
     *         name="subscriptionArn",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscription deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Subscription not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function destroy(DeleteSubscriptionRequest $request)
    {
        $arn = 'arn:aws:sns:us-east-1:000000000000:' . $request->get('topic') . ':' . $request->get('arn_resource');

        try {
            $this->sns->unsubscribe([
                'SubscriptionArn' => $arn,
            ]);

            event(new SubscriptionDeleted());

            return response()->json(['message' => 'Subscription deleted successfully ']);
        } catch (SnsException $e) {
            if ($e->getAwsErrorCode() === 'NotFound') {
                return response()->json(['Subscription not found'], 404);
            }
            return response()->json([$e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 500);
        }
    }
}
