<?php

use App\Mail\SendEmail;

describe('SendEmail', function() {
    it('should send the right content to the recipient', function () {
        $subject = 'mySubject';
        $body = 'myBody';

        $mailable = new SendEmail($subject, $body);

        $mailable->assertSeeInText($body);
    });
});