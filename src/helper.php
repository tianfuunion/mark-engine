<?php
    declare (strict_types=1);

    use mark\filesystem\Explorer;

    if (!function_exists('p')) {
        /**
         * 输出各种类型的数据，调试程序时打印数据使用。
         *
         * @param mixed    参数：可以是一个或多个任意变量或值
         */
        function p()
        {
            $args = func_get_args();  //获取多个参数
            if (count($args) < 1) {
                return;
            }
            echo '<div style="width:100%;text-align:left;"><pre>';
            //多个参数循环输出
            foreach ($args as $arg) {
                if (is_array($arg)) {
                    print_r($arg);
                    echo '<br>';
                } elseif (is_string($arg)) {
                    echo unicodeDecode($arg) . '<br>';
                } else {
                    var_dump($arg);
                    echo '<br>';
                }
            }
            echo '</pre></div>';
        }
    }

    if (!function_exists('isEmpty')) {
        /**
         * 检测变量是否为空
         *
         * @param null $variable
         * @return bool
         * @deprecated
         */
        function isEmpty($variable = null)
        {
            return is_empty($variable) ? true : false;
        }
    }

    if (!function_exists('is_empty')) {
        /**
         * 判断php变量是否定义，是否为空,为空则返回true, 反之则返回false
         *
         * @link https://www.cnblogs.com/jasonxu19900827/p/6637913.html
         *
         * @source
         * 表达式         gettype()    empty()    is_null()    isset()    boolean : if($x)
         * $x = "";         string        TRUE    false    TRUE    false\n
         * $x = null;     NULL        TRUE    TRUE    false    false
         * var $x;         NULL        TRUE    TRUE    false    false
         * $x is undefined    NULL    TRUE    TRUE    false    false
         * $x = array(); array        TRUE    false    TRUE    false
         * $x = false;     boolean    TRUE    false    TRUE    false
         * $x = true;     boolean    false    false    TRUE    TRUE
         * $x = 1;         integer    false    false    TRUE    TRUE
         * $x = 42;         integer    false    false    TRUE    TRUE
         * $x = 0;         integer    TRUE    false    TRUE    false
         * $x = -1;         integer    false    false    TRUE    TRUE
         * $x = "1";     string        false    false    TRUE    TRUE
         * $x = "0";     string        TRUE    false    TRUE    false
         * $x = "-1";     string        false    false    TRUE    TRUE
         * $x = "php";     string        false    false    TRUE    TRUE
         * $x = "true";     string        false    false    TRUE    TRUE
         * $x = "false"; string        false    false    TRUE    TRUE
         *
         * @param null $variable
         * @return bool
         */
        function is_empty($variable = null)
        {
            if (
                $variable == null
                || $variable == ''
                || $variable == 'null'
                || $variable == 'undefined'
                || $variable == 'undefined'
                || !isset($variable)) {
                return true;
            }

            switch (gettype($variable)) {
                case 'boolean':
                    return $variable;
                case 'string':
                    return empty($variable) || $variable == '' || $variable == null;
                case 'array':
                    return count($variable) <= 0;
                case 'integer':
                    return false;
                case 'NULL':
                    return true;
                default:
                    return true;
                    break;
            }
        }
    }

    if (!function_exists('toSize')) {
        /**
         * 文件尺寸转换，将大小将字节转为各种单位大小
         *
         * @param int $bytes 字节大小
         *
         * @return    string    转换后带单位的大小
         * @link Explorer::toSize()
         */
        function toSize($bytes)
        {
            if ($bytes >= (2 ** 40)) { // 如果提供的字节数大于等于2的40次方，则条件成立
                //将字节大小转换为同等的T大小
                $return = round($bytes / (1024 ** 4), 2);
                //单位为TB
                $suffix = 'TB';
            } elseif ($bytes >= (2 ** 30)) { // 如果提供的字节数大于等于2的30次方，则条件成立
                //将字节大小转换为同等的G大小
                $return = round($bytes / (1024 ** 3), 2);
                //单位为GB
                $suffix = 'GB';
            } elseif ($bytes >= (2 ** 20)) { // 如果提供的字节数大于等于2的20次方，则条件成立
                //将字节大小转换为同等的M大小
                $return = round($bytes / (1024 ** 2), 2);
                //单位为MB
                $suffix = 'MB';
            } elseif ($bytes >= (2 ** 10)) { // 如果提供的字节数大于等于2的10次方，则条件成立
                $return = round($bytes / (1024 ** 1), 2);
                //将字节大小转换为同等的K大小
                $suffix = 'KB';
                //单位为KB
            } else { // 否则提供的字节数小于2的10次方，则条件成立
                //字节大小单位不变
                $return = $bytes;
                //单位为Byte
                $suffix = 'Byte';
            }

            //返回合适的文件大小和单位
            return $return . ' ' . $suffix;
        }
    }

    if (!function_exists('getRequestHost')) {
        /**
         * 获取当前访问请求域名
         *
         * @return string
         */
        function getRequestHost()
        {
            return ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        }
    }

    if (!function_exists('getRequestUrl')) {
        /**
         * 获取当前访问请求Url，带参数
         *
         * @return false|string
         */
        function getRequestUrl()
        {
            return utf8_encode($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . ':' . $_SERVER['REQUEST_URI']);
        }
    }

    if (!function_exists('getRequestMethod')) {
        /**
         * 获取请求方法
         * @return bool|string
         */
        function getRequestMethod()
        {
            return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : false;
        }
    }

    if (!function_exists('is_post')) {
        /**
         * 判断是否为POST方法请求
         * @return bool
         */
        function is_post()
        {
            return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
        }
    }

    if (!function_exists('is_get')) {
        /**
         * 判断是否为GET方法请求
         * @return bool
         */
        function is_get()
        {
            return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'GET';
        }
    }

    if (!function_exists('is_ajax')) {
        /**
         * 判断是否为AJAX方法请求
         * @return bool
         */
        function is_ajax()
        {
            return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']) === 'XMLHTTPREQUEST';
        }
    }

    if (!function_exists('is_pjax')) {
        /**
         * 当前是否Pjax请求
         * @access public
         * @param bool $pjax true 获取原始pjax请求
         * @return bool
         */
        function is_pjax(bool $pjax = false): bool
        {
            $result = !empty($_SERVER['HTTP_X_PJAX']) ? true : false;

            if (true === $pjax) {
                return $result;
            }

            return $result;
        }
    }

    if (!function_exists('is_json')) {
        /**
         * 当前是否JSON请求
         * @access public
         * @return bool
         */
        function is_json(): bool
        {
            if (!isset($_SERVER['HTTP_ACCEPT'])) {
                return false;
            }
            return false !== strpos($_SERVER['HTTP_ACCEPT'], 'json');
        }
    }

    if (!function_exists('is_cli')) {
        /**
         * 判断是否为命令行模式方法请求
         * @return bool
         */
        function is_cli()
        {
            return (PHP_SAPI === 'cli' or defined('STDIN'));
        }
    }

    if (!function_exists('is_ssl')) {
        /**
         * 判断是否SSL协议
         * @return bool
         */
        function is_ssl()
        {
            if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
                return true;
            } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
                //https使用端口443
                return true;
            }
            return false;
        }
    }

    if (!function_exists('UnicodeEncode')) {
        /**
         * 下面来看PHP Unicode编码方法，将中文转为Unicode字符，例如将新浪微博转换为unicode字符串，代码如下
         * $str = "新浪微博";
         * echo UnicodeEncode($str);
         * Unicode编码输出字符串：“\u65b0\u6d6a\u5fae\u535a”
         *
         * @param $str
         * @return string
         */
        function UnicodeEncode($str)
        {
            // split word
            preg_match_all('/./u', $str, $matches);
            $unicodeStr = '';
            foreach ($matches[0] as $m) {
                //拼接
                $unicodeStr .= '&#' . base_convert(bin2hex(iconv('UTF-8', 'UCS-4', $m)), 16, 10);
            }

            return $unicodeStr;
        }
    }

    if (!function_exists('unicodeDecode')) {
        /**
         * 2：unicode解码方法，将上面的unicode字符转换成中文，代码如下：
         * $unicode_str = "\u65b0\u6d6a\u5fae\u535a";
         * echo unicodeDecode($unicode_str);
         * Unicode解码结果：“新浪微博”
         *
         * @param $unicode_str
         * @return mixed|string
         */
        function unicodeDecode($unicode_str)
        {
            $json = '{"str":"' . $unicode_str . '"}';
            $arr = json_decode($json, true);
            if (empty($arr)) {
                return "";
            }

            return $arr['str'];
        }
    }

    if (!function_exists('compress_html')) {
        /**
         * 压缩html
         *
         * @param $html
         * @return string
         */
        function compress_html($html)
        {
            $html = preg_replace(":\s+//.*?\n:", '', $html);
            $html = preg_replace("/<!--\s*[^[][^!][^&lt;].*?-->/s", '', $html);
            $html = preg_replace("/\/\*.*?\*\//s", '', $html);
            $html = preg_replace("/&gt;\s*&lt;/s", '&gt;&lt;', $html);
            $html = preg_replace("/(\s)+/s", ' ', $html);

            return trim($html);
        }
    }

    if (!function_exists('getallheaders')) {
        /**
         * 这个函数只能在apache环境下使用，iis或者nginx并不支持，可以通过自定义函数来实现。
         *
         * @return array
         */
        function getallheaders()
        {
            $headers = array();
            foreach ($_SERVER as $name => $value) {
                if (strpos($name, "HTTP_") === 0) {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }
    }

    if (!function_exists('style')) {
        /**
         * @param $style
         * @param string $type
         * @param null $path
         * @param string $version
         */
        function style($style, $type = 'private', $path = null, $version = "1.0")
        {
            if ($style != '') {
                switch ($type) {
                    case 'opensource':
                    case 'open': // 开源的，或来自网络的
                        $css = array('style' => $style, 'asset' => $path . DIRECTORY_SEPARATOR . $style . '.css', 'type' => $type, "version" => $version);
                        break;
                    case 'public':
                        $css = array('style' => $style, 'asset' => DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . $style . '.css', 'type' => $type, "version" => $version);
                        break;
                    case 'protected':
                    case 'project':
                        $css = array('style' => $style, 'asset' => DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . $style . '.css', 'type' => $type, "version" => $version);
                        break;
                    case 'private':
                    default:
                        $css = array('style' => $style, 'asset' => 'style' . DIRECTORY_SEPARATOR . $style . '.css', 'type' => $type, "version" => $version);
                        break;
                }

                $css_src = app()->env->get('style');
                if (!empty($css_src)) {
                    if (!in_array($css, $css_src)) {
                        $css_src[] = $css;
                        // $css_src = array_unique($css_src);
                    }
                } else {
                    $css_src = array($css);
                }
                app()->env->set('style', $css_src);
            }
        }
    }

    if (!function_exists('script')) {
        /**
         * opensource   开源库
         * private      私有库：第1方库
         * project      项目库：第2方库
         * protected    模块库：第2方库
         * public       公共库：第3方库
         *
         * @param string $script
         * @param string $type [public|project|protected|private]
         * @param string $path
         * @param string $version
         */
        function script($script = '', $type = 'private', $path = '', $version = "1.0")
        {
            if ($script != '') {
                switch ($type) {
                    case 'opensource':
                    case 'open': // 开源的，或来自网络的
                        $js = array('script' => $script, 'asset' => $path . DIRECTORY_SEPARATOR . $script . '.js', 'type' => $type, "version" => $version);
                        break;
                    case 'public': // 公共库
                        $js = array('script' => $script, 'asset' => DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . $script . '.js', 'type' => $type, "version" => $version);
                        break;
                    case 'project': // 当前项目库
                        $js = array('script' => $script, 'asset' => DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'script' . DIRECTORY_SEPARATOR . $script . '.js', 'type' => $type, "version" => $version);
                        break;
                    case 'protected': // 当前模块库
                        $js = array('script' => $script, 'asset' => DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'script' . DIRECTORY_SEPARATOR . $script . '.js', 'type' => $type, "version" => $version);
                        break;
                    case 'private': // 当前动作库
                    default:
                        $js = array('script' => $script, 'asset' => 'script' . DIRECTORY_SEPARATOR . $script . '.js', 'type' => $type, "version" => $version);
                        break;
                }

                $js_src = app()->env->get('script');
                if (!empty($js_src)) {
                    if (!in_array($js, $js_src)) {
                        $js_src[] = $js;
                        // $css_src = array_unique($css_src);
                    }
                } else {
                    $js_src = array($js);
                }
                app()->env->set('script', $js_src);
            }
        }
    }

    if (!function_exists('toQuote')) {
        /**
         * 替换单引号和双引号
         *
         * @param $str
         * @return string
         */
        function toQuote($str)
        {
            $str = str_replace(array("'", '"'), array('&#39;', '&#34;'), $str);

            return trim($str);
        }
    }

    if (!function_exists('deQuote')) {
        /**
         * @param $str
         *
         * @return string
         */
        function deQuote($str)
        {
            $str = str_replace(array('&#39;', '&#34;'), array("'", '"'), $str);

            return trim($str);
        }
    }

    if (!function_exists('replaceStr')) {
        /**
         * 常规字符串条件替换
         *
         * @param $mode
         * @param $str
         * @param $from
         * @param $to
         *
         * @return string
         */
        function replaceStr($mode, $str, $from, $to)
        {
            switch ($mode) {
                case '':
                default:
                    $return = strtr($str, array($from => $to));
                    break;
                case 'empty':
                    $return = (empty($str) || $str == '') ? $from : $str;
                    break;
            }

            return $return;
        }
    }

    if (!function_exists('getFileInfo')) {
        /**
         * 文件地址处理
         *
         * @param $str
         * @param $mode
         *
         * @return mixed|string
         */
        function getFileInfo($str, $mode)
        {
            if ($str == '' || $str === null) {
                return "";
            }
            switch ($mode) {
                case 'path' :
                    return dirname($str);
                    break;
                case 'name' :
                    $args = explode('.', $str);
                    if (!empty($args) && $args !== false) {
                        return basename($str, '.' . end($args));
                    }
                    break;
                case 'ext' :
                    $args = explode('.', $str);
                    if (!empty($args) && $args !== false) {
                        return end($argv);
                    }
                    break;
                case 'simg' :
                    return getFileInfo($str, 'path') . '/s_' . getFileInfo($str, 'name') . '.jpg';
                    break;
            }
            return $str;
        }
    }

    if (!function_exists('cutstr')) {
        /**
         * 字符截断，支持中英文不乱码
         *
         * @param        $str
         * @param int $len
         * @param string $dot
         * @param string $encoding
         *
         * @return mixed|string
         */
        function cutstr($str, $len = 0, $dot = '...', $encoding = 'utf-8')
        {
            if (!is_numeric($len)) {
                $len = (int)$len;
            }
            if (!$len || strlen($str) <= $len) {
                return $str;
            }
            $tempstr = '';
            $str = str_replace(array('&', '"', '<', '>'), array('&', '"', '<', '>'), $str);
            if ($encoding === 'utf-8') {
                $n = $tn = $noc = 0;
                while ($n < strlen($str)) {
                    $t = ord($str[$n]);
                    if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                        $tn = 1;
                        $n++;
                        $noc++;
                    } elseif (194 <= $t && $t <= 223) {
                        $tn = 2;
                        $n += 2;
                        $noc += 2;
                    } elseif (224 <= $t && $t < 239) {
                        $tn = 3;
                        $n += 3;
                        $noc += 2;
                    } elseif (240 <= $t && $t <= 247) {
                        $tn = 4;
                        $n += 4;
                        $noc += 2;
                    } elseif (248 <= $t && $t <= 251) {
                        $tn = 5;
                        $n += 5;
                        $noc += 2;
                    } elseif ($t == 252 || $t == 253) {
                        $tn = 6;
                        $n += 6;
                        $noc += 2;
                    } else {
                        $n++;
                    }
                    if ($noc >= $len) {
                        break;
                    }
                }
                if ($noc > $len) {
                    $n -= $tn;
                }
                $tempstr = substr($str, 0, $n);
            } elseif ($encoding === 'gbk') {
                for ($i = 0; $i < $len; $i++) {
                    $tempstr .= ord($str{$i}) > 127 ? $str{$i} . $str{++$i} : $str{$i};
                }
            }
            $tempstr = str_replace(array('&', '"', '<', '>'), array('&', '"', '<', '>'), $tempstr);

            return $tempstr . $dot;
        }
    }

    if (!function_exists('cuthtml')) {
        /**
         * 字符截断，支持html补全
         *
         * @param        $str
         * @param int $length
         * @param string $suffixStr
         * @param bool $clearhtml
         * @param string $charset
         * @param int $start
         * @param string $tags
         * @param float $zhfw
         *
         * @return mixed|string
         */
        function cuthtml(
            $str, $length = 0, $suffixStr = '...', $clearhtml = true, $charset = 'utf-8', $start = 0,
            $tags = 'P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|TABLE|TR|TD|TH|INPUT|SELECT|TEXTAREA|OBJECT|A|UL|OL|LI|BASE|META|LINK|HR|BR|PARAM|IMG|AREA|INPUT|SPAN',
            $zhfw = 0.9
        )
        {
            if ($clearhtml || $clearhtml == 1) {
                return cutstr(strip_tags($str), $length, $suffixStr, $charset);
            }
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            $zhre['utf-8'] = "/[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $zhre['gb2312'] = "/[\xb0-\xf7][\xa0-\xfe]/";
            $zhre['gbk'] = "/[\x81-\xfe][\x40-\xfe]/";
            $zhre['big5'] = "/[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            $tpos = array();
            preg_match_all('/<(' . $tags . ")([\s\S]*?)>|<\/(" . $tags . ')>/ism', $str, $match);
            $mpos = 0;
            for ($j = 0, $jMax = count($match[0]); $j < $jMax; $j++) {
                $mpos = strpos($str, $match[0][$j], $mpos);
                $tpos[$mpos] = $match[0][$j];
                $mpos += strlen($match[0][$j]);
            }
            ksort($tpos);
            $sarr = array();
            $bpos = 0;
            $epos = 0;
            foreach ($tpos as $k => $v) {
                $temp = substr($str, $bpos, $k - $epos);
                if (!empty($temp)) {
                    $sarr[] = $temp;
                }
                $sarr[] = $v;
                $bpos = ($k + strlen($v));
                $epos = $k + strlen($v);
            }
            $temp = substr($str, $bpos);
            if (!empty($temp)) {
                $sarr[] = $temp;
            }
            $bpos = $start;
            $epos = $length;
            foreach ($sarr as $i => $iValue) {
                if (preg_match("/^<([\s\S]*?)>$/i", $sarr[$i])) {
                    continue;
                }
                preg_match_all($re[$charset], $sarr[$i], $match);
                for ($j = $bpos, $jMax = min($epos, count($match[0])); $j < $jMax; $j++) {
                    if (preg_match($zhre[$charset], $match[0][$j])) {
                        $epos -= $zhfw;
                    }
                }
                $sarr[$i] = '';
                for ($j = $bpos, $jMax = min($epos, count($match[0])); $j < $jMax; $j++) {
                    $sarr[$i] .= $match[0][$j];
                }
                $bpos -= count($match[0]);
                $bpos = max(0, $bpos);
                $epos -= count($match[0]);
                $epos = round($epos);
            }
            $slice = implode('', $sarr);
            if ($slice != $str) {
                return $slice . $suffixStr;
            }

            return $slice;
        }
    }

    if (!function_exists('showTinyintMsg')) {
        /**
         * 根据tinyint字段判断显示内容
         *
         * @param $val
         * @param $str1
         * @param $str2
         *
         * @return mixed
         */
        function showTinyintMsg($val, $str1, $str2)
        {
            if ($val == 1) {
                $out = $str1;
            } else {
                $out = $str2;
            }

            return $out;
        }
    }

    if (!function_exists('arrayRecursive')) {
        /**
         * 使用特定function对数组中所有元素做处理
         *
         * @param array $array 要处理的字符串
         * @param callable $function 要执行的函数
         * @param bool $apply_to_keys_also 是否也应用到key上
         */
        function arrayRecursive(array &$array, callable $function, $apply_to_keys_also = false)
        {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    arrayRecursive($array[$key], $function, $apply_to_keys_also);
                } else {
                    $array[$key] = $function($value);
                }

                if ($apply_to_keys_also && is_string($key)) {
                    $new_key = $function($key);
                    if ($new_key != $key) {
                        $array[$new_key] = $array[$key];
                        unset($array[$key]);
                    }
                }
            }
        }
    }

    if (!function_exists('downfile')) {
        /**
         * 下载文件至服务器
         *
         * @param $url
         * @param $path
         *
         * @return array
         * @deprecated
         */
        function downfile($url, $path)
        {
            $arr = parse_url($url);

            $imginfo = getimagesize($url);
            $suffix = trim(strrchr($imginfo['mime'], '/'), '/');
            $fileName = 'av_' . time() . '.' . ($suffix ?: 'jpg');

            $file = file_get_contents($url);
            $result = file_put_contents($path . $fileName, $file);

            return array('result' => $result, 'url' => $url, 'path' => $path, 'filename' => $fileName, 'suffix' => $suffix);
        }
    }

    if (!function_exists('downfile2')) {
        /**
         * @param $url
         * @param $path
         *
         * @return array
         * @deprecated
         */
        function downfile2($url, $path)
        {
            $curl = curl_init();//初始化一个cURL会话
            curl_setopt($curl, CURLOPT_URL, $url);//抓取url
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//是否显示头信息
            curl_setopt($curl, CURLOPT_SSLVERSION, 3);//传递一个包含SSL版本的长参数
            $data = curl_exec($curl);// 执行一个cURL会话
            $error = curl_error($curl);//返回一条最近一次cURL操作明确的文本的错误信息。
            curl_close($curl);//关闭一个cURL会话并且释放所有资源

            $imginfo = getimagesize($url);
            $suffix = trim(strrchr($imginfo['mime'], '/'), '/');
            $fileName = 'av_' . time() . '.' . ($suffix ?: 'jpg');

            $file = fopen($fileName, 'wb+');
            fwrite($file, $data);//写入文件
            fclose($file);

            return array('result' => $data, 'url' => $url, 'path' => $path, 'filename' => $fileName, 'suffix' => $suffix);
        }
    }

    if (!function_exists('curlDownFile')) {
        /**
         * CURL 下载文件(图片)
         *
         * @param        $url
         * @param string $path
         * @param string $filename
         *
         * @return array|bool
         * @deprecated
         */
        function curlDownFile($url, $path = '', $filename = '')
        {
            if (trim($url) == '') {
                return false;
            }
            if (trim($path) == '') {
                $path = $GLOBALS['upload']['attachdir'];
            }
            if (trim($filename) == '') {
                $imginfo = getimagesize($url);
                $suffix = trim(strrchr($imginfo['mime'], '/'), '/');
                $filename = 'av_' . time() . '.' . ($suffix ?: 'jpg');
            }

            // curl下载文件
            $curl = curl_init();
            $timeout = 5;
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
            $result = curl_exec($curl);
            curl_close($curl);

            // 保存文件到制定路径
            $bytes = file_put_contents($path . '/' . $filename, $result);

            return array('bytes' => $bytes, 'url' => $url, 'path' => $path, 'filename' => $filename);
        }
    }

    if (!function_exists('merge_css')) {
        /**
         * 合并CSS
         * @param $css
         * @param null $savefile
         * @return false|int|string
         */
        function merge_css($css, $savefile = null)
        {
            if (is_array($css)) {
                $paths = $css;
            } else {
                $paths = array($css);
            }
            $css_content = '';
            foreach ($paths as $path) {
                $asset = $path['asset'];
                $css_content .= @file_get_contents($asset);
                /*
                if (file_exists($asset)) {
                    $css_content .= @file_get_contents($asset);
                } else {
                    dump($asset);
                    dump($path);
                }
                */
            }

            //清除换行符、换行符、制表符
            $css_content = str_replace(array("\r\n", "\n", "\t", "../images/"), array("", "", "", "./../common/images/"), $css_content);

            $css_content = compress_html($css_content);

            if ($savefile == null) {
                return $css_content;
            }

            Explorer::mkdir(pathinfo($savefile, PATHINFO_DIRNAME));

            return @file_put_contents($savefile, $css_content);
        }
    }

    if (!function_exists('uri_merge')) {

        /**
         * 合并URI，向URL中添加参数
         * @param string $url
         * @param string $key
         * @param $value
         * @return string
         */
        function uri_merge(string $url, string $key, $value)
        {
            $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
            $url = substr($url, 0, -1);
            if (strpos($url, '?') === false) {
                return ($url . '?' . $key . '=' . $value);
            } else {
                return ($url . '&' . $key . '=' . $value);
            }
        }
    }

    /**========== View Modifier ==========**/
    if (!function_exists('phone_format')) {
        /**
         * 手机号格式化
         * 参数1：手机号
         * 参数2：开始位置，默认为3
         * 参数3：结束位置，默认为6
         * 参数4：间隔符号，默认为空格
         * 输出：格式化后的手机号：133 3360 9123
         * 默认：开始于3，间隔4个
         *
         * @param $phone
         * @param int $start
         * @param int $end
         * @param string $spacing
         * @return string
         */
        function phone_format($phone, $start = 3, $end = 4, $spacing = ' ')
        {
            if ($start == 0) {
                return '';
            }
            if (strlen($phone) > $start) {
                return substr($phone, 0, (int)$start) .
                    $spacing .
                    substr($phone, (int)$start, (int)$end) .
                    $spacing .
                    substr($phone, (int)$start + (int)$end);
            }
            return $phone;
        }
    }

    if (!function_exists('string_explode')) {
        /**
         * @param string $string
         * @param string $separator
         * @param int $limit
         * @return false|string|string[]
         */
        function string_explode(string $string, $separator = ',', $limit = 0)
        {
            if (empty($string) && $string === '') {
                return $string;
            }
            return explode($separator, $string);
        }
    }

    if (!function_exists('string_encrypt')) {
        /**
         * 字符串加密
         *
         * @param string $string 字符串，例手机号（默认参数）
         * @param string $params 字符数组，加密的起始位置，加密字符（附加参数）
         *
         * @return string
         * @example <{$phone|string_encrypt:"3,7,×"}>
         * @TODO    :暂时只能加密已知长度，随后加入根据长度自动计算
         */
        function string_encrypt(string $string, $params = '')
        {
            if (empty($string) && $string === '') {
                return $string;
            }
            if ($params == '') {
                return $string;
            }

            $args = explode(',', $params);
            $start = $args[0] ?? 0;
            $end = $args[1] ?? strlen($string);
            $fill = $args[2] ?? "*";

            return strlen($string) > $start
                ? substr($string, 0, (int)$start)
                . str_repeat($fill, (int)abs($end) - (int)$start)
                . substr($string, (int)abs($end))
                : $string;
        }
    }

    if (!function_exists('array_total')) {
        /**
         * 统计数组中某一列的和
         *
         * @param array $array
         * @param string $field
         *
         * @return float|int
         */
        function array_total(array $array, string $field)
        {
            if (is_array($array) && count($array) > 0 && !empty($field)) {
                return array_sum(array_column($array, $field));
            }
            return 0;
        }
    }

    if (!function_exists('auto_version')) {
        /**
         * 自动生成版本号
         *
         * @param $file
         * @return string
         * @deprecated
         */
        function auto_version($file)
        {
            if (file_exists($file)) {
                $ver = filemtime($file);
            } else {
                $ver = time();
            }

            return $file . '?v=' . date('YmdHis', $ver);
        }
    }

    if (!function_exists('truncate')) {
        /**
         * 截取字符串到指定长度，默认长度是80. 第二个参数可选，指定了截取后代替显示的字符。 截取后的字符长度是截取规定的长度加上第二个参数的字符长度。
         * 默认truncate会尝试按单词进行截取。 如果你希望按字符截取（单词可能会被截断），需要设置第三个参数true。
         *
         * @param string $string
         * @param int $length
         * @param string $etc
         * @param bool $break_words
         * @param bool $middle
         * @param string $charset
         * @return string
         */
        function truncate(string $string, $length = 80, $etc = '...', $break_words = false, $middle = false, $charset = 'UTF-8')
        {
            if ($length == 0) {
                return '';
            }

            // if (Smarty::$_MBSTRING) {
            if (function_exists('mb_get_info')) {
                if (mb_strlen($string, $charset) > $length) {
                    $length -= min($length, mb_strlen($etc, $charset));
                    if (!$break_words && !$middle) {
                        $string = preg_replace('/\s+?(\S+)?$/' . 'u', '', mb_substr($string, 0, $length + 1, $charset));
                    }
                    if (!$middle) {
                        return mb_substr($string, 0, $length, $charset) . $etc;
                    }
                    return mb_substr($string, 0, $length / 2, $charset) . $etc . mb_substr($string, -$length / 2, $length, $charset);
                }
                return $string;
            }

            // no MBString fallback
            if (isset($string[$length])) {
                $length -= min($length, strlen($etc));
                if (!$break_words && !$middle) {
                    $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
                }
                if (!$middle) {
                    return substr($string, 0, $length) . $etc;
                }

                return substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2);
            }

            return $string;
        }
    }

    if (!function_exists('parse_host')) {
        /**
         * 解析Host
         *
         * @param string $url
         * @return mixed|string
         */
        function parse_host(string $url)
        {
            if (!is_string($url) || $url == '') return "";
            $info = parse_url($url);
            $host = isset($info['host']) ? $info['host'] : "";
            if ($host == "") return "";
            if (preg_match("/^192\.168\.\d{1,3}\.\d{1,3}|127\.\d{1,3}\.\d{1,3}\.\d{1,3}|255\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $host)) return "";
            if (!preg_match("/\.[a-z]+$/i", $host) && !preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $host)) return "";

            return $host;
        }
    }

    if (!function_exists('parse_domain')) {
        /**
         * 解析Domain
         *
         * @param string $url
         * @return mixed|string
         */
        function parse_domain(string $url)
        {
            $host = parse_host($url);
            if ($host === "") return "";
            // 纯IP
            if (preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $host)) {
                preg_match("/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$/", $host, $matches);
                if ($matches) return $matches[1];
            } else {
                preg_match("/(.*?)([^.]+\.[^.]+)$/", $host, $matches);
                if ($matches) return $matches[2];
            }
            return "";
        }
    }