<?php

namespace App\Http\Controllers;

use App\Events\TopicCreated;
use Aws\Sns\SnsClient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(
 *     title="Localstack UI API",
 *     version="1.0.0",
 *     description="API documentation for Localstack UI",
 *     @OA\Contact(
 *         email="diegoarmando2011@gmail.com"
 *     )
 * )
 */
class TopicController extends Controller
{
    protected $sns;

    public function __construct()
    {
        $this->sns = new SnsClient([
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
     *     path="/api/topics",
     *     summary="List all topics",
     *     tags={"Topics"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(type="string"))
     *     )
     * )
     */
    public function index()
    {
        try {
            $result = $this->sns->listTopics();
            $topics = [];

            foreach ($result['Topics'] as $topic) {
                $arn = $topic['TopicArn'];
                $name = $this->getTopicNameFromArn($arn);
                $url = $this->getTopicUrl($arn);

                $topics[] = [
                    'name' => $name,
                    'url' => $url,
                    'arn' => $arn,
                ];
            }

            return response()->json($topics);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getTopicUrl($arn): string
    {
        $region = env('AWS_DEFAULT_REGION');
        $accountId = explode(':', $arn)[4];
        $topicName = $this->getTopicNameFromArn($arn);

        return "https://sns.{$region}.amazonaws.com/{$accountId}/{$topicName}";
    }

    private function getTopicNameFromArn($arn): string
    {
        $parts = explode(':', $arn);

        return end($parts);
    }

    /**
     * @OA\Post(
     *     path="/api/topics",
     *     summary="Create a new topic",
     *     tags={"Topics"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Topic created successfully",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $this->sns->createTopic(['Name' => $request->input('name')]);

        broadcast(new TopicCreated($request->input('name')));

        return response()->json('Topic created successfully', Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/topics",
     *     summary="Delete a topic",
     *     tags={"Topics"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"topic"},
     *             @OA\Property(property="topic", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Topic deleted successfully"
     *     )
     * )
     */
    public function destroy($name)
    {
        return response()->json('Topic deleted successfully', Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->sns->deleteTopic(['TopicArn' => 'arn:aws:sns:us-east-1:000000000000:' . $name]);

        return response()->noContent();
    }
}
