<?php

declare (strict_types=1);

namespace mark\mail;

/**
 * Class Mail
 *
 * @package mark\mail
 */
class Mail {

    private $emailto;
    private $smtpaddr;
    private $mailtitle;
    private $mailbody;
    private $smtphost;
    private $smtpport;
    private $smtpuser;
    private $smtppass;
    private $mailtype;
    private $debug;
    private $time_out;
    private $host_name;
    private $log_file;
    private $auth;
    private $sock;

    // $smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
    // function smtp($relay_host =	"",	$smtp_port = 25,$auth =	false,$user,$pass){

    public function __construct(
        $emailto, $smtpaddr = null, $mailtitle = null, $mailbody = null, $smtphost = null, $smtpport = null, $smtpuser = null,
        $smtppass = null, $mailtype = null, $debug = null
    ) {
        $this->emailto = $emailto;                                                                            //	SMTP服务器的收件人
        empty($smtpaddr) ? $this->smtpaddr = $GLOBALS['smtp']['smtpaddr'] : $this->smtpaddr = $smtpaddr;    //	SMTP服务器的发件人
        empty($mailtitle) ? $this->mailtitle = $GLOBALS['smtp']['mailtitle'] : $this->mailtitle = $mailtitle;    //	发送邮件的主题
        empty($mailbody) ? $this->mailbody = $GLOBALS['smtp']['mailbody'] : $this->mailbody = $mailbody;    //	发送邮件的内容
        empty($smtphost) ? $this->smtphost = $GLOBALS['smtp']['smtphost'] : $this->smtphost = $smtphost;    //	SMTP服务器地址
        empty($smtpport) ? $this->smtpport = $GLOBALS['smtp']['smtpport'] : $this->smtpport = $smtpport;    //	SMTP服务器端口
        empty($smtpuser) ? $this->smtpuser = $GLOBALS['smtp']['smtpuser'] : $this->smtpuser = $smtpuser;    //	SMTP服务器的用户帐号
        empty($smtppass) ? $this->smtppass = $GLOBALS['smtp']['smtppass'] : $this->smtppass = $smtppass;    //	SMTP服务器的用户密码
        empty($mailtype) ? $this->mailtype = $GLOBALS['smtp']['mailtype'] : $this->mailtype = $mailtype;    //	邮件格式（HTML/TXT）
        empty($debug) ? $this->debug = $GLOBALS['smtp']['debug'] : $this->debug = $debug;                    //	是否显示发送的调试信息
        $this->time_out = $GLOBALS['smtp']['time_out'];        //is used	in fsockopen()
        $this->auth = $GLOBALS['smtp']['auth'];                //auth
        $this->host_name = 'localhost';                        //is used in HELO command
        $this->log_file = '';
        $this->sock = $GLOBALS['smtp']['sock'];

        // return $this->sendmail($this->emailto, $this->smtpaddr, $this->mailtitle, $this->mailbody, $this->mailtype);
    }

