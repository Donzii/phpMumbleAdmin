<?php

 /*
 * phpMumbleAdmin (PMA), web php administration tool for murmur (mumble server daemon).
 * Copyright (C) 2010 - 2015  Dadon David. PMA@ipnoz.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (! defined('PMA_STARTED')) { die('ILLEGAL: You cannot call this script directly !'); }

class PMA_mail
{
    private $host;
    private $port;
    private $defaultSenderEmail;
    private $smtpHelo;

    private $xmailer = 'phpMumbleAdmin';

    public $smtpDialogues = array();

    public $smtpError = false;
    public $smtpErrorMessage = '';

    private $from;
    private $subject;
    private $recipients = array();
    private $ToHeaders = array();
    private $CcHeaders = array();
    private $headers = array();
    private $message = array();

    function __construct()
    {
        $this->smtpHelo = $_SERVER['HTTP_HOST'];
        /**
        * Add commons headers
        *
        * Date ('r') = RFC 2822 formatted date
        * Date ('T') = Timezone abbreviation
        * example of output : Wed, 20 Feb 2013 16:51:20 +0100 (CET)
        */
        $curtime = time();
        $this->addHeader('Date: '.date('r', $curtime).' ('.date('T', $curtime).')');
        $this->addHeader('X-Mailer: '.$this->xmailer);
        $this->addHeader('MIME-Version: 1.0');
        $this->addHeader('Content-type: text/html; charset=utf-8');
    }

    /**
    * Set the SMTP host.
    */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
    * Set the SMTP port.
    */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
    * Set a custom xmailer.
    */
    public function setXmailer($mailer)
    {
        $this->xmailer = $mailer;
    }

    /**
    * Set the a default sender email.
    */
    public function setDefaultSender($default)
    {
        $this->defaultSenderEmail = $default;
    }

    /**
    * Set the email of the sender.
    */
    public function setFrom($string)
    {
        $this->from = $string;
    }

    /**
    * Add all Recipients (to, cc, bcc) here, for the "MAIL TO" smtp command.
    */
    private function addRecipient($email)
    {
        $this->recipients[] = $email;
    }

    /**
    * Add a To recipient.
    */
    public function addTo($email, $name = '')
    {
        $this->addRecipient($email);
        $this->ToHeaders[] = $this->getEnveloppe($email, $name);
    }

    /**
    * Add a Cc recipient.
    */
    public function addCc($email, $name = '')
    {
        $this->addRecipient($email);
        $this->CcHeaders[] = $this->getEnveloppe($email, $name);
    }

    /**
    * Add a Bcc recipient.
    * Memo: no need to show Bcc in the mail headers
    */
    public function addBcc($email, $name = '')
    {
        $this->addRecipient($email);
    }

    /**
    * Set the subject of the email.
    */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
    * Add customs headers.
    */
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
    * Add the message, line by line.
    */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
    * Return a formatted "name <email>" enveloppe
    */
    private function getEnveloppe($email, $name)
    {
        if (is_string($name) && $name !== '') {
            return $name.' <'.$email.'>';
        }
        return $email;
    }

    /**
    * Construct the email to send.
    *
    * @return array - array of the email.
    */
    private function getEmailBody()
    {
        $email = array();
        /**
        * Headers.
        */
        $email[] = 'Subject: '.$this->subject;
        $email[] = 'From: '.$this->from;
        /**
        * Headers : recipients.
        * Memo: no need to show Bcc.
        */
        if (! empty($this->ToHeaders)) {
            $email[] = 'to: '.join(', ', $this->ToHeaders);
        }
        if (! empty($this->CcHeaders)) {
            $email[] = 'cc: '.join(', ', $this->CcHeaders);
        }
        /**
        * Headers : customs.
        */
        foreach ($this->headers as $header) {
            $email[] = $header;
        }
        /**
        * End of headers, add a blank line.
        */
        $email[] = '';
        /**
        * Message
        */
        $email[] = $this->message;
        /**
        * End of the email.
        * DATA require to end with <CR><LF>.<CR><LF>
        */
        $email[] = '.';
        /**
        * Join and return.
        */
        return join(PHP_EOL, $email);
    }

    /**
    * Add all smtp dialogues to debug
    */
    private function getSmtpDialogues(array $array)
    {
        $this->smtpDialogues = $array;
    }

    private function smtpError($text)
    {
        $this->smtpError = true;
        $this->smtpErrorMessage = $text;
    }

    public function send_mail()
    {
        /**
        * Sanity.
        */
        if (empty($this->from)) {
            $this->from = $this->defaultSenderEmail;
        }

        /**
        * Setup the smtp object.
        */
        $smtp = new PMA_smtp($this->host, $this->port);
        /**
        * Connection to the SMTP server.
        */
        $smtp->connection();
        if ($smtp->getLastCode() !== 220) {
            $this->smtpError($smtp->getLastDialogue());
            if ($smtp->getLastCode() > 0) {
                $smtp->quit();
            }
            $this->getSmtpDialogues($smtp->dialogues);
            return;
        }
        /**
        * Send EHLO
        */
        $smtp->sendAndGetResponse('EHLO '.$this->smtpHelo);
        if ($smtp->getLastCode() === 250) {
            /**
            * get ESMTP options
            */
            foreach ($smtp->dialogues as $response) {
                if (substr($response->text, 4) === 'PIPELINING') {
                    $pipelining = true;
                    break;
                }
            }
        } else {
            /**
            * try HELO
            */
            $smtp->sendAndGetResponse('HELO '.$this->smtpHelo);
            if ($smtp->getLastCode() !== 250) {
                $this->smtpError($smtp->getLastDialogue());
                $this->getSmtpDialogues($smtp->dialogues);
                return;
            }
        }
        /**
        * PIPELINING
        */
        if (isset($pipelining)) {
            /**
            * Construct datas pipe
            */
            $pipe[] = 'MAIL FROM:<'.$this->from.'>';
            foreach ($this->recipients as $email) {
                $pipe[] = 'RCPT TO:<'.$email.'>';
            }
            $pipe[] = 'DATA';

            $smtp->sendAndGetResponse($pipe, true);
            /**
            * Check MAIL FROM response
            */
            if ($smtp->getLastCode() !== 250) {
                $this->smtpError($smtp->getLastDialogue());
                $smtp->quit();
                $this->getSmtpDialogues($smtp->dialogues);
                return;
            }
            /**
            * One valid recipient is required to send a mail
            */
            $one_valid_recipient = false;
            /**
            * Get responses for submitted recipients
            */
            foreach ($this->recipients as $email) {
                $smtp->getResponse(true);
                $code = $smtp->getLastCode();
                if ($code === 250 OR $code === 251) {
                    $one_valid_recipient = true;
                }
            }
            /**
            * Get DATA response
            */
            $smtp->getResponse(true);
            /**
            * RFC 2920, page 5 :
            * The server didn't found valid recipient but accepted the DATA command.
            */
            if ($smtp->getLastCode() === 354 && ! $one_valid_recipient) {
                $this->smtpError('No valid recipient found');
                $smtp->sendAndGetResponse('.');
                $smtp->quit();
                $this->getSmtpDialogues($smtp->dialogues);
                return;
            }
            /**
            * Some server like postfix refuse the DATA command and
            * return a 554 error code which is RFC complient too.
            */
            if ($smtp->getLastCode() !== 354) {
                $this->smtpError($smtp->getLastDialogue());
                $smtp->quit();
                $this->getSmtpDialogues($smtp->dialogues);
                return;
            }
        /**
        * CLASSIC SMTP
        */
        } else {
            /**
            *  MAIL FROM
            */
            $smtp->sendAndGetResponse('MAIL FROM:<'.$this->from.'>');
            if ($smtp->getLastCode() !== 250) {
                $this->smtpError($smtp->getLastDialogue());
                $smtp->quit();
                $this->getSmtpDialogues($smtp->dialogues);
                return;
            }
            /**
            *  One valid recipient is required to send a mail
            */
            $one_valid_recipient = false;
            /**
            *  Send RECIPIENTS
            */
            foreach ($this->recipients as $email) {
                $smtp->sendAndGetResponse('RCPT TO:<'.$email.'>');
                if ($smtp->getLastCode() === 250 OR $smtp->getLastCode() === 251) {
                    $one_valid_recipient = true;
                }
            }
            /**
            *  Check for one valid recipient
            */
            if (! $one_valid_recipient) {
                $this->smtpError('No valid recipient found');
                $smtp->quit();
                $this->getSmtpDialogues($smtp->dialogues);
                return;
            }
            /**
            *  Send DATA
            */
            $smtp->sendAndGetResponse('DATA');
            if ($smtp->getLastCode() !== 354) {
                $this->smtpError($smtp->getLastDialogue());
                $smtp->quit();
                $this->getSmtpDialogues($smtp->dialogues);
                return;
            }
        }
        /**
        * Send BODY of the email.
        */
        $smtp->sendAndGetResponse($this->getEmailBody());
        if ($smtp->getLastCode() !== 250) {
            $this->smtpError($smtp->getLastDialogue());
        }
        /**
        *Bye.
        */
        $smtp->quit();
        $this->getSmtpDialogues($smtp->dialogues);
    }
}
