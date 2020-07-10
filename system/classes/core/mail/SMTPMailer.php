<?php

namespace core\mail;

use core\mail\v2\MailerFactory;
use PHPMailer;
use phpmailerException;
use Swift_Message;

class SMTPMailer
{
    private $host = '';
    private $port = 25;
    private $user = '';
    private $password = '';

    private $useSwift = true;

    public function __construct(string $host, int $port = 26, ?string $user = '', ?string $password = '')
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user ?? '';
        $this->password = $password ?? '';
    }

    public function isValid()
    {
        return strlen($this->host) > 0 && strlen($this->user) > 0;
    }

    public function send(string $from, $fromClearName = null, array $to = [], string $subject, string $text = '', bool $isHtml = false, $fallbackText = null, $bcc = null)
    {
        if ($this->useSwift) {
            $this->sendSwift($from, $fromClearName, $to, $subject, $text, $isHtml, $fallbackText, $bcc);
        } else {
            $this->sendPHPMailer($from, $fromClearName, $to, $subject, $text, $isHtml, $fallbackText, $bcc);
        }
    }

    private function sendSwift(string $from, $fromClearName = null, array $to = [], string $subject, string $text = '', bool $isHtml = false, $fallbackText = null, $bcc = null)
    {
        $mailer = MailerFactory::getMailer();
        $mail = new Swift_Message($subject);

        if ($fromClearName) {
            $mail->setFrom([$from => $fromClearName]);
        } else {
            $mail->setFrom($from);
        }

        $mail->setTo($to);
        if($bcc) {
            $mail->setBcc($bcc);
        }
        $mail->setBody($text);
        if ($isHtml) {
            $mail->setContentType('text/html');
            $mail->addPart($fallbackText, 'text/plain');
        }

        $mailer->send($mail);
    }

    /**
     * @param string $from
     * @param null $fromClearName
     * @param array $to
     * @param string $subject
     * @param string $text
     * @param bool $isHtml
     * @param null $fallbackText
     *
     * @throws phpmailerException
     */
    private function sendPHPMailer(string $from, $fromClearName = null, array $to = [], string $subject, string $text = '', bool $isHtml = false, $fallbackText = null, $bcc = null)
    {
        $mail = new PHPMailer();
        //$text = preg_replace("/\\/", "", $text);
        $mail->IsSMTP();

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->SMTPAuth = true;
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        if ($this->user && $this->user != '') {
            $mail->Username = $this->user;
            $mail->Password = $this->password;
        }

        $mail->SetFrom($from, $fromClearName);
        $mail->Subject = $subject;

        $mail->Body = $text;
        if ($isHtml) {
            $mail->isHTML(true);
            if ($fallbackText) {
                $mail->AltBody = $fallbackText;
            }
        }

        foreach ($to as $name => $address) {
            $mail->AddAddress($address, !preg_match("/^\d+$/", $name) ? $name : null);
        }
        if($bcc) {
            foreach ($bcc as $name => $address) {
                $mail->addBCC($address, !preg_match("/^\d+$/", $name) ? $name : null);
            }
        }

        $mail->Send();
    }
}
