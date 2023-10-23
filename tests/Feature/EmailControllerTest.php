<?php

use App\Jobs\Email\SendEmailJob;
use App\Utilities\RedisHelper;
use Illuminate\Support\Facades\Queue;
use Elasticsearch;
use Illuminate\Support\Facades\Cache;

describe('/api/{user}/send', function () {
    it('should throw validation error when api_token is not specified', function () {
        $response = $this->postJson('api/1/send');

        $response->assertJsonValidationErrorFor('api_token');
    });

    it('should throw validation error when emails is not specified', function () {
        $response = $this->postJson('api/1/send');

        $response->assertJsonValidationErrorFor('emails');
    });

    it('should throw validation error when emails specified but is not an array', function () {
        $response = $this->postJson('api/1/send', ['emails' => 'hello']);

        $response->assertJsonValidationErrorFor('emails');
    });

    it('should throw validation error when subject is not specified in an array of email', function () {
        $response = $this->postJson('api/1/send', ['emails' => [['body' => 'myBody']]]);

        $response->assertJsonValidationErrorFor('emails.0.subject');
    });

    it('should throw validation error when body is not specified in an array of email', function () {
        $response = $this->postJson('api/1/send', ['emails' => [['subject' => 'mySubject']]]);

        $response->assertJsonValidationErrorFor('emails.0.body');
    });

    it('should throw validation error when email address is not specified in an array of email', function () {
        $response = $this->postJson('api/1/send', ['emails' => [[]]]);

        $response->assertJsonValidationErrorFor('emails.0.email_address');
    });

    it('should throw validation error when email address is specified in an array of email but not a valid email', function () {
        $response = $this->postJson('api/1/send', ['emails' => [['email_address' => 'myEmailAddress']]]);

        $response->assertJsonValidationErrorFor('emails.0.email_address');
    });

    it('should not dispatch send email job when there is a validation error', function () {
        Queue::fake();

        $this->postJson('api/1/send', ['emails' => [['email_address' => 'myEmailAddress']]]);

        Queue::assertNothingPushed();

        Queue::assertNotPushed(SendEmailJob::class);
    });

    it('should return successful response when valid request is sent', function () {
        $response = $this->postJson('api/1/send', [
            'api_token' => 'myApiToken',
            'emails' => [[
                'subject' => 'mySubject1',
                'body' => 'myBody',
                'email_address' => 'testing@email.com'
            ]]
        ]);

        $response->assertStatus(200);
    });

    describe('when valid request is sent', function () {
        beforeEach(function () {
            Queue::fake();

            $this->email = [
                'subject' => 'mySubject1',
                'body' => 'myBody',
                'email_address' => 'testing@email.com'
            ];

            $this->response = $this->postJson('api/1/send', [
                'api_token' => 'myApiToken',
                'emails' => [$this->email]
            ]);
        });

        afterEach(function () {
            Elasticsearch::indices()
                ->delete([
                    'index' => config('elasticsearch.default_index')
                ]);

            Cache::forget(config('app.redis_storage_key'));
        });

        it('should return successful response when valid request is sent', function () {
            $this->response->assertStatus(200);
        });

        it('should dispatch send email job', function () {
            Queue::assertPushed(SendEmailJob::class);
        });

        it('should store emails information in elastic search', function () {
            expect(Elasticsearch::get([
                'index' => config('elasticsearch.default_index'),
                'id'  => $this->email['email_address']
            ])['_source'])->toBe($this->email);
        });

        it('should store emails information in cache', function () {
            expect((new RedisHelper())->getMessages())->toBe([$this->email]);
        });
    });
});

describe('api/list', function () {
    it('should return successful response', function () {
        $response = $this->get('api/list');

        $response->assertStatus(200);
    });

    it('should return empty json when there is no cached email information', function () {
        $response = $this->getJson('api/list');

        $response->assertExactJson([]);
    });

    it('should return cached json when there is', function () {
        Queue::fake();

        $emails = [[
            'subject' => 'mySubject1',
            'body' => 'myBody',
            'email_address' => 'testing@email.com'
        ]];

        $this->postJson('api/1/send', [
            'api_token' => 'myApiToken',
            'emails' => $emails
        ]);

        $response = $this->getJson('api/list');

        $response->assertExactJson($emails);
    });
});