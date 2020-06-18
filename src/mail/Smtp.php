<?php

declare (strict_types=1);

namespace mark\mail;

/**
 * 通过 SOCKET 连接 SMTP 服务器发送(支持 ESMTP 验证) 2.0
 *
 * 1、全部参数设置Set，Get方法
 * 2、设置时使用Set，调用时使用Get方法
 * 3、参数设置完毕后手动提交发送
 *
 * 4、TODO：get、set默认值还需完善，邮件日志，如何处理？
 *
 * @author  Mark
 * @version 2.0
 * @time    20190806 190000
 *
 * <pre>
 *
 * $smtp = new smtp($GLOBALS['smtp']['smtphost'], $GLOBALS['smtp']['smtpport'], $GLOBALS['smtp']['smtpuser'], $GLOBALS['smtp']['smtppass']);
 * $smtp->setReceiver(someone@example.com);
 * $smtp->setSender($GLOBALS['smtp']['sender']);
 * $smtp->setTitle("this is a email title");
 * $smtp->setBody("this is a email body");
 *
 * if($smtp->send()){
 * return true;
 * }else{
 * return $smtp->getMsg();
 * }
 *
 * </pre>
 *
 **/
class Smtp {

    private $sock;
    private $msg;

    private $smtp_config = array(
        'smtphost' => '',
        'smtpport' => '',
        'smtpuser' => '',
        'smtppass' => '',

        // 收件人
        'receiver' => array(),

        'title' => '',
        'body' => '',

        // 发件人
        'smtpaddr' => '',
        'sender' => '',

        'type' => '',
        'debug' => false,
        'timeout' => '30',
        'hostname' => 'localhost',
        'log_file' => '',
        'auth' => true,
        'sock' => '');

    /**
     * 初始化系统参数
     * Smtp constructor.
     *
     * @param null $smtphost
     * @param null $smtpport
     * @param null $smtpuser
     * @param null $smtppass
     */
    public function __construct($smtphost = null, $smtpport = null, $smtpuser = null, $smtppass = null) {
        $this->setSmtpHost($smtphost);
        $this->setSmtpPort($smtpport);
        $this->setSmtpUser($smtpuser);
        $this->setSmtpPass($smtppass);
    }

    public function __set($key, $value) {
        $key = strtolower(substr($key, 3));
        if (array_key_exists($key, $this->smtp_config)) {
            if (!empty($key) && !empty($value)) {
                $this->smtp_config[$key] = $value;
            }
        }

        return $this;
    }

    public function __get($key) {
        $key = strtolower(substr($key, 3));
        if (array_key_exists($key, $this->smtp_config) && isset($this->smtp_config[$key])) {
            return $this->smtp_config[$key];
        }

        return null;
    }

    /**
     * 连贯操作调用 getHost,setHost,为邮件参数赋值，默认还需完善
     *
     * @param $method
     * @param $args
     *
     * @return $this|bool|mixed|null
     */
    public function __call($method, $args) {
        $key = strtolower(preg_replace('/(.)([A-Z])/', '$1$2', $method));
        switch (strtolower(substr($method, 0, 3))) {
            case 'get' :
                return $this->__get($key, $args[0] ?? null);
            case 'set' :
                return $this->__set($key, $args[0] ?? null);
            case 'uns' :
                return $this->unsetData($key);
            case 'has' :
                return isset($this->_data[$key]);
            default:
                $method = strtolower($method);
                if (array_key_exists($method, $this->smtp_config)) {
                    if (empty($args[0]) || (is_string($args[0]) && trim($args[0]) === '')) {
                        $this->smtp_config[$method] = '';
                    } else {
                        $this->smtp_config[$method] = $args;
                    }

                    if ($method === 'limit') {
                        if ($args[0] == '0') {
                            $this->smtp_config[$method] = $args;
                        }
                    }
                } else {
                    $this->log_write("调用类" . get_class($this) . "中的方法{$method}()不存在!");
                }
                break;
        }

        return $this;
    }

    /**
     *  发送邮件
     *
     * @return bool
     */
    public function send() {
        if (!$this->validate()) {
            return false;
        }

        return $this->sendmail($this->getReceiver(), $this->getSender(), $this->getTitle(), $this->getBody(), $this->getType());
    }

    /**
     * 数据参数校验
     *
     * @return bool
     */
    public function validate() {
        if (!$this->getSmtpHost()) {
            $this->setMsg('无效的邮件服务器地址');

            return false;
        }

        if (!$this->getSmtpPort()) {
            $this->setMsg('无效的邮件服务器端口');

            return false;
        }
        if (!$this->getSmtpUser()) {
            $this->setMsg('无效的邮件服务器用户名');

            return false;
        }

        if (!$this->getSmtpPass()) {
            $this->setMsg('无效的邮件服务器密码');

            return false;
        }

        if (!$this->getReceiver() && !$this->getAddressee()) {
            $this->setMsg('无效的收件人地址');

            return false;
        }

        if (!$this->getSender()) {
            $this->setMsg('无效的发件人地址');

            return false;
        }

        if (!$this->getTitle()) {
            $this->setTitle('（无主题）');
        }
        if (!$this->getType()) {
            //  $this->setType("text");
        }

        return true;
    }

    public function setMsg($msg = '') {
        $this->msg[] = $msg;
    }

    public function getMsg() {
        return $this->msg;
    }

