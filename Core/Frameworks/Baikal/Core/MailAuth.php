<?php

namespace Baikal\Core;

/**
 * This is an authentication backend that uses a pop3, imap or smtp backend to authenticate user.
 *
 * @author Sascha Kuehndel (InuSasha) <dev@inusasha.de>
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class MailAuth extends AbstractExternalAuth {

    /**
     * Validates a username and password over ldap
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function validateUserPassExternal($username, $password) {

        /* build connection string */
        $cert = BAIKAL_DAV_MAIL_CHECK_CERT ? "/validate-cert" : "/novalidate-cert";
        $url = "";
        switch(BAIKAL_DAV_MAIL_PROTOCOL) {
            case "imap":    $url = "{".BAIKAL_DAV_MAIL_SERVER."/imap/notls}INBOX"; break;
            case "imaps":   $url = "{".BAIKAL_DAV_MAIL_SERVER."/imap/ssl${cert}}INBOX"; break;
            case "imaptls": $url = "{".BAIKAL_DAV_MAIL_SERVER."/imap/tls${cert}}INBOX"; break;
            case "pop3":    $url = "{".BAIKAL_DAV_MAIL_SERVER."/pop3/notls}"; break;
            case "pop3s":   $url = "{".BAIKAL_DAV_MAIL_SERVER."/pop3/ssl${cert}}"; break;
            case "pop3tls": $url = "{".BAIKAL_DAV_MAIL_SERVER."/pop3/tls${cert}}"; break;
            case "smtp":    $url = "{".BAIKAL_DAV_MAIL_SERVER."/smtp/notls}"; break;
            case "smtps":   $url = "{".BAIKAL_DAV_MAIL_SERVER."/smtp/ssl${cert}}"; break;
            case "smtptls": $url = "{".BAIKAL_DAV_MAIL_SERVER."/smtp/tls${cert}}"; break;
        }

        /* connect to mail server (only one try) */
        set_error_handler("\Baikal\Core\MailAuth::exception_error_handler");
        $conn = imap_open($url, $username, $password, NULL, 0);
        restore_error_handler();
        if (!$conn)
            return false;

        /* skip notices, warnings and errors */
        imap_errors();

        /* close */
        imap_close($conn);
        return true;
    }

    # WorkAround error_handler in failed login in imap_open
    public static function exception_error_handler($errno, $errstr, $errfile, $errline) {
    }
}
 
