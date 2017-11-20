<?php

namespace Framework\SendGrid\Test;

use Framework\Base\Test\UnitTest;
use Framework\SendGrid\SendGrid;
use Framework\Base\Application\Exception\ValidationException;
use Framework\Base\Mailer\Mail;

/**
 * Class SendGridTest
 * @package Framework\SendGrid\Test
 */
class SendGridTest extends UnitTest
{
    /**
     * Test SendGrid send email without recipient "to" field - exception
     */
    public function testSendGridSendFailedNoRecipientTo()
    {
        $sendGrid = new SendGrid(new DummySendGridClient());
        $mail = new Mail('', 'test@test.com', 'test', '<h1>test</h1>', 'test');

        $this->expectException(ValidationException::class);

        $sendGrid->send($mail);
    }

    /**
     * Test SendGrid send email without "from" field - exception
     */
    public function testSendGridSendFailedNoRecipientFrom()
    {
        $sendGrid = new SendGrid(new DummySendGridClient());
        $mail = new Mail('test@test.com', '', 'test', '<h1>test</h1>', 'test');

        $this->expectException(ValidationException::class);

        $sendGrid->send($mail);
    }

    /**
     * Test SendGrid send email without "subject" field - exception
     */
    public function testSendGridSendFailedNoRecipientSubject()
    {
        $sendGrid = new SendGrid(new DummySendGridClient());
        $mail = new Mail(
            'test@test.com',
            'test@test.com',
            '',
            '<h1>test</h1>',
            'test'
        );

        $this->expectException(ValidationException::class);

        $sendGrid->send($mail);
    }

    /**
     * Test SendGrid send email - no html or text body provided - exception
     */
    public function testSendGridSendFailedNoHtmlOrTextBody()
    {
        $sendGrid = new SendGrid(new DummySendGridClient());
        $mail = new Mail('test@test.com', 'test@test.com', 'test');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Text-plain or html body is required.');
        $this->expectExceptionCode(403);

        $sendGrid->send($mail);
    }

    /**
     * Test SendGrid send email - successful
     */
    public function testSendGridSendMailSuccessfully()
    {
        $sendGrid = new SendGrid(new DummySendGridClient());
        $mail = new Mail(
            'test@test.com',
            'test@test.com',
            'test',
            '<h1>test</h1>',
            'test'
        );

        $this::assertEquals(
            'Email was successfully sent!',
            $sendGrid->send($mail)
        );
    }
}
