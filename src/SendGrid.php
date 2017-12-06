<?php

namespace Framework\SendGrid;

use Framework\Base\Application\Exception\ValidationException;
use Framework\Base\Mailer\Mailer;
use Framework\Base\Mailer\MailInterface;
use SendGrid\Attachment;
use SendGrid\Content;
use SendGrid\Email as EmailAddress;
use SendGrid\Mail as SendGridEmail;

/**
 * Class SendGrid
 * @package Framework\SendGrid
 */
class SendGrid extends Mailer
{
    /**
     * @param MailInterface $mail
     *
     * @return string
     * @throws ValidationException
     * @throws \Exception
     */
    public function send(MailInterface $mail)
    {
        $emailFrom = $mail->getFrom();
        $emailTo = $mail->getTo();
        $subject = $mail->getSubject();
        $htmlBody = $mail->getHtmlBody();
        $textBody = $mail->getTextBody();
        $options = $mail->getOptions();
        $attachments = $mail->getAttachments();

        $sg = $this->getClient();

        $from = new EmailAddress($emailFrom, $emailFrom);
        $to = new EmailAddress($emailTo, $emailTo);

        $body = [];

        if (empty($htmlBody) === false) {
            $body[] = new Content('text/html', $htmlBody);
        }

        if (empty($textBody) === false) {
            $body[] = new Content('text/plain', $textBody);
        }

        if (empty($body) === true) {
            throw new \Exception('Text-plain or html body is required.', 403);
        }

        $mail = new SendGridEmail($from, $subject, $to, $body[0]);

        if (isset($body[1]) === true) {
            $mail->addContent($body[1]);
        }

        if (isset($options['cc']) === true) {
            $mail->personalization[0]->addCc(['email' => $options['cc']]);
        }
        if (isset($options['bcc']) === true) {
            $mail->personalization[0]->addBcc(['email' => $options['bcc']]);
        }

        foreach ($attachments as $fileName => $content) {
            $attachment = new Attachment();
            $attachment->setFilename($fileName);
            $attachment->setContent(base64_encode($content));
            $attachment->setDisposition("attachment");
            $mail->addAttachment($attachment);
        }

        $response = $sg->client->mail()->send()->post($mail);

        $responseMsg = 'Email was successfully sent!';
        $errors = json_decode($response->body());

        if ($errors) {
            $errorMessages = [];
            foreach ($errors->errors as $error) {
                $errorMessages[$error->field] = $error->message;
            }
            $exception = new ValidationException();
            $exception->setFailedValidations($errorMessages);
            throw $exception;
        }

        return $responseMsg;
    }
}
