<?php
    declare (strict_types=1);

    namespace mark\system;

    use Exception;

    /**+==================================================
     * |  名称：MarkEngine
     * +--------------------------------------------------
     * |  文件：Operating System php
     * +--------------------------------------------------
     * |  概要: 操作系统浏览器信息管理类.
     * +--------------------------------------------------
     * |  版权：Copyright (c) 2017~2020 https://mark.tianfu.ink All rights reserved.
     * +--------------------------------------------------
     * |  许可：Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
     * +--------------------------------------------------
     * |  作者：Author: Mark <mark@tianfuunion.cn>
     * +--------------------------------------------------
     * |  创建时间: 2018-12-12
     * +--------------------------------------------------
     * |  修改时间: 2020-08-05 10:24:00
     * +--------------------------------------------------
     *
     * Class Os
     * @package mark\system
     **+==================================================*/
    final class Os
    {
        private function __construct()
        {

        }

        /**
         * 获取HTTP代理
         *
         * @return string
         */
        public static function getAgent()
        {
            if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] != '') {
                return strtolower($_SERVER['HTTP_USER_AGENT']);
            }
            if (isset($_SERVER['HTTP_X_UCBROWSER_UA']) && !empty($_SERVER['HTTP_X_UCBROWSER_UA']) && $_SERVER['HTTP_X_UCBROWSER_UA'] != '') {
                return strtolower($_SERVER['HTTP_X_UCBROWSER_UA']);
            }

            return '';
        }

        /**
         * HTTP请求头
         *
         * @return string
         */
        public static function getAccept()
        {
            if (isset($_SERVER['HTTP_ACCEPT']) && !empty($_SERVER['HTTP_ACCEPT'])) {
                return strtolower($_SERVER['HTTP_ACCEPT']);
            }
            return '';
        }

        /**
         * 获取HTTP代理
         *
         * @return string
         * @see Os::getAgent()
         */
        public static function getProxy()
        {
            return self::getAgent();
        }

        /**
         * 获取客户端操作系统信息包括win10
         * @link https://docs.microsoft.com/zh-cn/windows/win32/sysinfo/operating-system-version?redirectedfrom=MSDN
         * @param $flag
         * @return array|string
         * @example os|version|string|array
         */
        public static function getOs($flag = '')
        {
            $agent = strtolower(self::getAgent());
            try {
                if (isset($_SERVER['HTTP_X_UCBROWSER_UA']) && !empty($_SERVER['HTTP_X_UCBROWSER_UA'])) {
                    //获取UC用户代理字符串
                    $vers = explode(';', $_SERVER['HTTP_X_UCBROWSER_UA']);
                    $os = trim($vers[2], "\ov( | )");
                    $version = trim($vers[2], "\ov( | ))");
                } elseif (stripos($agent, 'Android') !== false) {
                    $match = preg_match("/(?<=android )[\d.]+/", $agent, $ver);
                    $os = 'Android';
                    if ($match) {
                        $version = $ver[0];
                    } else {
                        $version = 0;
                    }
                } elseif (stripos($agent, 'iphone') !== false) {
                    preg_match("/iphone os [\d|\w]*/", $agent, $ver);
                    $os = 'iPhone';
                    $version = str_replace('_', '.', preg_replace('/iphone os +/i', '', $ver[0]));
                } elseif (stripos($agent, 'ipad') !== false) {
                    preg_match("/cpu os [\d_]+/", $agent, $ver);
                    $os = 'iPad';
                    $version = str_replace('_', '.', preg_replace('/cpu os +/i', '', $ver[0]));
                } elseif (false !== stripos($agent, 'win') && preg_match('/nt 10.0/i', $agent)) {
                    $os = 'Windows';
                    $version = '10';
                } elseif (false !== stripos($agent, 'win') && preg_match('/nt 6.2/i', $agent)) {
                    $os = 'Windows';
                    $version = '8';
                } elseif (false !== stripos($agent, 'win') && preg_match('/nt 6.1/i', $agent)) {
                    $os = 'Windows';
                    $version = '7';
                } elseif (false !== stripos($agent, 'win') && preg_match('/nt 6.0/i', $agent)) {
                    $os = 'Windows';
                    $version = 'Vista';
                } elseif (false !== stripos($agent, 'win') && preg_match('/nt 5.1/i', $agent)) {
                    $os = 'Windows';
                    $version = 'XP';
                } elseif (false !== stripos($agent, 'win') && preg_match('/nt 5/i', $agent)) {
                    $os = 'Windows';
                    $version = '2000';
                } elseif (false !== stripos($agent, 'win') && false !== strpos($agent, '98')) {
                    $os = 'Windows';
                    $version = '98';
                } elseif (false !== stripos($agent, 'win') && strpos($agent, '95')) {
                    $os = 'Windows';
                    $version = '95';
                } elseif (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
                    $os = 'Windows';
                    $version = 'ME';
                } elseif (false !== stripos($agent, 'win') && false !== stripos($agent, 'nt')) {
                    $os = 'Windows';
                    $version = 'NT';
                } elseif (false !== stripos($agent, 'win') && false !== strpos($agent, '32')) {
                    $os = 'Windows';
                    $version = '32';
                } elseif (false !== stripos($agent, 'linux')) {
                    $os = 'Linux';
                    $version = 0;
                } elseif (false !== stripos($agent, 'unix')) {
                    $os = 'Unix';
                    $version = 0;
                } elseif (false !== stripos($agent, 'sun') && false !== stripos($agent, 'os')) {
                    $os = 'SunOS';
                    $version = 0;
                } elseif (false !== stripos($agent, 'ibm') && false !== stripos($agent, 'os')) {
                    $os = 'IBM OS/2';
                    $version = 0;
                } elseif (false !== stripos($agent, 'mac') && false !== stripos($agent, 'PC')) {
                    $os = 'MAC';
                    $version = 0;
                } elseif (false !== stripos($agent, 'powerpc')) {
                    $os = 'PowerPC';
                    $version = 0;
                } elseif (false !== stripos($agent, 'aix')) {
                    $os = 'AIX';
                    $version = 0;
                } elseif (false !== stripos($agent, 'hpux')) {
                    $os = 'HPUX';
                    $version = 0;
                } elseif (false !== stripos($agent, 'netbsd')) {
                    $os = 'NetBSD';
                    $version = 0;
                } elseif (false !== stripos($agent, 'bsd')) {
                    $os = 'BSD';
                    $version = 0;
                } elseif (false !== stripos($agent, 'osf1')) {
                    $os = 'OSF1';
                    $version = 0;
                } elseif (false !== stripos($agent, 'irix')) {
                    $os = 'IRIX';
                    $version = 0;
                } elseif (false !== stripos($agent, 'freebsd')) {
                    $os = 'FreeBSD';
                    $version = 0;
                } elseif (false !== stripos($agent, 'teleport')) {
                    $os = 'teleport';
                    $version = 0;
                } elseif (false !== stripos($agent, 'flashget')) {
                    $os = 'flashget';
                    $version = 0;
                } elseif (false !== stripos($agent, 'webzip')) {
                    $os = 'webzip';
                    $version = 0;
                } elseif (false !== stripos($agent, 'offline')) {
                    $os = 'offline';
                    $version = 0;
                } else {
                    $os = '';
                    $version = 0;
                }
            } catch (Exception $e) {
                $os = $e->getMessage();
                $version = 0;
            }

            switch ($flag) {
                case 'os':
                    return strtolower($os);
                    break;
                case 'version':
                    return strtolower($version);
                    break;
                case 'array':
                    return array('os' => $os, 'version' => $version);
                    break;
                case 'string':
                    return $os . '_' . $version;
                    break;
                default:
                    break;
            }

            return array('os' => $os, 'version' => $version);
        }

        /**
         * 获取品牌
         * private $brand;  设备品牌
         * private $model; 设备型号
         *
         * @param string $flag
         * @return array|string
         * @example brand|model|string|array;
         */
        public static function getBrand($flag = '')
        {
            $agent = strtolower(self::getAgent());
            $brand = self::isMobile() ? '手机' : '计算机';
            $model = '';
            try {
                if (isset($_SERVER['HTTP_X_UCBROWSER_UA']) && !empty($_SERVER['HTTP_X_UCBROWSER_UA'])) {
                    $vers = explode(';', $_SERVER['HTTP_X_UCBROWSER_UA']);
                    $model = trim($vers[0], "\dv(|)");//品牌
                } elseif (stripos($agent, 'iphone')) {
                    $brand = 'iPhone';
                    $match = preg_match("/iphone os [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'ipad')) {
                    $brand = 'iPad';
                    $match = preg_match("/cpu os [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (
                    stripos($agent, 'samsung') ||
                    stripos($agent, 'galaxy') ||
                    strpos($agent, 'gt-') ||
                    strpos($agent, 'sch-') ||
                    strpos($agent, 'sm-')) {
                    $brand = '三星';
                    $match = preg_match("/samsung [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (
                    stripos($agent, 'huawei') ||
                    stripos($agent, 'honor') ||
                    stripos($agent, 'h60-') ||
                    stripos($agent, 'h30-')) {
                    $brand = '华为';
                    $match = preg_match("/huawei [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'lenovo')) {
                    $brand = '联想';
                    $match = preg_match("/lenovo [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (
                    strpos($agent, 'mi-one') ||
                    strpos($agent, 'mi 1s') ||
                    strpos($agent, 'mi 2') ||
                    strpos($agent, 'mi 3') ||
                    strpos($agent, 'mi 4') ||
                    strpos($agent, 'mi-4')) {
                    $brand = '小米';
                    $match = preg_match("/mi [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (strpos($agent, 'hm note') || strpos($agent, 'hm201')) {
                    $brand = '红米';
                    $match = preg_match("/hm [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (
                    stripos($agent, 'coolpad') ||
                    strpos($agent, '8190q') ||
                    strpos($agent, '5910')) {
                    $brand = '酷派';
                    $match = preg_match("/coolpad [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (
                    stripos($agent, 'zte') ||
                    stripos($agent, 'x9180') ||
                    stripos($agent, 'n9180') ||
                    stripos($agent, 'u9180')) {
                    $brand = '中兴';
                    $match = preg_match("/zte [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (
                    stripos($agent, 'oppo') || strpos($agent, 'x9007') || strpos($agent, 'x907')
                    || strpos($agent, 'x909') || strpos($agent, 'r831s') || strpos($agent, 'r827t')
                    || strpos($agent, 'r821t') || strpos($agent, 'r811') || strpos($agent, 'r2017') ||
                    strpos($agent, 'r11')) {
                    $brand = 'OPPO';
                    $match = preg_match("/oppo [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'vivo')) {
                    $brand = 'VIVO';
                    $match = preg_match("/vivo [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'v1838a')) {
                    $brand = 'VIVO';
                    $model = 'V1838A';
                } elseif (strpos($agent, 'htc') || stripos($agent, 'desire')) {
                    $brand = 'HTC';
                    $match = preg_match("/htc [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'k-touch')) {
                    $brand = '天语';
                    $match = preg_match("/touch [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'Nubia') || stripos($agent, 'NX50') || stripos($agent, 'NX40')) {
                    $brand = '努比亚';
                    $match = preg_match("/Nubia [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (strpos($agent, 'M045') || strpos($agent, 'M032') || strpos($agent, 'M355')) {
                    $brand = '魅族';
                    $match = preg_match("/MEIZU [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'doov')) {
                    $brand = '朵唯';
                    $match = preg_match("/doov [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'gfive')) {
                    $brand = '基伍';
                    $match = preg_match("/gfive [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'gionee') || strpos($agent, 'gn')) {
                    $brand = '金立';
                    $match = preg_match("/gionee [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'hs-u') || stripos($agent, 'hs-e')) {
                    $brand = '海信';
                    $match = preg_match("/hs [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'Nokia')) {
                    $brand = '诺基亚';
                    $match = preg_match("/nokia [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                } elseif (stripos($agent, 'nexus')) {
                    $brand = 'Nexus';
                    $match = preg_match("/nexus [\d|\w]*/", $agent, $models);
                    if ($match) {
                        $model = $models[0];
                    }
                }
            } catch (Exception $e) {
            }

            switch ($flag) {
                case 'brand':
                    return strtolower($brand);
                    break;
                case 'model':
                    return strtolower($model);
                    break;
                case 'array':
                    return array('brand' => $brand, 'model' => $model);
                    break;
                case 'string':
                    return $brand . '_' . $model;
                    break;
                default:
                    break;
            }

            return array('brand' => $brand, 'model' => $model);
        }

        /**
         * 获取客户端浏览器信息 添加win10 edge浏览器判断
         *
         * @return array|string
         */
        public static function getBrowser($flag = '')
        {
            // 获取用户代理字符串
            $agent = self::getAgent();

            $browser = array('title' => '未知浏览器', 'name' => '', 'version' => 0);
            try {
                if (stripos($agent, 'AlipayClient') > 0) {
                    $match = preg_match("/AlipayClient\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '支付宝', 'name' => 'AliPay', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'DingTalk') > 0) {
                    $match = preg_match("/DingTalk\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '钉钉', 'name' => 'DingTalk', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'Quark') > 0) {
                    $match = preg_match("/Quark\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '夸克浏览器', 'name' => 'Quark', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'UCBrowser') > 0) {
                    $match = preg_match("/UCBrowser\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => 'UC浏览器', 'name' => 'UCBrowser', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'UBrowser') > 0) {
                    $match = preg_match("/UBrowser\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => 'UC电脑版', 'name' => 'UBrowser', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'WindowsWechat') > 0) {
                    $match = preg_match("/MicroMessenger\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '微信电脑版', 'name' => 'WindowsWechat', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'MicroMessenger') > 0) {
                    $match = preg_match("/MicroMessenger\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '微信', 'name' => 'WeChat', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'MQQBrowser') > 0) {
                    $match = preg_match("/MQQBrowser\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '手机QQ', 'name' => 'MQQBrowser', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'QQBrowser') > 0) {
                    $match = preg_match("/QQBrowser\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => 'QQ浏览器', 'name' => 'QQBrowser', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'Firefox/') > 0) {
                    $match = preg_match("/Firefox\/([^;)]+)+/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '火狐浏览器', 'name' => 'Firefox', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'LBBROWSER/') > 0) {
                    $match = preg_match("/LBBROWSER\/([^;)]+)+/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '猎豹浏览器', 'name' => 'LBBrowser', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'MetaSr') > 0) {
                    $match = preg_match('/MetaSr([^;)]+)+/i', $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '搜狗浏览器', 'name' => 'MetaSr', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'Maxthon') > 0) {
                    $match = preg_match("/Maxthon\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '傲游浏览器', 'name' => 'Maxthon', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'OPR') > 0) {
                    $match = preg_match("/OPR\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '欧朋浏览器', 'name' => 'Opera', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, '2345Explorer') > 0) {
                    $match = preg_match("/2345Explorer\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '2345浏览器', 'name' => '2345Explorer', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'OppoBrowser') > 0) {
                    $match = preg_match("/OppoBrowser\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => 'OPPO浏览器', 'name' => 'OPPO', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'VivoBrowser') > 0) {
                    $match = preg_match("/VivoBrowser\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => 'VIVO浏览器', 'name' => 'VIVO', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'MSIE') > 0) {
                    $match = preg_match("/MSIE\s+([^;)]+)+/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '微软浏览器', 'name' => 'IE', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'Edge') > 0) {
                    //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
                    $match = preg_match("/Edge\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '微软浏览器', 'name' => 'Edge', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'Trident') > 0) {
                    $match = preg_match("/Trident\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '微软浏览器', 'name' => 'Trident', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'rv:') > 0 && stripos($agent, 'Gecko') > 0) {
                    $match = preg_match("/rv:([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '微软浏览器', 'name' => 'IE', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'Chrome') > 0) {
                    $match = preg_match("/Chrome\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => 'Google浏览器', 'name' => 'Chrome', 'version' => $matches[1]);
                    }
                } elseif (stripos($agent, 'Safari') > 0) {
                    $match = preg_match("/Safari\/([\d.]+)/i", $agent, $matches);
                    if ($match) {
                        $browser = array('title' => '苹果浏览器', 'name' => 'Safari', 'version' => $matches[1]);
                    }
                }
            } catch (Exception $e) {
            }
            $browser['kernel'] = self::getKernel();
            if ($flag == 'string') {
                return implode("_", $browser);
            }
            return $browser;
        }

        /**
         * 获取浏览器内核
         *
         * @return string
         */
        public static function getKernel()
        {
            $agent = self::getAgent();
            if (stripos($agent, 'Blink')) {
                return 'Blink';
            }

            if (stripos($agent, 'Trident')) {
                return 'Trident';
            }

            if (stripos($agent, 'Gecko')) {
                return 'Gecko';
            }

            if (stripos($agent, 'Webkit')) {
                return 'Webkit';
            }

            return '';
        }

        /**
         * 检测是否使用手机访问
         * @return bool
         */
        public static function isMobile(): bool
        {
            if (isset($_SERVER['HTTP_VIA']) && !empty($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
                return true;
            } elseif (isset($_SERVER['HTTP_ACCEPT']) && !empty($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
                return true;
            } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) && !empty($_SERVER['HTTP_X_WAP_PROFILE'])) {
                return true;
            } elseif (isset($_SERVER['HTTP_PROFILE']) && !empty($_SERVER['HTTP_PROFILE'])) {
                return true;
            } elseif (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
                return true;
            }

            return false;
        }

        /**
         * 获取设备类型
         *
         * @return string
         */
        public static function getDeviceType()
        {
            $agent = self::getAgent();
            if (strpos($agent, 'ipad')) {
                return "tablet";
            }

            if (strpos($agent, 'android')) {
                if (!strstr($agent, 'mobile')) {
                    return "tablet";
                }
                return "mobile";
            }

            if (strpos($agent, 'iphone')) {
                return "mobile";
            }

            return 'pc';
        }

        /**
         * 检测是否为微信，包括手机端、PC端
         *
         * @return bool
         */
        public static function isWeChat()
        {
            $agent = self::getAgent();
            if (!empty($agent) && stripos($agent, 'micromessenger') !== false && stripos($agent, 'micromessenger') > 0) {
                return true;
            }

            if (!empty($agent) && stripos($agent, 'windowswechat') !== false && stripos($agent, 'windowswechat') > 0) {
                return true;
            }

            return false;
        }

        /**
         * 检测是否为支付宝
         *
         * @return bool
         */
        public static function isAliPay()
        {
            $agent = self::getAgent();

            return !empty($agent) && stripos($agent, 'alipay') !== false && stripos($agent, 'alipay') > 0;
        }

        /**
         * 检测是否为钉钉
         *
         * @return bool
         */
        public static function isDingTalk()
        {
            $agent = self::getAgent();

            return !empty($agent) && stripos($agent, 'dingtalk') !== false && stripos($agent, 'dingtalk') > 0;
        }

        /**
         * 判断浏览器是否支持webp解析
         *
         * @return bool
         */
        public static function isSupportWebp()
        {
            return isset($_SERVER['HTTP_ACCEPT']) && !empty($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
        }

        /**
         * 获取网络类型 * 暂时仅实现腾讯浏览器,
         *
         * @return string
         */
        public static function getNetType()
        {
            $agent = self::getAgent();
            if (stripos($agent, 'NetType/WIFI')) {
                return 'WIFI';
            }

            if (stripos($agent, 'NetType/2G')) {
                return '2G';
            }

            if (stripos($agent, 'NetType/3G+')) {
                return '3G+';
            }

            if (stripos($agent, 'NetType/4G')) {
                return '4G';
            }

            if (stripos($agent, 'NetType/5G')) {
                return '5G';
            }

            if (stripos($agent, 'NetType/6G')) {
                return '6G';
            }

            return '';
        }

        /**
         * 获取网络运营商
         *
         * @return string
         */
        public static function getIsp()
        {
            return '';
        }

        /**
         * 获得访问者浏览器语言
         *
         * @return string
         */
        public static function getLang()
        {
            $lang = '';
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                if (false !== stripos($lang, "zh-cn")) {
                    $lang = '简体中文';
                } elseif (false !== stripos($lang, "zh")) {
                    $lang = '繁体中文';
                } else {
                    $lang = 'English';
                }
            }

            return strtolower($lang);
        }

        /**
         * 获取IPv4
         *
         * @return string
         */
        public static function getIpvs()
        {
            if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), '')) {
                $ip = getenv('HTTP_CLIENT_IP');
            } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), '')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), '')) {
                $ip = getenv('REMOTE_ADDR');
            } elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], '')) {
                $ip = $_SERVER['REMOTE_ADDR'];
            } else {
                $ip = '';
            }

            return strtolower($ip);
        }

        /**
         * 获取IPv6
         *
         * @return string
         */
        public static function getIpvf()
        {
            return self::getIpvs();
        }

        /**
         * 获取系统信息
         *
         * @return array
         */
        public static function getInfo()
        {
            return array(
                'agent' => self::getAgent(),
                'os' => self::getOs(),
                'type' => self::getDeviceType(),

                'brand' => self::getBrand(),

                'browser' => self::getBrowser(),
                'ismobile' => self::isMobile(),
                'lang' => self::getLang(),
                'newtype' => self::getNetType(),
                'isp' => self::getIsp(),
                'ipvs' => self::getIpvs(),
                'ipvf' => self::getIpvf()
            );
        }
    }