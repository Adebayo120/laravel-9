<?php

namespace App\Utilities;

use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\Facades\Cache;

class RedisHelper implements RedisHelperInterface
{
    public function storeRecentMessages(array $newMessages): void
    {
        $key = config('app.redis_storage_key');

        $existingMessages = Cache::get($key, []);

        Cache::put($key, [...$existingMessages, ...$newMessages]);
    }

    public function getMessages(): array
    {
        return Cache::get(config('app.redis_storage_key'), []);
    }
}
