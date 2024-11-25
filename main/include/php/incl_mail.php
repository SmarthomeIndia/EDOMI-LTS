<?
class class_email
{
    public $timeout = 30;
    public $mail_to = '';
    public $mail_subject = '';
    public $mail_body = '';
    public $mail_from = '';
    private $Socket = false;
    private $Domain = 'localhost';
    private $Host = '';
    private $Port = '';
    private $Login = '';
    private $Pass = '';
    private $Auth = '';

    public function __construct()
    {
        $this->Host = strtolower(trim(global_mailHost));
        $this->Port = global_mailPort;
        $this->Login = trim(global_mailLogin);
        $this->Pass = trim(global_mailPassword);
        $this->Auth = strtolower(trim(global_mailSecure));
        $this->mail_from = trim(global_mailFromAdr);
    }

    public function sendEmail()
    {
        if (global_emailGatewayActive && !isEmpty(global_mailHost) && !isEmpty(global_mailPort)) {
            if ($this->com_connect()) {
                $this->mailSendRequest("EHLO " . $this->Domain);
                $this->mailGetResponse("SEND HELLO MESSAGE");
                if ($this->com_login()) {
                    return $this->com_sendmail();
                }
            }
        }
        return false;
    }

    private function com_connect()
    {
        if ($this->Auth == 'ssl') {
            if (extension_loaded('openssl')) {
                $this->Host = 'ssl://' . $this->Host;
            } else {
                writeToLog(-1, false, 'EMAIL-error: SSL-connection not possible (openssl-Extension missing)');
                return false;
            }
        }
        if ($this->Socket = fsockopen($this->Host, $this->Port, $errno, $errstr, $this->timeout)) {
            if (substr($this->mailGetResponse("fsockopen"), 0, 3) != '220') {
                writeToLog(-1, false, 'EMAIL-error: connection rejected from host.');
                fclose($this->Socket);
                return false;
            }
        } else {
            writeToLog(-1, false, 'EMAIL-error: socket cannot be opened.');
            return false;
        }
        return true;
    }

    private function com_login()
    {
        if ($this->Auth == 'tls') {
            if (extension_loaded('openssl')) {
                $this->mailSendRequest("STARTTLS");
                if (substr($this->mailGetResponse("STARTTLS"), 0, 3) != '220') {
                    writeToLog(-1, false, 'EMAIL-error: TLS-connection rejected from host.');
                    $this->mailReset();
                    return false;
                }
                stream_socket_enable_crypto($this->Socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $this->mailSendRequest("EHLO " . $this->Domain);
                if (substr($this->mailGetResponse("SEND HELLO MESSAGE"), 0, 3) != '250') {
                    writeToLog(-1, false, 'EMAIL-error: TLS-connection rejected from host. TLS failed.');
                    $this->mailReset();
                    return false;
                }
                $this->mailSendRequest("AUTH LOGIN");
                if (substr($this->mailGetResponse("AUTH LOGIN"), 0, 3) != '334') {
                    writeToLog(-1, false, 'EMAIL-error: connection rejected from host. authentication failed.');
                    $this->mailReset();
                    return false;
                }
                $this->mailSendRequest(base64_encode($this->Login));
                if (substr($this->mailGetResponse("AUTH Username"), 0, 3) != '334') {
                    writeToLog(-1, false, 'EMAIL-error: connection rejected from host. authentication failed.');
                    $this->mailReset();
                    return false;
                }
                $this->mailSendRequest(base64_encode($this->Pass));
                if (substr($this->mailGetResponse("AUTH Password"), 0, 3) != '235') {
                    writeToLog(-1, false, 'EMAIL-error: connection rejected from host. authentication failed.');
                    $this->mailReset();
                    return false;
                }
            } else {
                writeToLog(-1, false, 'EMAIL-error: TLS connection not possible (missing openssl-extension)');
                $this->mailReset();
                return false;
            }
        }
        return true;
    }

    private function com_sendmail()
    {
        $mailId = md5(date('r', time()));
        $this->mailSendRequest('MAIL FROM: <' . $this->mail_from . '>');
        $this->mailGetResponse('SET MAIL FROM: <' . $this->mail_from . '>');
        $this->mailSendRequest('RCPT TO: <' . $this->mail_to . '>');
        $this->mailGetResponse('SET RECIPIENT TO: <' . $this->mail_to . '>');
        $this->mail_subject = trim($this->mail_subject);
        $this->mail_body = trim($this->mail_body);
        $body = 'Date: ' . date('r') . "\r\n";
        $body .= 'From: ' . $this->mail_from . "\r\n";
        $body .= 'Reply-To: ' . $this->mail_from . "\r\n";
        $body .= 'To: ' . $this->mail_to . "\r\n";
        $body .= 'Subject: ' . $this->mail_subject . "\r\n";
        $body .= "MIME-Version: 1.0\r\n";
        $body .= "Content-Type: multipart/alternative; boundary=\"PHP-alt-" . $mailId . "\"\r\n";
        $body .= "\r\n--PHP-alt-" . $mailId . "\r\n";
        $body .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n";
        $body .= "\r\n" . $this->mail_body . "\r\n";
        $body .= "\r\n--PHP-alt-" . $mailId . "--\r\n";
        $body .= ".\r\n";
        $this->mailSendRequest('DATA');
        $this->mailGetResponse('DATA - CHECK');
        $this->mailSendRequest($body);
        $response = $this->mailGetResponse('DATA - Send');
        if (substr($response, 0, 3) != '250') {
            writeToLog(-1, false, 'EMAIL-error: email could not be send (Reply-Code: ' . substr($response, 0, 3) . ' / Message-ID=' . $mailId . ').');
            $this->mailDisconnect();
            return false;
        }
        $this->mailDisconnect();
        return true;
    }

    private function mailGetResponse($request)
    {
        $request = strtoupper($request);
        $n = '';
        $t = getMicrotime();
        while (($data = fgets($this->Socket)) && ((getMicrotime() - $t) < $this->timeout)) {
            $n .= $data;
            if (substr($data, 3, 1) == ' ') {
                break;
            }
            usleep(100);
        }
        if (isEmpty($n)) {
            $n = "Error Response\r\n";
        }
        return $n;
    }

    private function mailSendRequest($request)
    {
        fputs($this->Socket, $request . "\r\n");
    }

    private function mailReset()
    {
        $this->mailSendRequest('RSET');
        $this->mailGetResponse('RESET');
        $this->mailDisconnect();
    }

    private function mailDisconnect()
    {
        $this->mailSendRequest('QUIT');
        $this->mailGetResponse('QUIT');
        fclose($this->Socket);
    }
}

function sendMail($to, $subject, $body)
{
    $cMail = new class_email();
    if (isEmpty($to)) {
        $to = global_mailDefaultToAdr;
    }
    $cMail->mail_to = $to;
    $cMail->mail_subject = $subject;
    $cMail->mail_body = $body;
    return $cMail->sendEmail();
    $cMail = null;
}
?>