<?php

declare(strict_types=1);

namespace Baikal\Core;

use \Sabre\DAV;
use \Sabre\VObject\ITip;

/**
 * iMIP handler using Pear SMTP.
 *
 * This class is responsible for sending out iMIP messages. iMIP is the
 * email-based transport for iTIP. iTIP deals with scheduling operations for
 * iCalendar objects.
 *
 * If you want to customize the email that gets sent out, you can do so by
 * extending this class and overriding the sendMessage method.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Aisha Tammy <floss@bsd.ac>
 * @license http://sabre.io/license/ Modified BSD License
 */
class IMipSMTPPlugin extends \Sabre\DAV\ServerPlugin
{
    /**
     * Email address used in From: header.
     *
     * @var string
     */
    protected $senderEmail;

    /**
     * SMTP connection made by PEAR
     *
     */
     protected $smtp;

    /**
     * ITipMessage.
     *
     * @var ITip\Message
     */
    protected $itipMessage;

    /**
     * Creates the email handler.
     *
     * @param string $senderEmail. The 'senderEmail' is the email that shows up
     *                             in the 'From:' address. This should
     *                             generally be some kind of no-reply email
     *                             address you own.
     */
    public function __construct($senderEmail, $smtp_host = "", $smtp_port = "", $smtp_username = "", $smtp_password = "")
    {
        require_once "Mail.php";
        $this->senderEmail = $senderEmail;
        $this->smtp = \Mail::factory('smtp', array ('host' => $smtp_host, 'port' => $smtp_port, 'auth' => true, 'username' => $smtp_username, 'password' => $smtp_password));
    }

    /*
     * This initializes the plugin.
     *
     * This function is called by Sabre\DAV\Server, after
     * addPlugin is called.
     *
     * This method should set up the required event subscriptions.
     *
     * @param DAV\Server $server
     * @return void
     */
    public function initialize(DAV\Server $server)
    {
        $server->on('schedule', [$this, 'schedule'], 120);
    }

    /**
     * Returns a plugin name.
     *
     * Using this name other plugins will be able to access other plugins
     * using \Sabre\DAV\Server::getPlugin
     *
     * @return string
     */
    public function getPluginName()
    {
        return 'imip-smtp';
    }

    /**
     * Event handler for the 'schedule' event.
     */
    public function schedule(ITip\Message $iTipMessage)
    {
        // Not sending any emails if the system considers the update
        // insignificant.
        if (!$iTipMessage->significantChange) {
            if (!$iTipMessage->scheduleStatus) {
                $iTipMessage->scheduleStatus = '1.0;We got the message, but it\'s not significant enough to warrant an email';
            }

            return;
        }

        $summary = $iTipMessage->message->VEVENT->SUMMARY;

        if ('mailto' !== parse_url($iTipMessage->sender, PHP_URL_SCHEME)) {
            return;
        }

        if ('mailto' !== parse_url($iTipMessage->recipient, PHP_URL_SCHEME)) {
            return;
        }

        $sender = substr($iTipMessage->sender, 7);
        $recipient = substr($iTipMessage->recipient, 7);

        if ($iTipMessage->senderName) {
            $sender = $iTipMessage->senderName.' <'.$sender.'>';
        }
        if ($iTipMessage->recipientName && $iTipMessage->recipientName != $recipient) {
            $recipient = $iTipMessage->recipientName.' <'.$recipient.'>';
        }

        $subject = 'SabreDAV iTIP message';
        switch (strtoupper($iTipMessage->method)) {
            case 'REPLY':
                $subject = 'Re: '.$summary;
                break;
            case 'REQUEST':
                $subject = 'Invitation: '.$summary;
                break;
            case 'CANCEL':
                $subject = 'Cancelled: '.$summary;
                break;
        }

        $headers = array(
            'Reply-To' => $sender,
            'From' => $iTipMessage->senderName.' <'.$this->senderEmail.'>',
            'To' => $recipient,
            'Subject' => $subject,
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/calendar; charset=UTF-8; method='.$iTipMessage->method,
        );
        if (DAV\Server::$exposeVersion) {
            $headers += ['X-Sabre-Version' => DAV\Version::VERSION];
        }
        $this->mail(
            $recipient,
            $headers,
            $iTipMessage->message->serialize()
        );
        $iTipMessage->scheduleStatus = '1.1; Scheduling message is sent via iMip';
    }

    // @codeCoverageIgnoreStart
    // This is deemed untestable in a reasonable manner

    /**
     * This function is responsible for sending the actual email.
     *
     * @param string $to      Recipient email address
     * @param string $body    iCalendar body
     * @param array  $headers List of headers
     */
    protected function mail($to, array $headers, $body)
    {
        $mail = $this->smtp->send($to, $headers, $body);
        if (\PEAR::isError($mail))
            error_log($mail->getMessage());
        else
            error_log("Email successfully sent!");
    }

    // @codeCoverageIgnoreEnd

    /**
     * Returns a bunch of meta-data about the plugin.
     *
     * Providing this information is optional, and is mainly displayed by the
     * Browser plugin.
     *
     * The description key in the returned array may contain html and will not
     * be sanitized.
     *
     * @return array
     */
    public function getPluginInfo()
    {
        return [
            'name' => $this->getPluginName(),
            'description' => 'Email delivery (rfc6047) for CalDAV scheduling using Pear SMTP',
            'link' => 'http://sabre.io/dav/scheduling/',
        ];
    }
}
