<?php

    declare (strict_types=1);

    namespace mark\response;

    use think\Response;

    use think\facade\Request;
    use think\facade\Config;
    use think\facade\View;
    use think\facade\Db;

    /**
     * Class Responsive Response
     *
     * @package mark\response
     */
    class Responsive extends Response
    {

        /**
         * 自适应响应输出
         *
         * @since   http://tools.jb51.net/table/http_status_code
         * @version HTTP Status Code 2.0
         *
         * @param mixed $data
         * @param int $code
         * @param string $status
         * @param string $msg
         *
         * @param string $type
         *
         * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\View
         */
        public static function display($data, $code = 200, $status = '', $msg = '', $type = 'html')
        {
            switch ($code) {
                case 100:
                    $status = !empty($status) ? $status : 'Continue';
                    $msg = !empty($msg) ? $msg : '请继续发送请求';
                    break;
                case 101:
                    $status = !empty($status) ? $status : 'Switching Protocols';
                    $msg = !empty($msg) ? $msg : '请使用新的HTTP版本协议';
                    break;
                case 102:
                    $status = !empty($status) ? $status : 'Processing';
                    $msg = !empty($msg) ? $msg : '请求持续处理中';
                    break;
                case 200:
                    $status = !empty($status) ? $status : 'OK';
                    $msg = !empty($msg) ? $msg : '请求成功';
                    break;
                case 201:
                    $status = !empty($status) ? $status : 'Created';
                    $msg = !empty($msg) ? $msg : '请求成功并创建了新的资源';
                    break;
                case 202:
                    $status = !empty($status) ? $status : 'Accepted';
                    $msg = !empty($msg) ? $msg : '已接受请求，但尚未处理';
                    break;
                case 203:
                    $status = !empty($status) ? $status : 'Non-Authoritative Information';
                    $msg = !empty($msg) ? $msg : '请求成功，信息未授权';
                    break;
                case 204:
                    $status = !empty($status) ? $status : 'No Content';
                    $msg = !empty($msg) ? $msg : '服务器成功处理，但未返回内容';
                    break;
                case 205:
                    $status = !empty($status) ? $status : 'Reset Content';
                    $msg = !empty($msg) ? $msg : '处理成功，请重置视图';
                    break;
                case 206:
                    $status = !empty($status) ? $status : 'Partial Content';
                    $msg = !empty($msg) ? $msg : '处理成功了部分请求';
                    break;
                case 207:
                    $status = !empty($status) ? $status : 'Multi-Status ';
                    $msg = !empty($msg) ? $msg : '多状态响应';
                    break;

                // 3开头的状态码
                case 300:
                    $status = !empty($status) ? $status : 'Multiple Choices';
                    $msg = !empty($msg) ? $msg : '请选择合适的操作';
                    break;
                case 301:
                    $status = !empty($status) ? $status : 'Moved Permanently';
                    $msg = !empty($msg) ? $msg : '请求的资源已被永久的移动到新的位置';
                    break;
                case 302:
                    $status = !empty($status) ? $status : 'Found';
                    $msg = !empty($msg) ? $msg : '请求的资源已被临时移动';
                    break;
                case 303:
                    $status = !empty($status) ? $status : 'See Other';
                    $msg = !empty($msg) ? $msg : '请求自动跟踪跳转';
                    break;
                case 304:
                    $status = !empty($status) ? $status : 'Not Modified';
                    $msg = !empty($msg) ? $msg : '请求内容未修改';
                    break;
                case 305:
                    $status = !empty($status) ? $status : 'Use Proxy';
                    $msg = !empty($msg) ? $msg : '请使用代理访问资源';
                    break;
                case 306:
                    $status = !empty($status) ? $status : 'Unused';
                    $msg = !empty($msg) ? $msg : '未使用';
                    break;
                case 307:
                    $status = !empty($status) ? $status : 'Temporary Redirect';
                    $msg = !empty($msg) ? $msg : '请求临时重定向';
                    break;

                // 4开头的状态码
                case 400:
                    $status = !empty($status) ? $status : 'Bad Request';
                    $msg = !empty($msg) ? $msg : '请求参数错误';
                    break;
                case 401:
                    $status = !empty($status) ? $status : 'Unauthorized';
                    $msg = !empty($msg) ? $msg : '用户未登录';
                    break;
                case 402:
                    $status = !empty($status) ? $status : 'Insufficient authority';
                    $msg = !empty($msg) ? $msg : '权限不足，无法访问该页面';
                    break;
                case 403:
                    $status = !empty($status) ? $status : 'Forbidden';
                    $msg = !empty($msg) ? $msg : '服务器拒绝执行此请求';
                    break;
                case 404:
                    $status = !empty($status) ? $status : 'Not Found';
                    $msg = !empty($msg) ? $msg : '请求失败，您所请求的资源无法找到';
                    break;
                case 405:
                    $status = !empty($status) ? $status : 'Method Not Allowed';
                    $msg = !empty($msg) ? $msg : '请求中的方法已被禁止';
                    break;
                case 406:
                    $status = !empty($status) ? $status : 'Not Acceptable';
                    $msg = !empty($msg) ? $msg : '无法根据请求的内容特性完成请求';
                    break;
                case 407:
                    $status = !empty($status) ? $status : 'Proxy Authentication Required';
                    $msg = !empty($msg) ? $msg : '用户身份未经授权认证';
                    break;
                case 408:
                    $status = !empty($status) ? $status : 'Request Time-out';
                    $msg = !empty($msg) ? $msg : '请求超时，请重新发起请求';
                    break;
                case 409:
                    $status = !empty($status) ? $status : 'Conflict';
                    $msg = !empty($msg) ? $msg : '请求无法完成，资源发生了冲突';
                    break;
                case 410:
                    $status = !empty($status) ? $status : 'Gone';
                    $msg = !empty($msg) ? $msg : '请求资源不可用';
                    break;
                case 411:
                    $status = !empty($status) ? $status : 'Content Length Required';
                    $msg = !empty($msg) ? $msg : '无效的请求长度';
                    break;
                case 412:
                    $status = !empty($status) ? $status : 'Precondition Failed';
                    $msg = !empty($msg) ? $msg : '错误的请求条件';
                    break;
                case 413:
                    $status = !empty($status) ? $status : 'Request Entity Too Large';
                    $msg = !empty($msg) ? $msg : '拒绝请求，负载过大';
                    break;
                case 414:
                    $status = !empty($status) ? $status : 'Request-URI Too Large';
                    $msg = !empty($msg) ? $msg : '拒绝请求，Url过长';
                    break;
                case 415:
                    $status = !empty($status) ? $status : 'Unsupported Media Type';
                    $msg = !empty($msg) ? $msg : '无效的媒体格式';
                    break;
                case 416:
                    $status = !empty($status) ? $status : 'Requested range not satisfiable';
                    $msg = !empty($msg) ? $msg : '无效的请求范围';
                    break;
                case 417:
                    $status = !empty($status) ? $status : 'Expectation Failed';
                    $msg = !empty($msg) ? $msg : '无效的Expect信息';
                    break;
                case 419:
                    $status = !empty($status) ? $status : 'User account is blocked';
                    $msg = !empty($msg) ? $msg : '用户账号被冻结';
                    break;
                case 421:
                    $status = !empty($status) ? $status : ' Too many network connections';
                    $msg = !empty($msg) ? $msg : '网络连接过多';
                    break;
                case 422:
                    $status = !empty($status) ? $status : 'Unprocessable Entity';
                    $msg = !empty($msg) ? $msg : '无法处理的实体';
                    break;
                case 423:
                    $status = !empty($status) ? $status : 'Locked';
                    $msg = !empty($msg) ? $msg : '资源已被锁定';
                    break;
                case 424:
                    $status = !empty($status) ? $status : 'Failed Dependency';
                    $msg = !empty($msg) ? $msg : '请求失败，依赖错误';
                    break;
                case 426:
                    $status = !empty($status) ? $status : 'Upgrade Required';
                    $msg = !empty($msg) ? $msg : '请使用加密协议访问';
                    break;
                case 429:
                    $status = !empty($status) ? $status : 'Too Many Requests';
                    $msg = !empty($msg) ? $msg : '请求超载';
                    break;
                case 444:
                    $status = !empty($status) ? $status : 'No Response';
                    $msg = !empty($msg) ? $msg : '请求无响应';
                    break;
                case 449:
                    $status = !empty($status) ? $status : 'Please try again later';
                    $msg = !empty($msg) ? $msg : '请在执行完操作后进行重试。';
                    break;

                // 5开头的状态码
                case 500:
                    $status = !empty($status) ? $status : 'Internal Server Error';
                    $msg = !empty($msg) ? $msg : '服务异常';
                    break;
                case 501:
                    $status = !empty($status) ? $status : 'Not Implemented';
                    $msg = !empty($msg) ? $msg : '无法识别的请求';
                    break;
                case 502:
                    $status = !empty($status) ? $status : 'Bad Gateway';
                    $msg = !empty($msg) ? $msg : '无效的网关请求';
                    break;
                case 503:
                    $status = !empty($status) ? $status : 'Service Unavailable';
                    $msg = !empty($msg) ? $msg : '系统维护，暂停服务';
                    break;
                case 504:
                    $status = !empty($status) ? $status : 'Gateway Time-out';
                    $msg = !empty($msg) ? $msg : '网关请求超时，请稍后重试';
                    break;
                case 505:
                    $status = !empty($status) ? $status : 'HTTP Version not supported';
                    $msg = !empty($msg) ? $msg : '请求协议不支持';
                    break;
                case 506:
                    $status = !empty($status) ? $status : 'Service internal negotiation error';
                    $msg = !empty($msg) ? $msg : '服务内部配置错误';
                    break;
                case 507:
                    $status = !empty($status) ? $status : 'Insufficient Storage';
                    $msg = !empty($msg) ? $msg : '系统空间不足';
                    break;
                case 508:
                    $status = !empty($status) ? $status : 'Loop Detected';
                    $msg = !empty($msg) ? $msg : '检测到循环';
                    break;
                case 509:
                    $status = !empty($status) ? $status : 'Bandwidth Limit Exceeded';
                    $msg = !empty($msg) ? $msg : '服务器达到带宽限制';
                    break;
                case 510:
                    $status = !empty($status) ? $status : 'Not Extended';
                    $msg = !empty($msg) ? $msg : '无效资源策略';
                    break;
                default :
                    break;
            }

            $response = array('data' => $data, 'code' => $code, 'status' => $status, 'msg' => $msg);

            if (is_json() || $type === 'json') {
                return json($response);
            }
            if (is_pjax() || $type === 'pjax') {
                return jsonp($response);
            }

            if (is_ajax() || $type === 'ajax') {
                return json($response);
            }

            if (Request::isPost() || $type === 'post') {
                return response($response);
            }
            if (Request::isGet() || $type === 'get') {
                View::assign('response', $response);

                $template = Config::get('app.exception_tpl', '');
                if (!empty($template) && file_exists($template)) {
                    return view($template);
                }
            }

            return response(json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        /**
         * 响应允许跨域
         *
         * @param \think\Response $response
         * @param array $domain
         * @param string $methods
         * @param string $headers
         * @return \think\Response
         * @todo 临时使用从数据库获取Allow-Origin，随后在调用处添加
         */
        public static function allowCrossDomain(Response $response, $domain = array(), $methods = 'GET, POST', $headers = '')
        {
            // 检测是否跨域
            if (Request::has('origin', 'header', true)) {
                if (empty($domain)) {
                    $domain = Db::name('app')
                        ->where('status', '=', 1)
                        ->cache('allowCrossDomain', Config::get('session.expire', 1440))
                        ->column('host');
                }
                $origin = parse_url(Request::header('origin', ''), PHP_URL_HOST);

                if (!empty($origin) && $origin !== false && !empty($domain) && in_array($origin, $domain)) {
                    $scheme = parse_url(Request::header('origin', ''), PHP_URL_SCHEME);
                    $scheme = !empty($scheme) ? $scheme : 'http';

                    // $response->header(["Access-Control-Allow-Origin" => "*"]);
                    $response->header(['Access-Control-Allow-Origin' => $scheme . '://' . $origin]);

                    $response->header(['Access-Control-Allow-Credentials' => 'true']); // 设置是否允许发送 cookies

                    $response->header(['Access-Control-Expose-Headers' => '*']);

                    $response->header(['Access-Control-Allow-Methods' => $methods]);

                    // $response->header(["Access-Control-Allow-Headers" => "*"]);
                    $response->header(['Access-Control-Allow-Headers' => 'X-Requested-With,X_Requested_With,Content-Type,token']);
                }
            }

            return $response;
        }

    }