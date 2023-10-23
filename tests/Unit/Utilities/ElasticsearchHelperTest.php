<?php

use Elasticsearch;
use App\Utilities\ElasticsearchHelper;

afterEach(function () {
    Elasticsearch::indices()
                ->delete([
                    'index' => config('elasticsearch.default_index')
                ]);
});

describe('storeEmails', function() {
    it('should store array of emails in index', function () {
        $email = [
            'subject' => 'mySubject1',
            'body' => 'myBody',
            'email_address' => 'testing@email.com'
        ];
    
        (new ElasticsearchHelper())->storeEmails([$email]);
    
        expect(Elasticsearch::get([
            'index' => config('elasticsearch.default_index'),
            'id'  => $email['email_address']
        ])['_source'])->toBe($email);
    });
});
