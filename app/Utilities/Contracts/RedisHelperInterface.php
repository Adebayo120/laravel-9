<?php

namespace App\Utilities\Contracts;

interface RedisHelperInterface {
    /**
     * Store the messages in Redis.
     *
     * @param  array  $messages
     * @return void
     */
    public function storeRecentMessages(array $newMessages): void;

    /**
     * fetch the messages in Redis.
     *
     * @return  array  $messages
     */
    public function getMessages(): array;
}
