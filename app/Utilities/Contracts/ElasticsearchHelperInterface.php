<?php

namespace App\Utilities\Contracts;

interface ElasticsearchHelperInterface {
    /**
     * Store the emails message body, subject and to address inside elasticsearch.
     *
     * @param  array  $emails
     * @return void
     */
    public function storeEmails(array $emails): void;
}
