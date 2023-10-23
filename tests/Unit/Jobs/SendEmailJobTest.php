<?php

use App\Jobs\Email\SendEmailJob;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

describe('SendEmailJob', function () {
    it('should send multiple email to given recipients', function () {
        Mail::fake();

        $emails = [
            [
                'subject' => 'mySubject1',
                'body' => 'myBody1',
                'email_address' => 'testing1@email.com'
            ],
            [
                'subject' => 'mySubject2',
                'body' => 'myBod2',
                'email_address' => 'testing2@email.com'
            ]
        ];

        (new SendEmailJob($emails))->handle();

        Mail::assertQueued(SendEmail::class);

        Mail::assertQueued(SendEmail::class, 2);
    });

    it('should send multiple email to given recipients with the right details', function () {
        Mail::fake();

        $emails = [
            [
                'subject' => 'mySubject1',
                'body' => 'myBody1',
                'email_address' => 'testing1@email.com'
            ],
            [
                'subject' => 'mySubject2',
                'body' => 'myBod2',
                'email_address' => 'testing2@email.com'
            ]
        ];

        (new SendEmailJob($emails))->handle();

       
        Mail::assertQueued(SendEmail::class, function ($mail) use ($emails) {
            return $mail->hasTo($emails[0]['email_address']) &&
                   $mail->hasSubject($emails[0]['subject']);
        });

        Mail::assertQueued(SendEmail::class, function ($mail) use ($emails) {
            return $mail->hasTo($emails[1]['email_address']) &&
                   $mail->hasSubject($emails[1]['subject']);
        });
    });
});
