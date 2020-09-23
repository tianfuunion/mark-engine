<?php
    declare (strict_types=1);

    namespace mark\http;

    use finfo;
    use Exception;
    use mark\Mark;
    use mark\system\Os;
    use mark\http\response\HttpResponse;

    /**
     * Curl for php 封装类
     * @author     Mark<mark@tianfu.ink>
     * @site http://www.tianfuunion.cn
     * @time       2019年10月11日 15:27:00
     * @modifyTime 2020年02月28日 00:38:00
     * @modifyTime 2020年07月25日 17:00:00
     *
     * HTTP/1.1协议中共定义了八种方法（有时也叫“动作”）来表明Request-URI指定的资源的不同操作方式：
     * OPTIONS：返回服务器针对特定资源所支持的HTTP请求方法。也可以利用向Web服务器发送"*"的请求来测试服务器的功能性。
     * HEAD：向服务器索要与GET请求相一致的响应，只不过响应体将不会被返回。这一方法可以在不必传输整个响应内容的情况下，就可以获取包含在响应消息头中的元信息。
     * GET：向特定的资源发出请求。注意：GET方法不应当被用于产生“副作用”的操作中。
     * POST：向指定资源提交数据进行处理请求（例如提交表单或者上传文件）。数据被包含在请求体中。POST请求可能会导致新的资源的建立和/或已有资源的修改。
     * PUT：向指定资源位置上传其最新内容。
     * DELETE：请求服务器删除Request-URI所标识的资源。
     * TRACE：回显服务器收到的请求，主要用于测试或诊断。
     * CONNECT：HTTP/1.1协议中预留给能够将连接改为管道方式的代理服务器
     *
     * @todo 注意：如果当该对象多次调用后，返回的结果为最后一次调用时响应的结果（如响应头，响应代码，请求方法）
     *
     * @link PHP CURL参数详解 - https://www.cnblogs.com/g2star/p/3760346.html
     * Class Curl
     *
     * @package    mark\http
     */
    class Curl
    {
        /**@var self */
        private static $instance;

        private $curl;
        private $transfer = true;

        private $url; // 访问的url
        private $method = 'get'; // 访问方式,默认是GET请求

        /**
         * @link https://www.runoob.com/http/http-content-type.html
         * 是否开启请求头
         * @var bool
         */
        private $requestHeaderOut = false;
        /**
         * The headers being sent in the request.
         */
        private $requestHeaders = array();

        /**
         * 是否支持重定向(默认不支持)
         * @see set('location',true)
         * @var bool
         */
        private $location = false;
        /**
         * 最大递归返回的数量
         * @var int
         */
        private $maxredirs = 10;

        private $timeout = 600;
        /**
         * 在发起连接前等待的时间，如果设置为0，则不等待。
         * @var int
         */
        private $connectTimeout = 0;

        private $formData = array();
        private $fileData = array();
        private $upfilesize = 0;

        private $cookie;
        private $outfile;

        private $errno;
        private $errmsg;

        // 响应头
        private $responseCode = 0; // 响应码
        private $responseHeader = 0; // 是否接收响应头
        private $responseHeaderSize = 0; // 响应头大小
        private $responseHeaderContent = ''; // 响应头内容
        /**
         * @var HttpResponse
         */
        private $HttpResponse;

        private $fileName = '';
        private $fileSuffix = '';
        private $filePath = '';

        /**
         * 构造方法
         *
         * Curl constructor.
         */
        private function __construct()
        {
            if (!extension_loaded('curl')) {
                die('未安装Curl模块');
            }
        }

        /**
         * Curl初始化
         *
         * @return $this
         */
        private function initialize(): self
        {
            if (!empty($this->url)) {
                $this->curl = curl_init($this->url) or die('Curl初始化失败');
            } else {
                $this->curl = curl_init() or die('Curl初始化失败');
            }

            return $this;
        }

        /**
         * 创建Facade实例。
         *
         * @static
         * @access protected
         *
         * @param bool $newInstance 是否每次创建新的实例
         * @return self
         */
        public static function getInstance(bool $newInstance = false): self
        {
            if ($newInstance || !self::$instance || empty(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Get 请求
         *
         * @param string $url
         * @param string $type
         *
         * @return $this
         */
        public function get(string $url, $type = 'html')
        {
            $this->url = $url;
            $this->method = 'get';
            switch ($type) {
                case 'json':
                    $this->addHeader('Content-Type', 'application/json');
                    $this->addHeader('Accept', 'application/json');
                    break;
                case 'pdf':
                    $this->addHeader('Content-Type', 'application/pdf');
                    $this->addHeader('Accept', 'application/pdf');
                    break;
                case 'msword':
                    $this->addHeader('Content-Type', 'application/msword');
                    $this->addHeader('Accept', 'application/msword');
                    break;
                case 'stream':
                    $this->addHeader('Content-Type', 'application/octet-stream');
                    $this->addHeader('Accept', 'application/octet-stream');
                    break;

                case 'html':
                    $this->addHeader('Content-Type', 'text/html');
                    $this->addHeader('Accept', 'text/html');
                    break;
                case  'text':
                    $this->addHeader('Content-Type', 'text/plain');
                    $this->addHeader('Accept', 'text/plain');
                    break;
                case  'xml':
                    $this->addHeader('Content-Type', 'text/xml');
                    $this->addHeader('Accept', 'text/xml');
                    break;

                case  'gif':
                    $this->addHeader('Content-Type', 'image/gif');
                    $this->addHeader('Accept', 'image/gif');
                    break;
                case  'jpeg':
                    $this->addHeader('Content-Type', 'image/jpeg');
                    $this->addHeader('Accept', 'image/jpeg');
                    break;
                case  'png':
                    $this->addHeader('Content-Type', 'image/png');
                    $this->addHeader('Accept', 'image/png');
                    break;
                case  'webp':
                    $this->addHeader('Content-Type', 'image/webp');
                    $this->addHeader('Accept', 'image/webp');
                    break;

                case  'mp3':
                    $this->addHeader('Content-Type', 'audio/mp3');
                    $this->addHeader('Accept', 'audio/mp3');
                    break;
                case  'wav':
                    $this->addHeader('Content-Type', 'audio/wav');
                    $this->addHeader('Accept', 'audio/wav');
                    break;

                case  'mp4':
                    $this->addHeader('Content-Type', 'video/mpeg4');
                    $this->addHeader('Accept', 'video/mpeg4');
                    break;

                default:
                    $this->addHeader('Content-Type', 'application/x-www-data-urlencode');
                    $this->addHeader('Accept', 'application/x-www-data-urlencode');
                    break;
            }

            return $this->initialize();
        }

        /**
         * Post 请求，请求数据可不写，也可随后用append()添加上
         *
         * @param string $url
         * @param array $data
         *
         * @return $this
         */
        public function post(string $url, $data = array()): self
        {
            $this->url = $url;
            $this->method = 'post';
            $this->addHeader('Content-Type', 'application/x-www-form-urlencode');
            $this->addHeader('Accept', 'application/x-www-form-urlencode');

            return $this->initialize()->append($data);
        }

        /**
         * 上传文件
         *
         * @param string $url
         *
         * @return $this
         */
        public function upload(string $url): self
        {
            $this->url = $url;
            $this->method = 'upload';
            $this->addHeader('Content-Type', 'multipart/form-data');
            $this->addHeader('Accept', 'multipart/form-data');

            return $this->initialize();
        }

        /**
         * 下载文件
         *
         * @param string $url
         * @param string $savePath
         * @param string $fileName
         * @param string $suffix
         *
         * @return $this|array
         */
        public function download(string $url, $savePath = '', $fileName = '', $suffix = ''): self
        {
            $this->method = 'download';
            $this->addHeader('Content-Type', 'application/octet-stream');
            $this->addHeader('Accept', 'application/octet-stream');

            if (empty(trim($url))) {
                return array('file_name' => '', 'save_path' => '', 'error' => 1);
            }
            $this->url = trim($url);
            // 设置文件保存路径
            $this->setFilePath($savePath);

            //保存文件名
            $this->setFileName($fileName);

            // 文件后缀
            $this->setFileSuffix($suffix);

            return $this;
        }

        /**
         * 设置文件保存路径
         *
         * @param string $filepath
         * @return $this
         */
        public function setFilePath($filepath = ''): self
        {
            if (trim($filepath) != '') {
                $this->filePath = trim($filepath);
            } else {
                $this->filePath = $filepath;
            }
            return $this;
        }

        /**
         * 获取文件路径
         * @return array|string
         */
        public function getFilePath()
        {
            if (file_exists($this->filePath)) {
                return $this->filePath;
            }
            if (
                !file_exists($this->filePath)
                && !mkdir($concurrentDirectory = $this->filePath, 0777, true)
                && !is_dir($concurrentDirectory)) {
                return array('file_name' => '', 'save_path' => '', 'error' => 5);
            }
            return '';
        }

        /**
         * 设置文件名
         *
         * @param string $filename
         * @return $this
         */
        public function setFileName($filename = ''): self
        {
            //保存文件名
            if (trim($filename) != '') {
                $this->fileName = trim($filename);
            }
            return $this;
        }

        /**
         * 获取文件名
         * @param bool $all
         * @return string
         */
        public function getFileName($all = true): string
        {
            if ($this->fileName !== '') {
                return $this->fileName . ($all == true ? '.' . $this->getFileSuffix() : '');
            }
            // 后缀为空则使用文件名后缀
            $filename = pathinfo($this->fileName, PATHINFO_BASENAME);
            if ($filename !== '') {
                return $filename . '.' . $this->getFileSuffix();
            }
            // 文件名后缀为空则使用Url后缀
            $filename = pathinfo($this->url, PATHINFO_BASENAME);
            if (!empty($filename)) {
                return $filename . '.' . $this->getFileSuffix();
            }

            try {
                return 'unknown_' . time() . '_' . random_int(1000, 9999) . '.' . $this->getFileSuffix();
            } catch (Exception $e) {
                return 'unknown_' . time() . '_' . mt_rand(1000, 9999) . '.' . $this->getFileSuffix();
            }
        }

        /**
         * 设置文件后缀
         *
         * @param string $suffix
         * @return $this
         */
        public function setFileSuffix($suffix = ''): self
        {
            if (trim($suffix) !== '') {
                $this->fileSuffix = trim($suffix);
            }

            return $this;
        }

        /**
         *获取文件后缀
         * @return string|string[]
         */
        public function getFileSuffix()
        {
            // 后缀判断
            if ($this->fileSuffix != '') {
                return $this->fileSuffix;
            }
            // 后缀为空则使用文件名后缀
            $suffix = pathinfo($this->fileName, PATHINFO_EXTENSION);
            if ($suffix !== '') {
                return $suffix !== 'jpeg' ? $suffix : 'jpg';
            }
            // 文件名后缀为空则使用Url后缀
            $suffix = pathinfo($this->url, PATHINFO_EXTENSION);
            if ($suffix !== '') {
                return $suffix !== 'jpeg' ? $suffix : 'jpg';
            }
            $imginfo = getimagesize($this->url);
            $suffix = trim(strrchr($imginfo['mime'], '/'), '/');

            return $suffix !== 'jpeg' ? $suffix : 'jpg';
        }

        /**
         * Put 请求
         *
         * @param string $url
         *
         * @return $this
         */
        public function put(string $url): self
        {
            $this->url = $url;

            return $this;
        }

        /**
         * Delete 请求
         *
         * @param string $url
         *
         * @return $this
         */
        public function delete(string $url): self
        {
            $this->url = $url;

            return $this;
        }

        public function ftp($url): self
        {
            $this->url = $url;

            return $this;
        }

        /**
         * Curl 请求结果以Json的形式输出
         *
         * @return string
         */
        public function toJson()
        {
            $json = $this->execute();
            if ($this->getResponseCode() == 200) {
                if (is_string($json)) {
                    return $json;
                }
                if (is_array($json)) {
                    return json_encode($json, JSON_UNESCAPED_UNICODE);
                }
                if (is_object($json)) {
                    return json_encode(json_decode(json_encode($json), true), JSON_UNESCAPED_UNICODE);
                }
            }

            return $json;
        }

        /**
         * Curl 请求结果以数组的形式输出
         *
         * @return array
         */
        public function toArray()
        {
            $json = $this->execute();
            if ($this->getResponseCode() == 200) {
                if (is_string($json)) {
                    return json_decode($json, true);
                }
                if (is_array($json) === true) {
                    return $json;
                }
                if (is_object($json)) {
                    return json_decode(json_encode($json), true);
                }
            }

            return array();
        }

        /**
         * 添加请求数据，后添加会覆盖之后添加的数据
         *
         * @param $data
         *
         * @return $this
         */
        public function append($data): self
        {
            if (!empty($data)) {
                $this->formData[] = $data;
                // $this->formData = array_merge($this->formData, $data);
            }

            return $this;
        }

        /**
         * 添加请求数据，后添加会覆盖之后添加的数据
         *
         * @param $key
         * @param $value
         * @return $this
         */
        public function appendData($key, $value): self
        {
            if (!empty($key)) {
                $this->formData[$key] = $value;
            }

            return $this;
        }

        /**
         * 添加请求Json数据，后添加会覆盖之后添加的数据
         *
         * @param $value
         *
         * @return $this
         */
        public function appendJson($value): self
        {
            if (!empty($value)) {
                $this->formData = $value;
            }

            return $this;
        }

        public function appendRecursive($data): self
        {
            if (!empty($data)) {
                $this->formData = array_merge_recursive($this->formData, $data);
            }

            return $this;
        }

        public function appendPush($key, $field, $value): self
        {
            if (!empty($value)) {
                $this->formData[$key][$field] = $value;
            }

            return $this;
        }

        public function push($key, $value): self
        {
            if (!empty($value)) {
                $this->formData[$key][] = $value;
            }

            return $this;
        }

        /**
         * 添加文件
         *
         * @param $file
         *
         * @return $this
         */
        public function appendFile($file): self
        {
            return $this->appendFiles('file', $file);
        }

        /**
         * 添加多个文件
         *
         * @param $key
         * @param $file
         *
         * @return $this
         */
        public function appendFiles($key, $file): self
        {
            if (file_exists(realpath($file))) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file(realpath($file));

                // Log::info('Curl::appendFiles(文件存在)' . $key . ' ' . json_encode($file, JSON_UNESCAPED_UNICODE) . ' Mime:' . $mime);
                $this->fileData[] = curl_file_create(realpath($file), $mime, basename($file));
                $this->upfilesize += filesize($file);
            } else {
                // Log::info('Curl::appendFiles(文件不存在)' . $key . ' ' . json_encode($file, JSON_UNESCAPED_UNICODE));
            }

            /*
            if(!empty($key)){
            // $this->formData[$key] = $file;

            // 从php5.5开始,反对使用"@"前缀方式上传,可以使用CURLFile替代;
            // 据说php5.6开始移除了"@"前缀上传的方式
            if (class_exists("\CURLFile")){
            if(array_key_exists($key, $this->formData)){
            $newArr = array();
            array_push($newArr, $this->formData[$key]);
            // array_push($newArr, new CURLFile(realpath($file)));
            array_push($newArr, new CURLFile($file));
            // array_push($newArr, curl_file_create($file));
            $this->upfilesize+=filesize($file);

            $this->formData[$key] = $newArr;
            }else{
            $this->formData[$key] = new CURLFile(realpath($file));
            $this->upfilesize+=filesize(realpath($file));
            }
            // 禁用"@"上传方法,这样就可以安全的传输"@"开头的参数值
            // curl_setopt($this->getCurl(), CURLOPT_SAFE_UPLOAD, false);
            }else{
            $this->formData[$key] = "@".realpath($file);
            $this->upfilesize+=filesize($file);
            }
            }
            */

            return $this;
        }

        /**
         * 获取数据
         *
         * @return array
         */
        public function getFormData(): array
        {
            return $this->formData;
        }

        /**
         * 2、设置选项
         *
         * @param $option
         * @param $value
         *
         * @return $this
         */
        public function set($option, $value): self
        {
            $this->$option = $value;
            // curl_setopt($this->getCurl(), $option, $value);

            return $this;
        }

        /**
         * 提交执行
         * 过时的，可用execute() 代替
         *
         * @return array|bool|false|string
         * @deprecated
         * @see Curl::execute();
         */
        public function commit()
        {
            return $this->execute();
        }

        /**
         * 3、执行CURL * execute
         *
         * @return array|bool|false|string
         */
        public function execute()
        {
            // 设置请求地址
            curl_setopt($this->getCurl(), CURLOPT_URL, $this->url);
            //设置请求头(可有可无)
            curl_setopt($this->getCurl(), CURLOPT_HTTPHEADER, $this->getHeader());

            curl_setopt($this->getCurl(), CURLOPT_USERAGENT, Os::getAgent() . ' MarK/' . Mark::VERSION);

            //设置cURL允许执行的最长秒数。
            if ($this->timeout > 0) {
                curl_setopt($this->getCurl(), CURLOPT_TIMEOUT, $this->timeout);
            }
            if ($this->connectTimeout > 0) {
                curl_setopt($this->getCurl(), CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
            }

            // 忽略HTTPS的安全证书
            if (1 == strpos('$' . $this->url, 'https://')) {
                curl_setopt($this->getCurl(), CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($this->getCurl(), CURLOPT_SSL_VERIFYHOST, false);
            }

            // 让CURL支持页面连接跳转
            if ($this->location) {
                curl_setopt($this->getCurl(), CURLOPT_FOLLOWLOCATION, true);
            }
            // 启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器,使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
            if ($this->maxredirs) {
                // 指定最多的HTTP重定向的数量,这个选项是和CURLOPT_FOLLOWLOCATION一起使用的
                curl_setopt($this->getCurl(), CURLOPT_MAXREDIRS, 10);
            }

            // 添加来源referer
            curl_setopt($this->getCurl(), CURLOPT_REFERER, getRequestUrl());

            // 当根据Location:重定向时,自动设置header中的Referer:信息。
            curl_setopt($this->getCurl(), CURLOPT_AUTOREFERER, true);
            // curl_setopt($this->getCurl(), CURLOPT_REFERER, "http://XXX"); // 在HTTP请求头中"Referer: "的内容。

            if ($this->cookie) {
                curl_setopt($this->getCurl(), CURLOPT_COOKIE, $this->cookie);
                curl_setopt($this->getCurl(), CURLOPT_COOKIEFILE, 'cookiefile');
                curl_setopt($this->getCurl(), CURLOPT_COOKIEJAR, 'cookiefile');
                curl_setopt($this->getCurl(), CURLOPT_COOKIESESSION, $this->cookie);
            }

            //至关重要，CURLINFO_HEADER_OUT选项可以拿到请求头信息
            if ($this->requestHeaderOut) {
                curl_setopt($this->getCurl(), CURLINFO_HEADER_OUT, true);
            }

            if ($this->responseHeader) {
                //设置响应头信息是否返回
                curl_setopt($this->getCurl(), CURLOPT_HEADER, $this->responseHeader);
            }

            // 将curl_exec()获取的信息以文件流的形式返回,而不是直接输出。(自定义默认不输出)
            if ($this->transfer) {
                curl_setopt($this->getCurl(), CURLOPT_RETURNTRANSFER, $this->transfer);
            }

            switch ($this->getMethod()) {
                case 'get':
                    //设置为get请求
                    curl_setopt($this->getCurl(), CURLOPT_HTTPGET, true);

                    // 重新为Url添加Get参数
                    if (!empty($this->url) && !empty($this->formData)) {
                        $this->url .= (strpos($this->url, '?') === false ? '?' : '&') . http_build_query($this->formData);
                        curl_setopt($this->getCurl(), CURLOPT_URL, $this->url);
                    }
                    break;
                case 'post':
                    //设置为post请求
                    curl_setopt($this->getCurl(), CURLOPT_POST, true);

                    { // TODO：待删除代码块
                        $parse_url = parse_url($this->url);
                        if (!empty($parse_url['query'])) {
                            $queryParts = explode('&', $parse_url['query']);
                            $params = array();
                            if (!empty($queryParts)) {
                                foreach ($queryParts as $param) {
                                    $item = explode('=', $param);
                                    $params[$item[0]] = $item[1];
                                }
                                // $this->formData = array_merge($params, $this->formData);
                            }
                        }
                    }

                    if (!empty($this->formData)) {
                        // curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, $this->formData);
                        curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, http_build_query($this->formData));
                    }
                    // Log::info('Curl:Execute(Post.formData)' . json_encode($this->formData, JSON_UNESCAPED_UNICODE));
                    break;
                case 'upload':
                    // Log::debug('Curl:Execute(UpLoad.formData)' . json_encode($this->formData, JSON_UNESCAPED_UNICODE));

                    // curl_setopt($this->getCurl(), CURLOPT_FOLLOWLOCATION, true);
                    // curl_setopt($this->getCurl(), CURLOPT_MAXREDIRS, 3);

                    curl_setopt($this->getCurl(), CURLOPT_CUSTOMREQUEST, 'POST');
                    // curl_setopt($this->getCurl(), CURLOPT_CONNECTTIMEOUT, 60);
                    /**
                     * 接收通过浏览器上传的文件
                     *
                     */
                    curl_setopt($this->getCurl(), CURLOPT_POST, 1); //设置为post请求

                    // curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, http_build_query($this->formData));
                    // curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, $this->formData);
                    // curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, $this->fileData);

                    // curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, array_merge($this->fileData, http_build_query($this->formData)));
                    curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, array_merge($this->fileData, $this->formData));

                    // curl_setopt($this->getCurl(), CURLOPT_PUT, 1);
                    // curl_setopt($this->getCurl(), CURLOPT_INFILE, realpath($file));
                    // curl_setopt($this->getCurl(), CURLOPT_INFILE, $this->formData);
                    // curl_setopt($this->getCurl(), CURLOPT_INFILESIZE, $this->upfilesize);

                    // 将传输结果作为curl_exec的返回值,而不是直接输出 //返回内容，不输出
                    curl_setopt($this->getCurl(), CURLOPT_RETURNTRANSFER, true);
                    // 启用时允许文件传输
                    // curl_setopt($this->getCurl(), CURLOPT_UPLOAD, true);

                    /*
                    if (isset($this->read_stream)) {
                    if (!isset($this->read_stream_size) || $this->read_stream_size < 0) {
                    throw new RequestCore_Exception("The stream size for the streaming upload cannot be determined.");
                    }
                    curl_setopt($this->getCurl(), CURLOPT_INFILESIZE, $this->read_stream_size);
                    curl_setopt($this->getCurl(), CURLOPT_UPLOAD, true);
                    } else {
                    curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, $this->formData);
                    }
                    */
                    break;
                case 'put':
                    //定义请求类型
                    curl_setopt($this->getCurl(), CURLOPT_CUSTOMREQUEST, 'put');
                    //定义提交的数据
                    curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, http_build_query($this->formData));
                    break;
                case 'download':
                    //定义请求类型
                    // curl_setopt($this->getCurl(), CURLOPT_CUSTOMREQUEST, "put");
                    //定义提交的数据
                    // curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, http_build_query($this->formData));

                    // 将传输结果作为curl_exec的返回值,而不是直接输出
                    curl_setopt($this->getCurl(), CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($this->getCurl(), CURLOPT_VERBOSE, 1);

                    // curl_setopt($this->getCurl(), CURLOPT_USERPWD, "peter.zhou:123456");
                    // $this->outfile = fopen("desc.txt", "wb");
                    // $this->outfile = fopen($this->filePath . "/" . $this->fileName, "wb");
                    // curl_setopt($this->getCurl(), CURLOPT_FILE, $this->outfile);

                    // curl_setopt($this->getCurl(), CURLOPT_FOLLOWLOCATION, 1);
                    break;
                default:
                    // Log::error('Curl:Execute()' . $this->method);
                    break;
            }

            $result = curl_exec($this->getCurl());
            $this->responseCode = curl_getinfo($this->getCurl(), CURLINFO_HTTP_CODE);

            if ($result === false) {
                $this->errmsg = curl_error($this->getCurl());

                return 'Curl Error：' . $this->errmsg;
            }

            // 检查是否有错误发生
            if ($this->errno = curl_errno($this->getCurl())) {
                $this->errmsg = curl_strerror($this->errno);
                // Log::info('Curl：ErrNo：' . $this->errno . ' ErrMsg：' . $this->errmsg);

                return 'Curl Error：' . curl_strerror($this->errno);
            }

            // 检查是否需要获取响应头
            if ($this->responseHeader) {
                // 获得响应结果里的：头大小
                $this->responseHeaderSize = $headerSize = curl_getinfo($this->getCurl(), CURLINFO_HEADER_SIZE);
                // 根据头大小去获取头信息内容
                $this->responseHeaderContent = substr($result, 0, $headerSize);

                return substr($result, $headerSize);
            }
            if ($this->method === 'download') {
                // 保存文件到制定路径
                $bytes = file_put_contents($this->getFilePath() . '/' . $this->getFileName(), $result);
                if ($bytes) {
                    return array(
                        'bytes' => $bytes,
                        'url' => $this->url,
                        'path' => $this->getFilePath(),
                        'name' => $this->getFileName(),
                        'suffix' => $this->getFileSuffix()
                    );
                }
                $this->errmsg[] = '保存文件失败';

                return false;
            }

            return $result;
        }

        /**
         * 设置请求字符集
         *
         * @param string $charset
         * @return $this
         */
        public function setCharset($charset = 'utf-8'): self
        {
            $this->addHeader('charset', $charset);

            return $this;
        }


        /**
         * 一个用来设置HTTP头字段的数组。使用如下的形式的数组进行设置
         * Add a custom HTTP header to the cURL request.
         *
         * @param string $key (Required) The custom HTTP header to set.
         * @param mixed $value (Required) The value to assign to the custom HTTP header.
         * @return $this A reference to the current instance.
         */
        public function addHeader($key, $value): self
        {
            $this->requestHeaders[$key] = $value;

            return $this;
        }

        /**
         * Remove an HTTP header from the cURL request.
         *
         * @param string $key (Required) The custom HTTP header to set.
         * @return $this A reference to the current instance.
         */
        public function removeHeader($key): self
        {
            if (isset($this->requestHeaders[$key])) {
                unset($this->requestHeaders[$key]);
            }
            return $this;
        }

        /**
         * Initialize headers
         *
         * @return array
         */
        private function generateHeaders(): array
        {
            $options = array(
                'Content-type' => Os::getAccept(),
                'Accept ' => Os::getAccept(),
                'Charset' => 'utf-8',
                'Cache-control' => 'no-cache',
                'Date' => gmdate('D, d M Y H:i:s \G\M\T'),
                'Pragma' => 'no-cache'
            );

            $headers = array_merge($options, $this->requestHeaders);
            return $headers;
        }

        /**
         * 获取HTTP头字段的数组
         *
         * @return array
         */
        private function getHeader(): array
        {
            $this->requestHeaders = $this->generateHeaders();

            $temp_headers = array();
            foreach ($this->requestHeaders as $k => $v) {
                $temp_headers[] = $k . ': ' . $v;
            }
            $temp_headers[] = 'Expect:';
            return $temp_headers;
        }

        /**
         * 请求的响应码
         *
         * @return int
         * @todo 注意：如果当该对象多次调用后，返回的结果为最后一次调用时请求的响应码
         */
        public function getResponseCode(): int
        {
            if (intval($this->responseCode) == 0) {
                $this->execute();
            }
            return intval($this->responseCode);
        }

        /**
         * 获取请求响应头
         *
         * @param bool $complete
         * @return string|string[]
         * @todo 注意：如果当该对象多次调用后，返回的结果为最后一次调用时请求的响应头
         */
        public function getResponseHeader(bool $complete = false)
        {
            if ($this->getResponseCode() == 0) {
                $this->execute();
            }
            if (!empty($this->responseHeaderContent) && $complete) {
                $header = explode("\r\n", $this->responseHeaderContent);
                if ($header !== false) {
                    return $header;
                }
            }
            return $this->responseHeaderContent;
        }

        /**
         * 注意：如果当该对象多次调用后，返回的结果为最后一次调用时请求的响应头大小
         *
         * @return int
         */
        public function getResponseHeaderSize(): int
        {
            if ($this->getResponseCode() == 0) {
                $this->execute();
            }
            return $this->responseHeaderSize;
        }

        /**
         * 获取一个cURL连接资源句柄的信息。
         *
         * @return mixed
         */
        public function getInfo()
        {
            if ($this->getResponseCode() == 0) {
                $this->execute();
            }
            return curl_getinfo($this->getCurl());
        }

        /**
         * 获取Curl的实例化对象
         *
         * @return false|resource
         */
        public function getCurl()
        {
            if (empty($this->curl)) {
                if (!empty($this->url)) {
                    $this->curl = curl_init($this->url) or die('Curl初始化失败');
                } else {
                    $this->curl = curl_init() or die('Curl初始化失败');
                }
            }
            return $this->curl;
        }

        /**
         * 返回一个保护当前会话最近一次错误的字符串。
         *
         * @return string
         */
        public function getError()
        {
            if ($this->getResponseCode() == 0) {
                $this->execute();
            }
            return curl_error($this->getCurl());
        }

        /**
         * 注意：如果当该对象多次调用后，返回的结果为最后一次调用时请求的方法
         *
         * @return string
         */
        public function getMethod(): string
        {
            return strtolower($this->method);
        }

        /**
         * 设置自定义响应回调
         *
         * @param HttpResponse $HttpResponse
         * @return $this
         * @todo 待完善
         */
        public function setHttpResponse(HttpResponse $HttpResponse): self
        {
            $this->HttpResponse = $HttpResponse;
            return $this;
        }

        /**
         * 4、关闭cURL资源,并且释放系统资源
         */
        public function __destruct()
        {
            $this->close();
        }

        /**
         * 关闭curl句柄
         */
        private function close()
        {
            if (!empty($this->outfile)) {
                fclose($this->outfile);
            }
            if (!empty($this->curl)) {
                curl_close($this->curl);
            }
        }

        /**
         * 设置中间传递数据
         *
         * @param string $key
         * @param $value
         * @return $this
         */
        public function __set(string $key, $value): self
        {
            $this->$key = $value;

            return $this;
        }

        /**
         * 获取中间传递数据的值
         *
         * @param string $key
         * @return mixed
         */
        public function __get(string $key)
        {
            return $this->$key;
        }

        /**
         * 检测中间传递数据的值
         *
         * @param string $key
         *
         * @return boolean
         */
        public function __isset(string $key): bool
        {
            return isset($key);
        }

        /**
         * @param $method
         * @param $params
         *
         * @return $this
         */
        public function __call($method, $params)
        {
            list($key, $value) = $params;
            $this->$key = $value;

            return $this;
        }

        /**
         * 获取当前Facade对应类名
         *
         * @access protected
         * @return string
         */
        protected static function getFacadeClass(): string
        {
            return 'mark\http\Curl';
        }

        /**
         * 始终创建新的对象实例
         *
         * @var bool
         */
        protected static $alwaysNewInstance;

        /**
         * 创建Facade实例
         *
         * @param bool $newInstance 是否每次创建新的实例
         *
         * @return Curl
         */
        protected static function createFacade(bool $newInstance = false)
        {
            $class = self::getFacadeClass() ?: 'mark\http\Curl';

            if (self::$alwaysNewInstance) {
                $newInstance = true;
            }

            if ($newInstance || !self::$instance) {
                self::$instance = new $class();
            }

            return self::$instance;
        }

        /**
         * 调用静态方法
         *
         * @param $method
         * @param $params
         *
         * @return mixed
         */
        public static function __callStatic($method, $params)
        {
            switch (strtolower($method)) {
                case 'options':
                case 'head':
                case 'get':
                case 'post':
                case 'put':
                case 'delete':
                case 'trace':
                case 'connect':
                    return (new self())->$method($params);
                    break;
            }

            try {
                return call_user_func_array([self::createFacade(), $method], $params);
            } catch (Exception $e) {
            }

            return call_user_func_array($method, $params);
        }

    }