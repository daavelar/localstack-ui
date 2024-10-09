<?php

namespace App\Http\Controllers;

use App\Events\QueueCreated;
use App\Events\QueueDeleted;
use App\Http\Requests\CreateQueueRequest;
use App\Http\Requests\DestroyQueueRequest;
use App\Http\Requests\PurgeQueueRequest;
use App\Models\Message;
use Aws\Sqs\SqsClient;
use Illuminate\Http\JsonResponse;

class QueueController extends Controller
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

    /**
     * @OA\Get(
     *     path="/api/queues",
     *     summary="List all queues",
     *     tags={"Queues"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(type="string"))
     *     )
     * )
     */
    public function index()
    {
        $queues = $this->sqs->listQueues();

        $queueList = [];

        if (empty($queues['QueueUrls'])) {
            return response()->json();
        }

        foreach ($queues['QueueUrls'] as $queueUrl) {
            $queueAttributes = $this->sqs->getQueueAttributes([
                'QueueUrl' => $queueUrl,
                'AttributeNames' => ['QueueArn'],
            ]);

            $queueName = basename($queueUrl);
            $queueArn = $queueAttributes['Attributes']['QueueArn'];

            $queueList[] = [
                'name' => $queueName,
                'url' => $queueUrl,
                'arn' => $queueArn,
            ];
        }

        return response()->json($queueList);
    }

    /**
     * @OA\Post(
     *     path="/api/queues",
     *     summary="Create a new queue",
     *     tags={"Queues"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Queue created successfully",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function store(CreateQueueRequest $request)
    {
        $queueName = $request->input('name');

        $this->sqs->createQueue(['QueueName' => $queueName]);

        broadcast(new QueueCreated($queueName));

        return response()->json('Queue created successfully', JsonResponse::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/queues/{queue}",
     *     summary="Get queue attributes",
     *     tags={"Queues"},
     *     @OA\Parameter(
     *         name="queue",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function show($queue)
    {
        $fullQueueName = 'http://sqs.us-east-1.localhost.localstack.cloud:4566/000000000000/' . $queue;

        $result = $this->sqs->getQueueAttributes([
            'QueueUrl' => $fullQueueName,
            'AttributeNames' => ['All'],
        ]);

        return response()->noContent($result['Attributes']);
    }

    /**
     * @OA\Post(
     *     path="/api/queues/purge",
     *     summary="Purge a queue",
     *     tags={"Queues"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"queue"},
     *             @OA\Property(property="queue", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Queue purged successfully"
     *     )
     * )
     */
    public function purge(PurgeQueueRequest $request)
    {
        $this->sqs->purgeQueue(['QueueUrl' => $request->get('queue')]);

        return response()->noContent();
    }

    /**
     * @OA\Delete(
     *     path="/api/queues",
     *     summary="Delete a queue",
     *     tags={"Queues"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"queue"},
     *             @OA\Property(property="queue", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Queue deleted successfully"
     *     )
     * )
     */
    public function destroy(DestroyQueueRequest $request)
    {
        $this->sqs->deleteQueue(['QueueUrl' => $request->get('queueUrl')]);

        broadcast(event: new QueueDeleted($request->get('queueName')));

        return response()->noContent();
    }

    public function messages($queue)
    {
        $messages = Message::whereQueue($queue)->latest()->take(100)->get();

        return $messages;
    }
}
