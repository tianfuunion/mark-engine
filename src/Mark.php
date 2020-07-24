<?php
    declare (strict_types=1);

    namespace mark;

    /**
     * Mark 基础类
     *
     */
    final  class Mark
    {
        const VERSION = '6.0.3';

        /**
         * 应用调试模式
         * @var bool
         */
        protected $appDebug = false;

        /**
         * 应用开始时间
         * @var float
         */
        protected $beginTime;

        /**
         * 应用内存初始占用
         * @var integer
         */
        protected $beginMem;

        /**
         * 当前应用类库命名空间
         * @var string
         */
        protected $namespace = 'app';

        /**
         * 应用根目录
         * @var string
         */
        protected $rootPath = '';

        /**
         * 框架目录
         * @var string
         */
        protected $markPath = '';

        /**
         * 应用目录
         * @var string
         */
        protected $appPath = '';

        /**
         * Runtime目录
         * @var string
         */
        protected $runtimePath = '';

        /**
         * 资源定义目录
         * @var string
         */
        protected $assetsPath = '';

        /**
         * 配置后缀
         * @var string
         */
        protected $configExt = '.php';


        /**
         * 注册的系统服务
         * @var array
         */
        protected $services = [];

        /**
         * 初始化
         * @var bool
         */
        protected $initialized = false;

        /**
         * 架构方法
         * @access public
         * @param string $rootPath 应用根目录
         */
        public function __construct(string $rootPath = '')
        {
            $this->markPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
            $this->rootPath = $rootPath ? rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : $this->getDefaultRootPath();
            $this->appPath = $this->rootPath . 'app' . DIRECTORY_SEPARATOR;
            $this->runtimePath = $this->rootPath . 'runtime' . DIRECTORY_SEPARATOR;
            $this->assetsPath = $this->rootPath . 'assets' . DIRECTORY_SEPARATOR;

            if (is_file($this->appPath . 'provider.php')) {
                // $this->bind(include $this->appPath . 'provider.php');
            }

            // static::setInstance($this);

            // $this->instance('app', $this);
            // $this->instance('think\Container', $this);
        }


        /**
         * 开启应用调试模式
         * @access public
         * @param bool $debug 开启应用调试模式
         * @return $this
         */
        public function debug(bool $debug = true)
        {
            $this->appDebug = $debug;
            return $this;
        }

        /**
         * 是否为调试模式
         * @access public
         * @return bool
         */
        public function isDebug(): bool
        {
            return $this->appDebug;
        }

        /**
         * 设置应用命名空间
         * @access public
         * @param string $namespace 应用命名空间
         * @return $this
         */
        public function setNamespace(string $namespace)
        {
            $this->namespace = $namespace;
            return $this;
        }

        /**
         * 获取应用类库命名空间
         * @access public
         * @return string
         */
        public function getNamespace(): string
        {
            return $this->namespace;
        }

        /**
         * 获取框架版本
         * @access public
         * @return string
         */
        public function version(): string
        {
            return static::VERSION;
        }

        /**
         * 获取应用根目录
         * @access public
         * @return string
         */
        public function getRootPath(): string
        {
            return $this->rootPath;
        }

        /**
         * 获取应用基础目录
         * @access public
         * @return string
         */
        public function getBasePath(): string
        {
            return $this->rootPath . 'app' . DIRECTORY_SEPARATOR;
        }

        /**
         * 获取当前应用目录
         * @access public
         * @return string
         */
        public function getAppPath(): string
        {
            return $this->appPath;
        }

        /**
         * 设置应用目录
         * @param string $path 应用目录
         */
        public function setAppPath(string $path)
        {
            $this->appPath = $path;
        }

        /**
         * 获取应用运行时目录
         * @access public
         * @return string
         */
        public function getRuntimePath(): string
        {
            return $this->runtimePath;
        }

        /**
         * 设置runtime目录
         * @param string $path 定义目录
         */
        public function setRuntimePath(string $path): void
        {
            $this->runtimePath = $path;
        }

        /**
         * 获取核心框架目录
         * @access public
         * @return string
         */
        public function getMarkPath(): string
        {
            return $this->markPath;
        }

        /**
         * 获取资源目录
         * @access public
         * @return string
         */
        public function getAssetsPath(): string
        {
            return $this->assetsPath;
        }

        /**
         * 获取应用配置目录
         * @access public
         * @return string
         */
        public function getConfigPath(): string
        {
            return $this->rootPath . 'config' . DIRECTORY_SEPARATOR;
        }

        /**
         * 获取配置后缀
         * @access public
         * @return string
         */
        public function getConfigExt(): string
        {
            return $this->configExt;
        }

        /**
         * 获取应用开启时间
         * @access public
         * @return float
         */
        public function getBeginTime(): float
        {
            return $this->beginTime;
        }

        /**
         * 获取应用初始内存占用
         * @access public
         * @return integer
         */
        public function getBeginMem(): int
        {
            return $this->beginMem;
        }

        /**
         * 初始化应用
         * @access public
         * @return $this
         */
        public function initialize()
        {
            $this->initialized = true;

            return $this;
        }

        /**
         * 是否初始化过
         * @return bool
         */
        public function initialized()
        {
            return $this->initialized;
        }


        /**
         * 是否运行在命令行下
         * @return bool
         */
        public function runningInConsole()
        {
            return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
        }

        /**
         * 获取应用根目录
         * @access protected
         * @return string
         */
        protected function getDefaultRootPath(): string
        {
            return dirname($this->markPath, 4) . DIRECTORY_SEPARATOR;
        }

    }
