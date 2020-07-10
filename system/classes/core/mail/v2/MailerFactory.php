<?php

namespace core\mail\v2;

use core\utils\XWServerInstanceToolKit;
use Swift_Mailer;
use Swift_SendmailTransport;
use Swift_SmtpTransport;

class MailerFactory
{
    private static function getConfig()
    {
        $result = [];
        $folder = XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath();
        if (is_file($folder . "/mail-config.json")) {
            $result = json_decode(file_get_contents($folder . "/mail-config.json"), true);
        }
        return $result;
    }

    public static function getMailer(): Swift_Mailer
    {
        $config = self::getConfig();
        if(!isset($config['type'])) {
            $config['type'] = isset($config['host']) ? 'smtp' : '';
        }

        $transport = null;
        switch ($config['type']) {
            case 'smtp':
                $transport = new Swift_SmtpTransport($config['host'], $config['port'] ?? 25,
                    $config['encryption'] ?? null);
                if (isset($config['user']) && strlen(trim($config['user'])) > 0) {
                    $transport->setUsername($config['user']);
                }
                if (isset($config['password']) && strlen(trim($config['password'])) > 0) {
                    $transport->setPassword($config['password']);
                }
                break;
            case 'sendmail':
                $transport = new Swift_SendmailTransport('/usr/sbin/sendmail -bs');
                break;
            case 'exim':
                $transport = new Swift_SendmailTransport('/usr/sbin/exim -bs');
                break;
            case 'qmail':
                $transport = new Swift_SendmailTransport('/usr/sbin/qmail -bs');
                break;
            case 'postfix':
                $transport = new Swift_SendmailTransport('/usr/sbin/postfix -bs');
                break;
            case 'mail':
            default:
                $transport = null;
        }

        return new Swift_Mailer($transport);
    }
}
