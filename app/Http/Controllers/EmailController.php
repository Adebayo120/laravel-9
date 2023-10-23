<?php

namespace App\Http\Controllers;

use App\Http\Requests\Email\SendEmailRequest;
use App\Jobs\Email\SendEmailJob;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    // TODO: finish implementing send method
    public function send(SendEmailRequest $request): bool
    {
        SendEmailJob::dispatch($request->emails);

        /** @var ElasticsearchHelperInterface $elasticsearchHelper */
        $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);
        // TODO: Create implementation for storeEmail and uncomment the following line
        $elasticsearchHelper->storeEmails($request->emails);

        /** @var RedisHelperInterface $redisHelper */
        $redisHelper = app()->make(RedisHelperInterface::class);
        // TODO: Create implementation for storeRecentMessage and uncomment the following line
        $redisHelper->storeRecentMessages($request->emails);

        return true;
    }

    //  TODO - BONUS: implement list method
    public function list(RedisHelperInterface $redisHelper): JsonResponse
    {
        return response()->json($redisHelper->getMessages());
    }
}
