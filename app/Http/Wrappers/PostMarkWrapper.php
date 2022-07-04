<?php

namespace App\Http\Wrappers;

use Postmark\PostmarkClient;
use Postmark\PostmarkAdminClient;
use Postmark\Models\PostmarkException;

class PostMarkWrapper
{
    public static function sendEmail()
    {
        try {
            $client = new PostmarkClient("<server token>");
            $fromEmail = "faizan.raza@saasfa.com"; //<sender signature>
            $toEmail = "faizan.raza@saasfa.com";
            $subject = "Hello from Postmark";
            $htmlBody = "<strong>Hello</strong> dear Postmark user.";
            $textBody = "Hello dear Postmark user.";
            $tag = "example-email-tag";
            $trackOpens = true;
            $trackLinks = "None";
            $messageStream = "outbound";

            // Send an email:
            $sendResult = $client->sendEmail(
                $fromEmail,
                $toEmail,
                $subject,
                $htmlBody,
                $textBody,
                $tag,
                $trackOpens,
                NULL, // Reply To
                NULL, // CC
                NULL, // BCC
                NULL, // Header array
                NULL, // Attachment array
                $trackLinks,
                NULL, // Metadata array
                $messageStream
            );

            // Getting the MessageID from the response
            echo $sendResult->MessageID;

        } catch (PostmarkException $ex) {
            // If the client is able to communicate with the API in a timely fashion,
            // but the message data is invalid, or there's a server error,
            // a PostmarkException can be thrown.
            echo $ex->httpStatusCode;
            echo $ex->message;
            echo $ex->postmarkApiErrorCode;

        } catch (\Exception $generalException) {
            // A general exception is thrown if the API
            // was unreachable or times out.
        }
    }
}