    /** Main    Function
     *
     * @param        $to
     * @param        $from
     * @param string $subject
     * @param string $body
     * @param        $mailtype
     * @param string $cc
     * @param string $bcc
     * @param string $additional_headers
     *
     * @return bool
     */
    public function sendmail($to, $from, $subject = '', $body = '', $mailtype, $cc = '', $bcc = '', $additional_headers = '') {
        $mail_from = $this->get_address($this->strip_comment($from));
        $body = preg_replace("/(^|(\r\n))(\\.)/", "\\1.\\3", $body);
        $header = "MIME-Version:1.0\r\n";
        if ($mailtype === 'HTML') {
            $header .= "Content-Type:text/html\r\n";
        }
        $header .= 'To:	' . $to . "\r\n";
        if ($cc != '') {
            $header .= 'Cc:	' . $cc . "\r\n";
        }
        $header .= "From: $from<" . $from . ">\r\n";
        $header .= 'Subject: ' . $subject . "\r\n";
        $header .= $additional_headers;
        $header .= 'Date: ' . date('r') . "\r\n";
        $header .= 'X-Mailer:By	Redhat (PHP/' . PHP_VERSION . ")\r\n";
        [$msec, $sec] = explode(' ', microtime());
        $header .= 'Message-ID:	<' . date('YmdHis', $sec) . '.' . ($msec * 1000000) . '.' . $mail_from . ">\r\n";
        $TO = explode(',', $this->strip_comment($to));

        if ($cc != '') {
            $TO = array_merge($TO, explode(',', $this->strip_comment($cc)));
        }

        if ($bcc != '') {
            $TO = array_merge($TO, explode(',', $this->strip_comment($bcc)));
        }

        $sent = true;
        foreach ($TO as $rcpt_to) {
            $rcpt_to = $this->get_address($rcpt_to);
            if (!$this->smtp_sockopen($rcpt_to)) {
                $this->log_write('Error: Cannot	send email to ' . $rcpt_to . "\n");
                $sent = false;
                continue;
            }
            if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header, $body)) {
                $this->log_write('E-mail has been sent to <' . $rcpt_to . ">\n");
            } else {
                $this->log_write('Error: Cannot	send email to <' . $rcpt_to . ">\n");
                $sent = false;
            }
            fclose($this->sock);
            $this->log_write("Disconnected from	remote host\n");
        }
        // echo "<br>";
        // echo $header;
        return $sent;
    }

    /**
     * @param        $helo
     * @param        $from
     * @param        $to
     * @param        $header
     * @param string $body
     *
     * @return bool
     */
    private function smtp_send($helo, $from, $to, $header, $body = '') {
        if (!$this->smtp_putcmd('HELO', $helo)) {
            return $this->smtp_error('sending HELO command');
        }
        #auth
        if ($this->auth) {
            if (!$this->smtp_putcmd('AUTH LOGIN', base64_encode($this->smtpuser))) {
                return $this->smtp_error('sending HELO command');
            }
            if (!$this->smtp_putcmd('', base64_encode($this->smtppass))) {
                return $this->smtp_error('sending HELO command');
            }
        }

        if (!$this->smtp_putcmd('MAIL', 'FROM:<' . $from . '>')) {
            return $this->smtp_error('sending MAIL FROM	command');
        }

        if (!$this->smtp_putcmd('RCPT', 'TO:<' . $to . '>')) {
            return $this->smtp_error('sending RCPT TO command');
        }

        if (!$this->smtp_putcmd('DATA')) {
            return $this->smtp_error('sending DATA command');
        }

        if (!$this->smtp_message($header, $body)) {
            return $this->smtp_error('sending message');
        }

        if (!$this->smtp_eom()) {
            return $this->smtp_error('sending <CR><LF>.<CR><LF>	[EOM]');
        }

        if (!$this->smtp_putcmd('QUIT')) {
            return $this->smtp_error('sending QUIT command');
        }

        return true;
    }

    public function smtp_sockopen($address) {
        if ($this->smtphost == '') {
            return $this->smtp_sockopen_mx($address);
        }

        return $this->smtp_sockopen_relay();
    }

    public function smtp_sockopen_relay() {
        $this->log_write('Trying to	' . $this->smtphost . ':' . $this->smtpport . "\n");
        $this->sock = @fsockopen($this->smtphost, $this->smtpport, $errno, $errstr, $this->time_out);
        if (!($this->sock && $this->smtp_ok())) {
            $this->log_write('Error: Cannot	connenct to	relay host ' . $this->smtphost . "\n");
            $this->log_write('Error: ' . $errstr . ' (' . $errno . ")\n");

            return false;
        }
        $this->log_write('Connected	to relay host ' . $this->smtphost . "\n");

        return true;
    }

    public function smtp_sockopen_mx($address) {
        $domain = preg_replace('/^.+@([^@]+)$/', "\\1", $address);
        if (!@getmxrr($domain, $MXHOSTS)) {
            $this->log_write('Error: Cannot	resolve	MX "' . $domain . "\"\n");

            return false;
        }
        foreach ($MXHOSTS as $host) {
            $this->log_write('Trying to	' . $host . ':' . $this->smtpport . "\n");
            $this->sock = @fsockopen($host, $this->smtpport, $errno, $errstr, $this->time_out);
            if (!($this->sock && $this->smtp_ok())) {
                $this->log_write('Warning: Cannot connect to mx	host ' . $host . "\n");
                $this->log_write('Error: ' . $errstr . ' (' . $errno . ")\n");
                continue;
            }
            $this->log_write('Connected	to mx host ' . $host . "\n");

            return true;
        }
        $this->log_write('Error: Cannot	connect	to any mx hosts	(' . implode(', ', $MXHOSTS) . ")\n");

        return false;
    }

    public function smtp_message($header, $body) {
        fwrite($this->sock, $header . "\r\n" . $body);
        $this->smtp_debug('> ' . str_replace("\r\n", "\n" . '> ', $header . "\n> " . $body . "\n>	"));

        return true;
    }

    public function smtp_eom() {
        fwrite($this->sock, "\r\n.\r\n");
        $this->smtp_debug(". [EOM]\n");

        return $this->smtp_ok();
    }

    public function smtp_ok() {
        $response = str_replace("\r\n", '', fgets($this->sock, 512));
        $this->smtp_debug($response . "\n");

        if (!preg_match('/^[23]/', $response)) {
            fwrite($this->sock, "QUIT\r\n");
            fgets($this->sock, 512);
            $this->log_write('Error: Remote	host returned "' . $response . "\"\n");

            return false;
        }

        return true;
    }

    public function smtp_putcmd($cmd, $arg = '') {
        if ($arg != '') {
            if ($cmd == '') {
                $cmd = $arg;
            } else {
                $cmd = $cmd . ' ' . $arg;
            }
        }

        fwrite($this->sock, $cmd . "\r\n");
        $this->smtp_debug('> ' . $cmd . "\n");

        return $this->smtp_ok();
    }

    public function smtp_error($string) {
        $this->log_write('Error: Error occurred	while ' . $string . ".\n");

        return false;
    }

    public function log_write($message) {
        $this->smtp_debug($message);

        if ($this->log_file == '') {
            return true;
        }

        $message = date('M d H:i:s ') . get_current_user() . '[' . getmypid() . ']:	' . $message;
        if (!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, 'ab'))) {
            $this->smtp_debug('Warning:	Cannot open	log	file "' . $this->log_file . "\"\n");

            return false;
        }
        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        fclose($fp);

        return true;
    }

    public function strip_comment($address) {
        $comment = "/\\([^()]*\\)/";
        while (preg_match($comment, $address)) {
            $address = preg_replace($comment, '', $address);
        }

        return $address;
    }

    public function get_address($address) {
        $address = preg_replace("/([ \t\r\n])+/", '', $address);
        $address = preg_replace('/^.*<(.+)>.*$/', "\\1", $address);

        return $address;
    }

    public function smtp_debug($message) {
        if ($this->debug) {
            echo $message . '<br>';
        }
    }

    public function get_attach_type($image_tag) {
        $filedata = array();
        $img_file_con = fopen($image_tag, 'rb');
        $image_data = null;
        while ($tem_buffer = AddSlashes(fread($img_file_con, filesize($image_tag)))) {
            $image_data .= $tem_buffer;
        }
        fclose($img_file_con);

        $filedata['context'] = $image_data;
        $filedata['filename'] = basename($image_tag);
        $extension = substr($image_tag, strrpos($image_tag, '.'));
        switch ($extension) {
            case '.gif':
                $filedata['type'] = 'image/gif';
                break;
            case '.gz':
                $filedata['type'] = 'application/x-gzip';
                break;
            case '.htm':
            case '.html':
                $filedata['type'] = 'text/html';
                break;
            case '.jpg':
                $filedata['type'] = 'image/jpeg';
                break;
            case '.tar':
                $filedata['type'] = 'application/x-tar';
                break;
            case '.txt':
                $filedata['type'] = 'text/plain';
                break;
            case '.zip':
                $filedata['type'] = 'application/zip';
                break;
            default:
                $filedata['type'] = 'application/octet-stream';
                break;
        }

        return $filedata;
    }

}