<?php

use App\Utilities\RedisHelper;
use Illuminate\Support\Facades\Cache;

afterEach(fn() => Cache::forget(config('app.redis_storage_key')));

describe('storeRecentMessages method', function () {
    it('should store messages in cache', function () {
        $email = [
            'subject' => 'mySubject1',
            'body' => 'myBody',
            'email_address' => 'testing@email.com'
        ];

        (new RedisHelper())->storeRecentMessages([$email]);

        expect(Cache::get(config('app.redis_storage_key'), []))->toBe([$email]);
    });

    it('should append new messages when it has existing messages', function () {
        $email = [
            'subject' => 'mySubject1',
            'body' => 'myBody',
            'email_address' => 'testing@email.com'
        ];

        (new RedisHelper())->storeRecentMessages([$email]);

        (new RedisHelper())->storeRecentMessages([$email]);

        expect(Cache::get(config('app.redis_storage_key'), []))->toBe([$email, $email]);
    });
});

describe('getMessages method', function () {
    it('should return messages in cache', function () {
        $email = [
            'subject' => 'mySubject1',
            'body' => 'myBody',
            'email_address' => 'testing@email.com'
        ];

        $redisHelper = new RedisHelper();

        $redisHelper->storeRecentMessages([$email]);

        expect($redisHelper->getMessages())->toBe([$email]);
    });

    it('should append new messages when it has existing messages', function () {
        $email = [
            'subject' => 'mySubject1',
            'body' => 'myBody',
            'email_address' => 'testing@email.com'
        ];

        (new RedisHelper())->storeRecentMessages([$email]);

        $redisHelper = new RedisHelper();

        $redisHelper->storeRecentMessages([$email]);

        expect($redisHelper->getMessages())->toBe([$email, $email]);
    });
});