    public function getConfig() {
        return $this->smtp_config;
    }

    /** Main Function
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
        $header .= 'To: ' . $to . "\r\n";
        if ($cc != '') {
            $header .= 'Cc: ' . $cc . "\r\n";
        }
        $header .= "From: $from<" . $from . ">\r\n";
        $header .= 'Subject: ' . $subject . "\r\n";
        $header .= $additional_headers;
        $header .= 'Date: ' . date('r') . "\r\n";
        $header .= 'X-Mailer:By Redhat (PHP/' . PHP_VERSION . ")\r\n";
        [$msec, $sec] = explode(' ', microtime());
        $header .= 'Message-ID: <' . date('YmdHis', $sec) . '.' . ($msec * 1000000) . '.' . $mail_from . ">\r\n";
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
                $this->log_write('Error: Cannot send email to ' . $rcpt_to . "\n");
                $sent = false;
                continue;
            }
            if ($this->smtp_send($this->getHostName(), $mail_from, $rcpt_to, $header, $body)) {
                $this->log_write('E-mail has been sent to <' . $rcpt_to . ">\n");
            } else {
                $this->log_write('Error: Cannot send email to <' . $rcpt_to . ">\n");
                $sent = false;
            }
            fclose($this->sock);
            $this->log_write("Disconnected from remote host\n");
        }

        return $sent;
    }

    private function smtp_send($helo, $from, $to, $header, $body = '') {
        if (!$this->smtp_putcmd('HELO', $helo)) {
            return $this->smtp_error('sending HELO command');
        }

        if ($this->getAuth()) {
            if (!$this->smtp_putcmd('AUTH LOGIN', base64_encode($this->getSmtpUser()))) {
                return $this->smtp_error('sending HELO command');
            }
            if (!$this->smtp_putcmd('', base64_encode($this->getSmtpPass()))) {
                return $this->smtp_error('sending HELO command');
            }
        }

        if (!$this->smtp_putcmd('MAIL', 'FROM:<' . $from . '>')) {
            return $this->smtp_error('sending MAIL FROM command');
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
            return $this->smtp_error('sending <CR><LF>.<CR><LF> [EOM]');
        }

        if (!$this->smtp_putcmd('QUIT')) {
            return $this->smtp_error('sending QUIT command');
        }

        return true;
    }

    public function smtp_sockopen($address) {
        if ($this->getSmtpHost() == '') {
            return $this->smtp_sockopen_mx($address);
        }

        return $this->smtp_sockopen_relay();
    }

    public function smtp_sockopen_relay() {
        $this->log_write('Trying to ' . $this->getSmtpHost() . ':' . $this->getSmtpPort() . "\n");
        $this->sock = @fsockopen($this->getSmtpHost(), $this->getSmtpPort(), $errno, $errstr, $this->getTimeOut());
        stream_set_blocking($this->sock, true);
        if (!($this->sock && $this->smtp_ok())) {
            $this->log_write('Error: Cannot connenct to relay host ' . $this->getSmtpHost() . ' ' . $errstr . ' (' . $errno . ")\n");

            return false;
        }
        $this->log_write('Connected to relay host ' . $this->getSmtpHost() . "\n");

        return true;
    }

    public function smtp_sockopen_mx($address) {
        $domain = preg_replace('/^.+@([^@]+)$/', "\\1", $address);
        if (!@getmxrr($domain, $MXHOSTS)) {
            $this->log_write('Error: Cannot resolve MX "' . $domain . "\"\n");

            return false;
        }
        foreach ($MXHOSTS as $host) {
            $this->log_write('Trying to ' . $host . ':' . $this->getSmtpPort() . "\n");
            $this->sock = @fsockopen($host, $this->getSmtpPort(), $errno, $errstr, $this->getTimeOut());

            if (!($this->sock && $this->smtp_ok())) {
                $this->log_write('Warning: Cannot connect to mx host ' . $host . "\n");
                $this->log_write('Error: ' . $errstr . ' (' . $errno . ")\n");
                continue;
            }
            $this->log_write('Connected to mx host ' . $host . "\n");

            return true;
        }
        $this->log_write('Error: Cannot connect to any mx hosts (' . implode(', ', $MXHOSTS) . ")\n");

        return false;
    }

    public function smtp_message($header, $body) {
        fwrite($this->sock, $header . "\r\n" . $body);
        $this->smtp_debug('> ' . str_replace("\r\n", "\n" . '> ', $header . "\n> " . $body . "\n> "));

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
            $this->log_write('Error: Remote host returned "' . $response . "\"\n");

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
        $this->log_write('Error: Error occurred while ' . $string . ".\n");

        return false;
    }

    public function log_write($message) {
        $this->smtp_debug($message);
        $this->setMsg($message);

        if ($this->getLog_file() == '') {
            return true;
        }

        $message = date('M d H:i:s ') . get_current_user() . '[' . getmypid() . ']: ' . $message;
        if (!@file_exists($this->getLog_file()) || !($fp = @fopen($this->getLog_file(), 'ab'))) {
            $this->smtp_debug('Warning: Cannot open log file "' . $this->getLog_file() . "\"\n");

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
        if ($this->getDebug()) {
            echo $message . '<br>';
        }
    }

    public function get_attach_type($image_tag) {
        $filedata = array();
        $img_file_con = fopen($image_tag, 'rb');
        unset($image_data);
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

// end class
}