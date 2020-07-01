<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
    declare (strict_types=1);

    namespace mark\response;

    use think\Cookie;
    use think\Request;
    use think\Response;
    use think\Exception;
    use Imagick;

    /**
     * Jsonp Response
     */
    class Image extends Response
    {

        protected $expire = 360;
        protected $mimeType;

        public static function display($data = '', int $code = 200)
        {
            $cookie = new Cookie(new Request());

            return new self($cookie, $data, $code);
        }

        private function __construct(Cookie $cookie, $data = '', int $code = 200)
        {
            $this->cookie = $cookie;
            $this->init($data, $code);
        }

        /**
         * 处理数据
         *
         * @access protected
         *
         * @param mixed $data 要处理的数据
         *
         * @return mixed
         * @throws \Exception
         */
        protected function output($data)
        {

            if ($data == '' || $data == null || empty($data)) {
                throw new Exception('file not exists:' . $data);
            }

            ob_end_clean();

            $mimeType = $this->getMimeType($data);

            $this->header['Pragma'] = 'public';

            $this->header['Content-Type'] = $mimeType ?: 'image/jpg';

            $this->header['Cache-control'] = 'max-age=' . $this->expire;
            $this->header['Content-Length'] = $this->getImageLength($data);
            // $this->header['Content-Transfer-Encoding'] = 'binary';
            $this->header['Expires'] = gmdate('D, d M Y H:i:s', time() + $this->expire) . ' GMT';

            $this->lastModified(gmdate('D, d M Y H:i:s', time()) . ' GMT');

            return $data;
        }

        private $isContent = false;

        /**
         * 设置是否为内容 必须配合mimeType方法使用
         *
         * @access public
         *
         * @param bool $content
         *
         * @return $this
         */
        public function isContent(bool $content = true)
        {
            $this->isContent = $content;

            return $this;
        }

        /**
         * 设置有效期
         *
         * @access public
         *
         * @param integer $expire 有效期
         *
         * @return $this
         */
        public function expire(int $expire)
        {
            $this->expire = $expire;

            return $this;
        }

        /**
         * 设置文件类型
         *
         * @access public
         *
         * @param string $mimeType
         *
         * @return $this
         */
        public function setMimeType(string $mimeType)
        {
            $this->mimeType = $mimeType;

            return $this;
        }

        /**
         * 获取文件类型信息
         *
         * @param Imagick $imagick
         * @return string
         */
        protected function getMimeType(Imagick $imagick): string
        {
            if (!empty($this->mimeType)) {
                return $this->mimeType;
            }
            if ($imagick !== null && $imagick instanceof Imagick) {
                return $imagick->getImageMimeType();
            }

            return 'image/jpg';
        }

        protected function getImageLength($imagick)
        {
            if ($imagick != null && !empty($imagick) && $imagick instanceof Imagick) {
                return $imagick->getImageLength();
            }

            return 0;
        }

    }
