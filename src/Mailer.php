<?php

namespace Baseplus\Secdash;

class Mailer
{
    static public function init()
    {
        add_action('phpmailer_init', function($phpmailer) {
            /** @var \PHPMailer\PHPMailer\PHPMailer $phpmailer */
            if (null !== $recipient = self::getEmailRecipientFromCookie()) {
                $phpmailer->clearAllRecipients();
                $phpmailer->clearAddresses();

                $phpmailer->addAddress($recipient);
            }
        }, 99, 1);
    }

    static private function getEmailRecipientFromCookie()
    {
        if (false === isset($_COOKIE['BP_AUTO_FORMTEST_INTERNAL_MAIL_RECIPIENT'])) {
            return null;
        }

        $cookie = json_decode(stripslashes($_COOKIE['BP_AUTO_FORMTEST_INTERNAL_MAIL_RECIPIENT']), true);
        if (false === is_array($cookie)) {
            die();
        }

        $token = \Secdash::getOption('mailer_token', 'SECDASH_MAILER_SECRET');

        $checksum = hash('sha256', $cookie['recipient'] . date('dmY') . $token);
        if ($checksum !== $cookie['checksum']) {
            die();
        }

        return $cookie['recipient'];
    }
}
