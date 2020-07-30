<?php

    declare (strict_types=1);

    namespace mark\filesystem;

    use finfo;
    use Exception;

    /******************** Explorer File Class Start ********************
     *
     * // +----------------------------------------------------------------------
     * // | Explorer [ WE CAN DO IT JUST THINK ]
     * // +----------------------------------------------------------------------
     * // | Copyright (c) 2017~2020 http://TianFuUnion.cn All rights reserved.
     * // +----------------------------------------------------------------------
     * // | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
     * // +----------------------------------------------------------------------
     * // | Author: Mark <mark@TianFuUnion.cn>
     * // +----------------------------------------------------------------------
     *
     * ————————————————
     * 原文链接：https://blog.csdn.net/yonggeit/article/details/77512794
     * 资源管理器
     *
     * 一、文件传输
     * 1、文件上传、流式上传
     * 2、文件下载、流式下载
     * 3、远程文件
     *
     *
     * 二、重命名
     * 三、复制
     * 四、移动（剪切）
     * 五、删除
     * 六、创建（新建）
     * 七、读
     * 八、写
     * 九、权限
     * 十、转码
     *
     * $file = new file();
     * $file_path = "C:/Documents and Settings/Administrator/桌面/phpThumb_1.7.10-201104242100-beta";
     * $files = "C:/Documents and Settings/Administrator/桌面/phpThumb_1.7.10-201104242100-beta/index.php";
     * $create_path = "D:/这是创建的目录/哈哈/爱/的/味道/是/雪儿/给的/";
     * echo "创建文件夹:create_dir()<br>";
     * //if($file->create_dir($create_path)) echo "创建目录成功"; else "创建目录失败";
     * echo "<hr>创建文件:create_file()<br>";
     * //if($file->create_file($create_path."创建的文件.txt",true,strtotime("1988-05-04"),strtotime("1988-05-04"))) echo "创建文件成功!"; else echo "创建文件失败!";
     * echo "<hr>删除非空目录:remove_dir()<br>";
     * //if($file->remove_dir($file_path,true)) echo "删除非空目录成功!"; else echo "删除非空目录失败!";
     * echo "<hr>取得文件完整名称(带后缀名):get_basename()<br>";
     * //echo $file->get_basename($files);
     * echo "<hr>取得文件后缀名:get_ext()<br>";
     * //echo $file->get_ext($files);
     * echo "<hr>取得上N级目录:father_dir()<br>";
     * //echo $file->father_dir($file_path,3);
     * echo "<hr>删除文件:unlink()<br>";
     * //if($file->unlink($file_path."/index.php")) echo "删除文件成功!"; else "删除文件失败!";
     * echo "<hr>操作文件:handle_file()<br>";
     * //if($file->handle_file($file_path."/index.php",$create_path."/index.php","copy",true)) echo "复制文件成功!"; else echo "复制文件失败!";
     * //if($file->handle_file($file_path."/index.php", $create_path."/index.php","move",true)) echo "文件移动成功!"; else echo "文件移动失败!";
     * echo "<hr>操作文件夹:handle_dir()<br>";
     * //if($file->handle_dir($file_path,$create_path,"copy",true)) echo "复制文件夹成功!"; else echo "复制文件夹失败!";
     * //if($file->handle_dir($file_path,$create_path,"move",true)) echo "移动文件夹成功!"; else echo "移动文件夹失败!";
     * echo "<hr>取得文件夹信息:get_dir_info()<br>";
     * //print_r($file->get_dir_info($create_path));
     * echo "<hr>替换统一格式路径:dir_replace()<br>";
     * //echo $file->dir_replace("c:\d/d\e/d\h");
     * echo "<hr>取得指定模板文件:get_templtes()<br>";
     * //echo $file->get_templtes($create_path."/index.php");
     * echo "<hr>取得指定条件的文件夹中的文件:list_dir_info()<br>";
     * //print_r($file->list_dir_info($create_path,true));
     * echo "<hr>取得文件夹信息:dir_info()<br>";
     * //print_r($file->dir_info($create_path));
     * echo "<hr>判断文件夹是否为空:is_empty()<br>";
     * //if($file->is_empty($create_path)) echo "不为空"; else echo"为空";
     * echo "<hr>返回指定文件和目录的信息:list_info()<br>";
     * //print_r($file->list_info($create_path));
     * echo "<hr>返回关于打开文件的信息:open_info()<br>";
     * //print_r($file->open_info($create_path."/index.php"));
     * echo "<hr>取得文件路径信息:get_file_type()<br>";
     * //print_r($file->get_file_type($create_path));
     **/
    class Explorer
    {

        /**
         * @var array 上传文件信息
         */
        private $info = array(
            //限制上传文件的类型,可以使用set()设置，使用小字母
            'allowsuffix' => array('bmp', 'jpg', 'jpeg', 'gif', 'png', 'mp3', 'mp4', 'avi', 'rm', 'rmvb', '3pg', 'ico'),
            //限制上传文件的类型,可以使用set()设置，使用小字母
            'allowtype' => array('image/bmp', 'image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'audio/mpeg', 'video/mp4',
                'video/x-msvideo', 'video/rm', 'video/rmvb', 'video/3pg', 'image/x-icon'),
            //限制上传文件的类型,可以使用set()设置，使用小字母
            'allowmime' => array('image/bmp', 'image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'audio/mpeg', 'video/mp4',
                'video/x-msvideo', 'video/rm', 'video/rmvb', 'video/3pg', 'image/x-icon'),

            'maxsize' => 2097152,  //限制文件上传大小，单位是字节,可以使用set()设置

            'originname' => '',    //	源文件名
            'tmpfilename' => '',   //	临时文件名
            'filename' => '',      //	当前完整文件名，新文件名，上传文件名

            'filepath' => '',

            'filesize' => '0',     //	文件大小
            'filetype' => '',      //	文件类型
            'filesuffix' => '',    //	文件后缀
            'filemime' => '',      //	文件MIME

            'fileatime' => '0',    //	返回文件的上次访问时间。
            'filectime' => '0',    //	返回文件的上次改变时间。
            'filemtime' => '0',    //	返回文件的上次修改时间。
            'filegroup' => '0',    //	返回文件的组 id。
            'fileinode' => '0',    //	返回文件的 inode 编号。
            'fileowner' => '0',    //	文件的 user id （所有者）。
            'fileperms' => '0',    //	返回文件的权限。
            'fileetag' => '',      //	文件加密信息
            'filehash' => '',      //	获取文件的 hash 值

            'rule' => 'time',       // 文件上传命名规则
            'validate' => array(),  // @var array 文件上传验证规则

            'istest' => false,      //@var bool 单元测试

            'files' => '',

            'msg' => '',            // 提示消息
            'error' => array()      // 错误信息
        );

        /**
         * Explorer constructor.
         *
         * @param string $fileField 文件名称
         * @param string $mode 访问模式
         */
        public function __construct($fileField = '', $mode = 'r')
        {
            // parent::__construct($filename, $mode);
            // $this->setFile($_FILES);
            // $this->setFileName($fileField);
            // $this->filename = $this->getRealPath() ?: $this->getPathname();
        }

        /**
         * 检查目录是否可写
         *
         * @param string $path
         *
         * @return bool
         */
        private function checkFilePath(string $path)
        {
            if (empty($path)) {
                if (!is_dir($path)) {
                    //循环创建目录
                    self::mkdir($path, 0755, true);
                }
            }
            if (!file_exists($path) || !is_writable($path)) {
                if (!@self::mkdir($path, 0755, true)) {
                    $this->setError("建立文件目录（{$path}）失败，请重新指定文件目录");

                    return false;
                }
            }

            return true;
        }

        /**
         * 检查上传的文件是否是允许的大小 * 检测上传文件大小
         *
         * @param $size
         *
         * @return bool
         */
        private function checkFileSize($size)
        {
            if ($size > $this->getMaxSize()) {
                $this->setError('上传文件大小超过了最大值：' . self::toSize($this->getMaxSize()));

                return false;
            }

            if ($size <= 0) {
                $this->setError('上传文件大小低于了最小值：' . self::toSize(0));

                return false;
            }

            return true;
        }

        /**
         * 检查上传的文件是否是合法的类型
         *
         * @param string $type
         *
         * @return bool
         */
        private function checkFileType(string $type)
        {
            if (in_array(strtolower($type), $this->getAllowType())) {
                return true;
            }

            // $this->setOption("errorNum", -1);
            $this->setError('未允许文件类型');

            return false;
        }

        /**
         * 检查上传的文件是否是合法的后缀
         *
         * @param string $suffix
         *
         * @return bool
         */
        private function checkFileSuffix(string $suffix)
        {
            if (in_array(strtolower($suffix), $this->getAllowSuffix())) {
                return true;
            }

            $this->setError('上传文件后缀不允许');

            return false;
        }

        /**
         * 检查上传的文件是否是合法的MIME
         *
         * @param string $mime
         *
         * @return bool
         */
        private function checkFileMime(string $mime)
        {
            if (in_array(strtolower($mime), $this->getAllowMime())) {
                return true;
            }

            $this->setError('上传文件MIME类型不允许！');

            return false;
        }

        /**
         * 设置上传后的文件名称
         */
        private function setNewFileName()
        {
            if ($this->isRandName()) {
                $this->setOption('FileName', $this->proRandName());
            } else {
                $this->setOption('FileName', $this->getOriginName());
            }
        }

        /**
         * 创建文件
         *
         * @param string $fileName 需要创建的文件名
         * @param string $str 需要向文件中写的内容字符串
         */
        public static function touch(string $fileName, string $str)
        {
            if (!file_exists($fileName)) {
                if (file_put_contents($fileName, $str)) {
                    // self::$mess[]="创建文件 {$fileName} 成功.";
                    self::setMsg("创建文件 {$fileName} 成功.");
                }
            }
        }

        /**
         * 创建目录 * 连续创建带层级的文件夹
         *
         * @param string $paths
         * @param int $mode
         * @param bool $recursive
         *
         * @return bool
         */
        public static function mkdir(string $paths, $mode = 0755, $recursive = true)
        {
            $paths = preg_split("/[\\\\\/]/", $paths);
            $mkfolder = '';
            foreach ($paths as $path) {
                if (trim($path) === '') {
                    continue;
                }

                $mkfolder .= $path;
                if (!is_dir($mkfolder)) {
                    if (mkdir($mkfolder, $mode, $recursive) || is_dir($mkfolder)) {
                        self::setMsg("创建目录 {$mkfolder} 成功.");
                    } else {
                        self::setMsg("创建目录 {$mkfolder} 失败.");

                        return false;
                    }
                }
                $mkfolder .= DIRECTORY_SEPARATOR;
            }

            return is_dir($paths);
        }

        /**
         * 创建多级目录
         *
         * @param     $dir
         * @param int $mode
         *
         * @return bool
         */
        public static function mk_dir($dir, $mode = 0777)
        {
            return is_dir($dir) or (self::mk_dir(dirname($dir)) && !mkdir($dir, $mode) && !is_dir($dir));
        }

        /**
         * 如果文件夹不存在，将以递归方式创建该文件夹
         *
         * @param string $path
         * @param bool $recursive
         *
         * @return bool
         */
        public static function isdir(string $path, $recursive = true)
        {
            return is_dir($path) || mkdir($path, 0777, $recursive) || is_dir($path);
        }

        private static $msg = array();

        public static function setMsg($msg)
        {
            self::$msg[] = $msg;
        }

        public static function getMsg()
        {
            return self::$msg;
        }

        /**
         * 获取上传的文件信息
         *
         * @param string $fileField
         *
         * @return bool
         */
        public function upload(string $fileField = '')
        {
            $return = true;

            $name = $_FILES[$fileField]['name'];
            $tmp_name = $_FILES[$fileField]['tmp_name'];
            $size = $_FILES[$fileField]['size'];
            $error = $_FILES[$fileField]['error'];

            if (is_Array($name)) {  //如果是多个文件上传则$file["name"]会是一个数组
                // $error = array();
                for ($i = 0, $iMax = count($name); $i < $iMax; $i++) {
                    //设置文件信息
                    if (
                        $this->setFiles($name[$i], $tmp_name[$i], $size[$i], $error[$i]) &&
                        $this->checkFileSize($size[$i]) &&
                        $this->checkFileType($this->getFileType()) &&
                        $this->checkFileSuffix($this->getFileSuffix())) {

                    } else {
                        $return = false;
                    }
                    if (!$return) {
                        // 如果有问题，则重新初使化属性
                        $this->setFiles();
                    }
                }

                if ($return) {
                    $fileNames = array();   //存放所有上传后文件名的变量数组
                    for ($i = 0, $iMax = count($name); $i < $iMax; $i++) {
                        //设置文件信息
                        if ($this->setFiles($name[$i], $tmp_name[$i], $size[$i], $error[$i])) {
                            //设置新文件名
                            $this->setNewFileName();
                            $fileNames[] = $this->getNewFileName();
                        }
                    }
                    $this->setFileName($fileNames);
                }

                return $return;
            }

            if ($this->setFiles($name, $tmp_name, $size, $error)) {//设置文件信息
                if (
                    $this->checkFileSize($size) &&
                    $this->checkFileType($this->getFileType()) &&
                    $this->checkFileSuffix($this->getFileSuffix())) {
                    //设置新文件名
                    $this->setFileName();
                } else {
                    $return = false;
                }
            } else {
                $return = false;
            }

            return $return;
        }

        private $file = null;

        private function setFile($file, $error = 0)
        {
            if (empty($this->file)) {
                $this->file = $_FILES ?? [];
            }
            if (is_array($file)) {
                return $this->file = array_merge($this->file, $file);
            }
            $files = $this->file;
            $this->setFiles($files);
            if (!empty($files)) {
                // 处理上传文件
                $array = [];
                foreach ($files as $key => $file) {
                    if (is_array($file['name'])) {
                        $item = [];
                        $keys = array_keys($file);
                        $count = count($file['name']);
                        for ($i = 0; $i < $count; $i++) {
                            if (empty($file['tmp_name'][$i]) || !is_file($file['tmp_name'][$i])) {
                                continue;
                            }
                            $temp['key'] = $key;
                            foreach ($keys as $_key) {
                                $temp[$_key] = $file[$_key][$i];
                            }
                            // $item[] = (new File($temp["tmp_name"]))->setUploadInfo($temp);
                        }
                        $array[$key] = $item;
                    } else {
                        if ($file instanceof File) {
                            $array[$key] = $file;
                        } else {
                            if (empty($file['tmp_name']) || !is_file($file['tmp_name'])) {
                                continue;
                            }
                            // $array[$key] = (new File($file["tmp_name"]))->setUploadInfo($file);
                        }
                    }
                }
                if (strpos($file, '.')) {
                    [$file, $sub] = explode('.', $file);
                }
                if ("" === $file) {
                    // 获取全部文件
                    return $array;
                }

                if (isset($sub) && isset($array[$file][$sub])) {
                    return $array[$file][$sub];
                }

                if (isset($array[$file])) {
                    return $array[$file];
                }
            }

            return false;
        }

        /**
         * 设置和$_FILES有关的内容
         *
         * @param string $name
         * @param string $tmp_name
         * @param int $size
         * @param int $error
         *
         * @return bool
         */
        private function setFiles($name = '', $tmp_name = '', $size = 0, $error = 0)
        {
            // $this->setOption("errorNum", $error);
            // $this->setErrorNum($error);
            if ($error) {
                return false;
            }

            $this->setFileName($name);

            $this->setOriginName($name);
            $this->setTmpFileName($tmp_name);
            $this->setFileSize($size);
            $aryStr = explode('.', $name);
            $this->setFileSuffix(strtolower($aryStr[count($aryStr) - 1]));
            $this->setFileType(strtolower($aryStr[count($aryStr) - 1]));

            // $tmp_dir_name = dirname($tmp_name);
            $tmp_dir_name = $tmp_name;

            if (extension_loaded('fileinfo')) {
                $this->setFileType($this->getMime($tmp_name));
                $this->setFileMime($this->getMime($tmp_name));
            } else {
                $this->setError('Explorer::setFiles(fileinfo) ' . extension_loaded('fileinfo'));
            }

            // $this->setOption("fileETag", md5_file($tmp_dir_name));
            $this->setFileETag(md5_file($tmp_dir_name));
            $this->setFileHash(hash_file('sha1', $tmp_dir_name));

            $this->setFileCTime(filectime($tmp_dir_name));//	返回文件的上次改变时间。
            $this->setFileMTime(filemtime($tmp_dir_name));//	返回文件的上次修改时间。
            $this->setFileATime(fileatime($tmp_dir_name));//	返回文件的上次访问时间。
            $this->setFileGroup(filegroup($tmp_dir_name));//	返回文件的组 ID。
            $this->setFileInode(fileinode($tmp_dir_name));//	返回文件的 inode 编号。
            $this->setFileOwner(fileowner($tmp_dir_name));//	文件的 user ID （所有者）。
            $this->setFilePerms(fileperms($tmp_dir_name));//	返回文件的权限。

            return true;
        }

        /**
         * 简单上传，一次仅上传一个文件
         *
         */
        public function single()
        {
        }

        /**
         * 多文件上传，一次可上传多个文件
         *
         */
        public function multiple()
        {
        }

        /**
         * 分片上传，断点续传
         *
         */
        public function multipart()
        {
        }

        /**
         * $key
         * $field
         * $value
         *
         * @param $method
         * @param $args
         *
         * @return bool|mixed
         */
        public function __call($method, $args)
        {
            $this->setError("Explorer::__call({$method})" . json_encode($args, JSON_UNESCAPED_UNICODE));

            $key = strtolower(substr($method, 3));

            $i = substr($method, 0, 3);
            if ($i === 'get') {
                return $this->getData($key);
            }

            if ($i === 'set') {
                return $this->setData($key, $args[0]);
            }

            if ($i === 'uns') {
                return $this->unsetData($key);
            }

            if ($i === 'has') {
                return isset($this->info[$key]);
            }

            if (strpos($method, "is") === 0) {
                return $this->isData($key);
            }

            return false;

            /*
                switch (substr($method, 0, 3)) {
                    case "get" :
                        return $this->getData($key);
                    case "set" :
                        return $this->setData($key, $args[0]);
                    case "uns" :
                        return $this->unsetData($key);
                    case "has" :
                        return isset($this->info[$key]);
                    default:
                        return false;
                }
            */

        }

        private function getData($key)
        {
            return array_key_exists($key, $this->info) ? $this->info[$key] : false;
        }

        /**
         * 为单个成员属性设置值
         *
         * @param $key
         * @param $value
         */
        private function setOption($key, $value)
        {
            // $this->$key = $value;
            $this->setData($key, $value);
        }

        /**
         * 为单个成员属性设置值
         *
         * @param $key
         * @param $value
         *
         * @return bool
         */
        private function setData($key, $value)
        {
            if (array_key_exists($key, $this->info)) {
                $this->info[$key] = $value;

                return true;
            }

            return false;
        }

        private function unsetData($key)
        {
            if (array_key_exists($key, $this->info)) {
                unset($this->info[$key]);

                return true;
            }

            return false;
        }

        private function isData($key)
        {
            return array_key_exists($key, $this->info) ? $this->info[$key] ? true : false : false;
        }

        /**
         * 用于设置成员属性（$path, $allowtype,$maxsize, $israndname, $thumb,$watermark）
         * 可以通过连贯操作一次设置多个属性值
         *
         * @param string $key 成员属性名(不区分大小写)
         * @param mixed $val 为成员属性设置的值
         *
         * @return $this
         */
        public function set(string $key, $val)
        {
            $key = strtolower($key);
            if (array_key_exists($key, get_class_vars(get_class($this)))) {
                $this->setOption($key, $val);
            }

            return $this;
        }

        /**
         * 移动文件
         *
         * @access public
         *
         * @param string $path 保存路径
         * @param bool $savename 保存的文件名 默认自动生成
         * @param bool $replace 同名文件是否覆盖
         *
         * @return bool|Explorer
         */
        public function move(string $path, $savename = true, $replace = true)
        {
            if ($this->checkFilePath($path)) {
                $this->setFilePath($path);
            } else {
                return false;
            }

            // 文件上传失败，捕获错误代码
            if (!empty($this->info["error"])) {
                // return false;
            }

            // 检测合法性
            if (!$this->is_uploaded($this->getTmpFileName())) {
                $this->setError('upload illegal files ' . $this->getTmpFileName());

                return false;
            }

            // 验证上传
            if (!$this->validate()) {
                return false;
            }

            $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            // 文件保存命名规则
            $saveName = $this->buildSaveName($savename);
            // $filename = $path.$saveName;
            $filename = $path . $this->getFileName();

            // 检测目录
            if (false === $this->checkFilePath(dirname($filename))) {
                return false;
            }

            // 不覆盖同名文件
            if (!$replace && is_file($filename)) {
                $this->setError('has the same filename: {:filename}');

                return false;
            }

            /* 移动文件 */
            if ($this->isTest()) {
                rename($this->getFileName(), $filename);
            } elseif (move_uploaded_file($this->getTmpFileName(), $filename)) {
                return true;
            } else {
                $this->setError('upload write error');

                return false;
            }

            // 返回 File 对象实例
            $file = new self($filename);
            $file->setFileName($saveName)->setUploadInfo($this->info);

            return $file;
        }

        /**
         * 检测是否合法的上传文件
         *
         * @access public
         *
         * @param $file
         *
         * @return bool
         */
        public function is_uploaded($file)
        {
            return $this->isTest() ? is_file($file) : is_uploaded_file($file);
        }

        /**
         * 检测上传文件
         *
         * @access public
         *
         * @param array $rule 验证规则
         *
         * @return bool
         */
        public function validate($rule = [])
        {
            $rule = $rule ?: $this->getValidate();

            /* 检查文件大小 */
            if (isset($rule['size']) && !$this->checkSize($rule['size'])) {
                $this->setError('filesize not match');

                return false;
            }

            /* 检查文件 Mime 类型 */
            if (isset($rule['type']) && !$this->checkMime($rule['type'])) {
                $this->setError('mimetype to upload is not allowed');

                return false;
            }

            /* 检查文件后缀 */
            if (isset($rule['ext']) && !$this->checkExt($rule['ext'])) {
                $this->setError('extensions to upload is not allowed');

                return false;
            }

            /* 检查图像文件 */
            if (!$this->checkImg()) {
                // $this->error = "illegal image files";
                // $this->setError("illegal image files");
                // return false;
            }

            return true;
        }

        /**
         * 检测上传文件类型
         *
         * @access public
         *
         * @param array|string $mime 允许类型
         *
         * @return bool
         */
        public function checkMime($mime)
        {
            $mime = is_string($mime) ? explode(',', $mime) : $mime;

            return in_array(strtolower($this->getMime()), $mime);
        }

        /**
         * 获取文件类型信息
         *
         * @access public
         *
         * @param null $filename
         *
         * @return string
         */
        public function getMime($filename = null)
        {
            if ($filename == null) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);

                return finfo_file($finfo, $this->getFileName());
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);

            return $finfo->file($filename);
        }

        /**
         * 检测上传文件后缀
         *
         * @access public
         *
         * @param array|string $ext 允许后缀
         *
         * @return bool
         */
        public function checkExt($ext)
        {
            if (is_string($ext)) {
                $ext = explode(',', $ext);
            }

            $extension = strtolower(pathinfo($this->getInfo('tmpfilename'), PATHINFO_EXTENSION));

            return in_array($extension, $ext);
        }

        /**
         * 获取上传文件的信息
         *
         * @access public
         *
         * @param string $name 信息名称
         *
         * @return array|string
         */
        public function getInfo($name = '')
        {
            return $this->info[$name] ?? $this->info;
        }

        /**
         * 检测图像文件
         *
         * @return bool
         */
        public function checkImg()
        {
            $extension = strtolower(pathinfo($this->getInfo('tmpfilename'), PATHINFO_EXTENSION));

            // 如果上传的不是图片，或者是图片而且后缀确实符合图片类型则返回 true
            return !in_array($extension, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf']) ||
                in_array($this->getImageType($this->getFileName()), [1, 2, 3, 4, 6, 13]);
        }

        /**
         * 判断图像类型
         *
         * @param string $image
         *
         * @return bool|false|int|mixed
         */
        protected function getImageType(string $image)
        {
            if (function_exists('exif_imagetype')) {
                return exif_imagetype($image);
            }

            try {
                $info = getimagesize($image);

                return $info ? $info[2] : false;
            } catch (Exception $e) {
                return false;
            }
        }

        /**
         * 获取保存文件名
         *
         * @access protected
         *
         * @param string|bool $savename 保存的文件名 默认自动生成
         *
         * @return string
         */
        protected function buildSaveName($savename)
        {

            if ($savename === 'custom' || is_callable($savename)) {    // 使用自定义文件名
            } else {
                $this->setError("Explorer::buildSaveName(savename：{$savename})");
            }
            if ($savename === 'original') {    // 使用原始文件名
                $this->setFileName($this->getOriginName());
            } elseif ($savename === 'time') {
                $this->setFileName(md5(microtime()) . '.' . $this->getFileSuffix());
            } elseif ($savename === 'md5') {
                $this->setFileName(md5($this->getOriginName()) . '.' . $this->getFileSuffix());
            } elseif ($savename === 'sha1') {
                $this->setFileName(sha1($this->getOriginName()) . '.' . $this->getFileSuffix());
            } elseif ($savename === 'custom' || is_callable($savename)) {    // 使用自定义文件名
                $call = call_user_func($savename, $this->getOriginName());
                if ($call) {
                    $this->setFileName($call);
                } else {
                    $this->setFileName(md5_file(dirname($this->getTmpFileName())) . '.' . $this->getFileSuffix());
                }
                $this->setError("Explorer::buildSaveName(call：{$call})");
            } else {    // 使用随机文件名
                $this->setFileName(md5_file(dirname($this->getTmpFileName())) . '.' . $this->getFileSuffix());
            }
            $savename = $this->getFilename();

            /*
                    // 自动生成文件名
                    if (true === $savename) {
                        if ($this->rule instanceof \Closure) {
                            $savename = call_user_func_array($this->rule, [$this]);
                        } else {
                            switch ($this->rule) {
                                case "date":
                                    $savename = date("Ymd") . DIRECTORY_SEPARATOR . md5(microtime(true));
                                    break;
                                default:
                                    if (in_array($this->rule, hash_algos())) {
                                        $hash     = $this->hash($this->rule);
                                        $savename = substr($hash, 0, 2) . DIRECTORY_SEPARATOR . substr($hash, 2);
                                    } elseif (is_callable($this->rule)) {
                                        $savename = call_user_func($this->rule);
                                    } else {
                                        $savename = date("Ymd") . DIRECTORY_SEPARATOR . md5(microtime(true));
                                    }
                            }
                        }
                    } elseif ("" === $savename || false === $savename) {
                        $savename = $this->getInfo("name");
                    }

                    if (!strpos($savename, ".")) {
                        $savename .= "." . pathinfo($this->getInfo("name"), PATHINFO_EXTENSION);
                    }
            */

            return $savename;
        }

        /**
         * 设置上传信息
         *
         * @access public
         *
         * @param array $info 上传文件信息
         *
         * @return $this
         */
        public function setUploadInfo($info)
        {
            $this->info = $info;

            return $this;
        }

        /**
         * 创建指定路径下的指定文件
         *
         * @param string $path (需要包含文件名和后缀)
         * @param boolean $over_write 是否覆盖文件
         * @param int $time 设置时间。默认是当前系统时间
         * @param int $atime 设置访问时间。默认是当前系统时间
         *
         * @return boolean
         */
        public function create_file($path, $over_write = false, $time = NULL, $atime = NULL)
        {
            $path = self::dir_replace($path);
            $time = $time === null ? time() : $time;
            $atime = $atime === null ? time() : $atime;
            if (file_exists($path) && $over_write) {
                self::unlink($path);
            }
            $aimDir = dirname($path);
            self::mkdir($aimDir);

            return touch($path, $time, $atime);
        }

        /**
         * 替换相应的字符
         *
         * @param string $path 路径
         *
         * @return string
         */
        public static function dir_replace($path)
        {
            return str_replace(array("\\", '//'), '/', $path);
        }

        /**
         * 删除文件
         *
         * @param string $path
         *
         * @return boolean
         */
        public static function unlink($path)
        {
            $path = self::dir_replace($path);
            if (file_exists($path)) {
                return unlink($path);
            }

            return false;
        }

        /**
         * 关闭文件操作
         *
         * @param resource $path
         *
         * @return bool
         */
        public static function close($path)
        {
            return fclose($path);
        }

        /**
         * 读取文件操作
         *
         * @param $file
         *
         * @return false|string
         */
        public function read_file($file)
        {
            return @file_get_contents($file);
        }

        /**
         * 确定服务器的最大上传限制（字节数）
         *
         * @return string
         */
        public function allow_upload_size()
        {
            return trim(ini_get('upload_max_filesize'));
        }

        /**
         * 字节格式化 把字节数格式为 B K M G T P E Z Y 描述的大小
         *
         * @param int $size 大小
         * @param int $dec 显示类型
         *
         * @return int
         */
        public function byte_format($size, $dec = 2)
        {
            $a = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            $pos = 0;
            while ($size >= 1024) {
                $size /= 1024;
                $pos++;
            }

            return round($size, $dec) . ' ' . $a[$pos];
        }

        /**
         * 删除非空目录
         * 说明:只能删除非系统和特定权限的文件,否则会出现错误
         *
         * @param string $path 目录路径
         * @param bool $recursive 是否递归删除
         *
         * @return bool
         */
        public static function remove_dir(string $path, $recursive = false)
        {
            $dirName = self::dir_replace($path);
            $handle = @opendir($dirName);
            while (($file = @readdir($handle)) !== false) {
                if ($file !== '.' && $file !== '..') {
                    $dir = $dirName . DIRECTORY_SEPARATOR . $file;
                    if ($recursive) {
                        is_dir($dir) ? self::remove_dir($dir, $recursive) : self::unlink($dir);
                    } else {
                        if (is_file($dir)) {
                            self::unlink($dir);
                        }
                    }
                }
            }
            closedir($handle);

            return @rmdir($dirName);
        }

        /**
         * 清空文件夹函数和清空文件夹后删除空文件夹函数的处理 * 待测试
         *
         * @param string $path
         * @param bool $recursive
         * @return bool
         */
        public static function remove(string $path, $recursive = false)
        {
            if (!empty($path)) {
                $path = rtrim($path, '/');
                $path = rtrim($path, '\\');
            }
            //如果是目录则继续
            if (!is_dir($path)) {
                return false;
            }
            $result = true;

            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach ($p as $val) {
                //排除目录中的.和..
                if ($val !== '.' && $val !== '..') {
                    //如果是目录则递归子目录，继续操作
                    if (is_dir($path . DIRECTORY_SEPARATOR . $val)) {
                        if ($recursive) {
                            //子目录中操作删除文件夹和文件
                            self::remove($path . $val);
                            //目录清空后删除空文件夹
                            @rmdir($path . DIRECTORY_SEPARATOR . $val);
                        }
                    } else {
                        //如果是文件直接删除
                        $result = unlink($path . $val);
                    }
                }
            }

            return $result;
        }

        /**
         * 获取完整文件名
         *
         * @param string $file_path
         *
         * @return string
         */
        public function basename(string $file_path)
        {
            $file_path = self::dir_replace($file_path);

            return basename(str_replace("\\", '/', $file_path));
        }

        /**
         * 取得指定目录名称
         *
         * @param string $path 文件路径
         * @param int $num 需要返回以上级目录的数
         *
         * @return string
         */
        public function father_dir(string $path, $num = 1)
        {
            $path = self::dir_replace($path);
            $arr = explode('/', $path);
            if ($num == 0 || count($arr) < $num) {
                return pathinfo($path, PATHINFO_BASENAME);
            }

            return strpos(strrev($path), "/") === 0 ? $arr[(count($arr) - (1 + $num))] : $arr[(count($arr) - $num)];
        }

        /**
         * 文件夹操作(复制/移动)
         *
         * @param string $old_path 指定要操作文件夹路径
         * @param string $new_path 指定新文件夹路径
         * @param string $type 操作类型
         * @param bool $overWrite 是否覆盖文件和文件夹
         *
         * @return bool
         */
        public function handle_dir(string $old_path, string $new_path, $type = 'copy', $overWrite = false)
        {
            $new_path = $this->check_path($new_path);
            $old_path = $this->check_path($old_path);
            if (!is_dir($old_path)) {
                return false;
            }

            if (!file_exists($new_path)) {
                self::mkdir($new_path);
            }

            $dirHandle = opendir($old_path);

            if (!$dirHandle) {
                return false;
            }

            $boolean = true;

            while (false !== ($file = readdir($dirHandle))) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                if (!is_dir($old_path . $file)) {
                    $boolean = $this->handle_file($old_path . $file, $new_path . $file, $type, $overWrite);
                } else {
                    $this->handle_dir($old_path . $file, $new_path . $file, $type, $overWrite);
                }
            }
            switch ($type) {
                case 'copy':
                    closedir($dirHandle);

                    return $boolean;
                    break;
                case 'move':
                    closedir($dirHandle);

                    return rmdir($old_path);
                    break;
                default:
                    return false;
                    break;
            }
        }

        /**
         * 文件保存路径处理
         *
         * @param string $path
         *
         * @return string
         */
        public function check_path(string $path)
        {
            return (preg_match("/\/$/", $path)) ? $path : $path . DIRECTORY_SEPARATOR;
        }

        /**
         * 文件操作(复制/移动)
         *
         * @param string $old_path 指定要操作文件路径(需要含有文件名和后缀名)
         * @param string $new_path 指定新文件路径（需要新的文件名和后缀名）
         * @param string $type 文件操作类型
         * @param bool $overWrite 是否覆盖已存在文件
         *
         * @return bool
         */
        public function handle_file(string $old_path, string $new_path, $type = 'copy', $overWrite = false)
        {
            $old_path = self::dir_replace($old_path);
            $new_path = self::dir_replace($new_path);
            if (file_exists($new_path) && $overWrite === false) {
                return false;
            }

            if (file_exists($new_path) && $overWrite === true) {
                self::unlink($new_path);
            }

            $aimDir = dirname($new_path);
            self::mk_dir($aimDir);
            switch ($type) {
                case 'copy':
                    return copy($old_path, $new_path);
                    break;
                case 'move':
                    return rename($old_path, $new_path);
                    break;
                default:
                    return false;
                    break;
            }
        }

        /**
         * 读取指定路径下模板文件
         *
         * @param string $path 指定路径下的文件
         *
         * @return string $rstr
         */
        public function get_templtes(string $path)
        {
            $path = self::dir_replace($path);
            if (file_exists($path)) {
                $fp = fopen($path, 'rb');
                $rstr = fread($fp, filesize($path));
                fclose($fp);

                return $rstr;
            }

            return '';
        }

        /**
         * 文件重命名
         *
         * @param string $oldname
         * @param string $newname
         *
         * @return bool
         */
        public function rename(string $oldname, string $newname)
        {
            if (($newname != $oldname) && is_writable($oldname)) {
                return rename($oldname, $newname);
            }

            return false;
        }

        /**
         * 获取指定路径下的信息
         *
         * @param string $dir 路径
         *
         * @return mixed
         */
        public function get_dir_info(string $dir)
        {
            //打开指定目录
            $handle = @opendir($dir);
            $directory_count = 0;

            $total_size = 0;
            $file_cout = 0;

            while (false !== ($file_path = readdir($handle))) {
                if ($file_path !== '.' && $file_path !== '..') {
                    //is_dir("$dir/$file_path") ? $sizeResult += $this->get_dir_size("$dir/$file_path") : $sizeResult += filesize("$dir/$file_path");
                    $next_path = $dir . DIRECTORY_SEPARATOR . $file_path;
                    if (is_dir($next_path)) {
                        $directory_count++;
                        $result_value = self::get_dir_info($next_path);
                        $total_size += $result_value['size'];
                        $file_cout += $result_value['filecount'];
                        $directory_count += $result_value['dircount'];
                    } elseif (is_file($next_path)) {
                        $total_size += filesize($next_path);
                        $file_cout++;
                    }
                }
            }
            //关闭指定目录
            closedir($handle);
            $result_value['size'] = $total_size;
            $result_value['filecount'] = $file_cout;
            $result_value['dircount'] = $directory_count;

            return $result_value;
        }

        /**
         * 指定目录下指定条件文件编码转换
         *
         * @param string $dirname 目录路径
         * @param string $input_code 原始编码
         * @param string $out_code 输出编码
         * @param bool $is_all 是否转换所有子目录下文件编码
         * @param string $exts 文件类型
         *
         * @return bool
         */
        public function change_dir_files_code(string $dirname, string $input_code, string $out_code, $is_all = true, $exts = '')
        {
            if (is_dir($dirname)) {
                $fh = opendir($dirname);
                while (($file = readdir($fh)) !== false) {
                    if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0) {
                        continue;
                    }
                    $filepath = $dirname . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($filepath) && $is_all == TRUE) {
                        $files = $this->change_dir_files_code($filepath, $input_code, $out_code, $is_all, $exts);
                    } elseif ($this->get_ext($filepath) == $exts && is_file($filepath)) {
                        $boole = $this->change_file_code($filepath, $input_code, $out_code, $is_all, $exts);
                        if (!$boole) {
                            continue;
                        }
                    }
                }
                closedir($fh);

                return true;
            }

            return false;
        }

        /**
         * 获取文件后缀名
         *
         * @param $file
         *
         * @return string
         */
        public function get_ext($file)
        {
            $file = self::dir_replace($file);
            //return strtolower(substr(strrchr(basename($file), "."),1));
            //return end(explode(".",$filename ));
            //return strtolower(trim(array_pop(explode(".", $file))));//取得后缀
            //return preg_replace("/.*\.(.*[^\.].*)*/iU","\\1",$file);
            return pathinfo($file, PATHINFO_EXTENSION);
        }

        /**
         * 指定文件编码转换
         *
         * @param string $path 文件路径
         * @param string $input_code 原始编码
         * @param string $out_code 输出编码
         *
         * @return boolean
         * @todo Str::chang_code Unknown
         */
        public function change_file_code($path, $input_code, $out_code, $exts)
        {
            //检查文件是否存在,如果存在就执行转码,返回真
            if (is_file($path)) {
                $content = file_get_contents($path);
                $content = Str::chang_code($content, $input_code, $out_code);
                $fp = fopen($path, 'wb');

                $written = fwrite($fp, $content) ? true : false;
                fclose($fp);

                return $written;
            }

            return false;
        }

        /**
         * 列出指定目录下符合条件的文件和文件夹
         *
         * @param string $dirname 路径
         * @param boolean $is_all 是否列出子目录中的文件
         * @param string $exts 需要列出的后缀名文件
         * @param string $sort 数组排序
         *
         * @return array|bool
         */
        public function list_dir_info(string $dirname, $is_all = false, $exts = '', $sort = 'ASC')
        {
            // 处理多于的/号
            $new = strrev($dirname);
            if (strpos($new, '/') == 0) {
                $new = substr($new, 1);
            }
            $dirname = strrev($new);

            $sort = strtolower($sort);//将字符转换成小写

            $files = array();
            $subfiles = array();

            if (is_dir($dirname)) {
                $fh = opendir($dirname);
                while (($file = readdir($fh)) !== false) {
                    if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0) {
                        continue;
                    }
                    $filepath = $dirname . DIRECTORY_SEPARATOR . $file;

                    switch ($exts) {
                        case '*':
                            if (is_dir($filepath) && $is_all == TRUE) {
                                $files = array_merge($files, self::list_dir_info($filepath, $is_all, $exts, $sort));
                            }
                            $files[] = $filepath;
                            break;
                        case 'folder':
                            if (is_dir($filepath) && $is_all == TRUE) {
                                $files = array_merge($files, self::list_dir_info($filepath, $is_all, $exts, $sort));
                                $files[] = $filepath;
                            } elseif (is_dir($filepath)) {
                                $files[] = $filepath;
                            }
                            break;
                        case 'file':
                            if (is_dir($filepath) && $is_all == TRUE) {
                                $files = array_merge($files, self::list_dir_info($filepath, $is_all, $exts, $sort));
                            } elseif (is_file($filepath)) {
                                $files[] = $filepath;
                            }
                            break;
                        default:
                            if (is_dir($filepath) && $is_all == TRUE) {
                                $files = array_merge($files, self::list_dir_info($filepath, $is_all, $exts, $sort));
                            } elseif (preg_match("/\.($exts)/i", $filepath) && is_file($filepath)) {
                                $files[] = $filepath;
                            }
                            break;
                    }

                    switch ($sort) {
                        case 'asc':
                            sort($files);
                            break;
                        case 'desc':
                            rsort($files);
                            break;
                        case 'nat':
                            natcasesort($files);
                            break;
                    }
                }
                closedir($fh);

                return $files;
            }

            return false;
        }

        /**
         * 返回指定路径的文件夹信息，其中包含指定路径中的文件和目录
         *
         * @param string $dir
         *
         * @return array|false
         */
        public function dir_info(string $dir)
        {
            return scandir($dir);
        }

        /**
         * 判断目录是否为空
         *
         * @param string $dir
         *
         * @return boolean
         */
        public function is_empty(string $dir)
        {
            $handle = opendir($dir);
            while (($file = readdir($handle)) !== false) {
                if ($file !== '.' && $file !== '..') {
                    closedir($handle);

                    return true;
                }
            }
            closedir($handle);

            return false;
        }

        /**
         * 返回指定文件和目录的信息
         *
         * @param string $file
         *
         * @return array
         */
        public function list_info(string $file)
        {
            $dir = array();
            $dir['filename'] = basename($file);//返回路径中的文件名部分。
            $dir['pathname'] = realpath($file);//返回绝对路径名。
            $dir['owner'] = fileowner($file);//文件的 user ID （所有者）。
            $dir['perms'] = fileperms($file);//返回文件的 inode 编号。
            $dir['inode'] = fileinode($file);//返回文件的 inode 编号。
            $dir['group'] = filegroup($file);//返回文件的组 ID。
            $dir['path'] = dirname($file);//返回路径中的目录名称部分。
            $dir['atime'] = fileatime($file);//返回文件的上次访问时间。
            $dir['ctime'] = filectime($file);//返回文件的上次改变时间。
            $dir['perms'] = fileperms($file);//返回文件的权限。
            $dir['size'] = filesize($file);//返回文件大小。
            $dir['type'] = filetype($file);//返回文件类型。
            $dir['ext'] = is_file($file) ? pathinfo($file, PATHINFO_EXTENSION) : '';//返回文件后缀名
            $dir['mtime'] = filemtime($file);//返回文件的上次修改时间。
            $dir['isDir'] = is_dir($file);//判断指定的文件名是否是一个目录。
            $dir['isFile'] = is_file($file);//判断指定文件是否为常规的文件。
            $dir['isLink'] = is_link($file);//判断指定的文件是否是连接。
            $dir['isReadable'] = is_readable($file);//判断文件是否可读。
            $dir['isWritable'] = is_writable($file);//判断文件是否可写。
            $dir['isUpload'] = is_uploaded_file($file);//判断文件是否是通过 HTTP POST 上传的。

            return $dir;
        }

        /**
         * 返回关于打开文件的信息
         *
         * 数字下标     关联键名（自 PHP 4.0.6）     说明
         * 0     dev     设备名
         * 1     ino     号码
         * 2     mode     inode 保护模式
         * 3     nlink     被连接数目
         * 4     uid     所有者的用户 id
         * 5     gid     所有者的组 id
         * 6     rdev     设备类型，如果是 inode 设备的话
         * 7     size     文件大小的字节数
         * 8     atime     上次访问时间（Unix 时间戳）
         * 9     mtime     上次修改时间（Unix 时间戳）
         * 10     ctime     上次改变时间（Unix 时间戳）
         * 11     blksize     文件系统 IO 的块大小
         * 12     blocks     所占据块的数目
         *
         * @param $file
         *
         * @return array
         */
        public function open_info($file)
        {
            $file = fopen($file, 'rb');
            $result = fstat($file);
            fclose($file);

            return $result;
        }

        /**
         * 改变文件和目录的相关属性
         *
         * @param string $file 文件路径
         * @param string $type 操作类型
         * @param string $ch_info 操作信息
         *
         * @return bool
         */
        public function change_file(string $file, string $type, string $ch_info)
        {
            switch ($type) {
                case 'group' :
                    return chgrp($file, $ch_info);//改变文件组。
                    break;
                case 'mode' :
                    return chmod($file, $ch_info);//改变文件模式。
                    break;
                case 'ower' :
                    return chown($file, $ch_info);//改变文件所有者。
                    break;
                default:
                    return false;
                    break;
            }
        }

        /**
         * 取得文件路径信息
         *
         * @param string $path 完整路径
         *
         * @return string|string[]
         */
        public function get_file_type(string $path)
        {
            /*
            //pathinfo() 函数以数组的形式返回文件路径的信息。
            $file_info = pathinfo($path); echo file_info["extension"];
            extension取得文件后缀名【pathinfo($path,PATHINFO_EXTENSION)】
            dirname取得文件路径【pathinfo($path,PATHINFO_DIRNAME)】
            basename取得文件完整文件名【pathinfo($path,PATHINFO_BASENAME)】
            filename取得文件名【pathinfo($path,PATHINFO_FILENAME)】
            */
            return pathinfo($path);
        }

        /**
         * 取得上传文件信息
         *
         * @param string $file file属性信息
         *
         * @return array
         */
        public function get_upload_file_info(string $file)
        {
            $file_info = $_FILES[$file];//取得上传文件基本信息
            $info = array();
            //取得文件类型
            $info['type'] = strtolower(trim(stripslashes(preg_replace('/^(.+?);.*$/', "\\1", $file_info['type'])), '"'));
            $info['temp'] = $file_info['tmp_name'];//取得上传文件在服务器中临时保存目录
            $info['size'] = $file_info['size'];//取得上传文件大小
            $info['error'] = $file_info['error'];//取得文件上传错误
            $info['name'] = $file_info['name'];//取得上传文件名
            $info['ext'] = $this->get_ext($file_info['name']);//取得上传文件后缀

            return $info;
        }

        /**
         * 设置文件命名规则
         *
         * @param string $type
         *
         * @return string
         */
        public function set_file_name(string $type)
        {
            switch ($type) {
                case 'hash' :
                    $new_file = md5(uniqid(mt_rand(), true));//mt_srand()以随机数md5加密来命名
                    break;
                case 'time' :
                    $new_file = time();
                    break;
                default :
                    $new_file = date($type, time());//以时间格式来命名
                    break;
            }

            return $new_file . '';
        }

        public function down_remote_file($url, $save_dir = '', $filename = '', $type = 0)
        {
            if (trim($url) == '') {
                return array('file_name' => '', 'save_path' => '', 'error' => 1);
            }
            if (trim($save_dir) == '') {
                $save_dir = './';
            }
            if (trim($filename) == '') {//保存文件名
                $ext = strrchr($url, '.');
                // if($ext!=".gif"&&$ext!=".jpg"){
                // return array("file_name"=>"","save_path"=>"","error"=>3);
                // }

                $filename = time() . $ext;
            }
            if (0 !== strrpos($save_dir, '/')) {
                $save_dir .= DIRECTORY_SEPARATOR;
            }
            // 创建保存目录
            if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true) && !is_dir($save_dir)) {
                return array('file_name' => '', 'save_path' => '', 'error' => 5);
            }
            // 获取远程文件所采用的方法
            if ($type) {
                $ch = curl_init();
                $timeout = 5;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $img = curl_exec($ch);
                curl_close($ch);
            } else {
                ob_start();
                readfile($url);
                $img = ob_get_clean();
            }
            // $size=strlen($img);
            // 文件大小
            $fp2 = fopen($save_dir . $filename, 'ab');

            fwrite($fp2, $img);
            fclose($fp2);
            unset($img, $url);

            return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0);
        }

        /**
         * 获取文件的哈希散列值
         *
         * @access public
         *
         * @param string $type 类型
         *
         * @return string
         */
        public function hash($type = 'sha1')
        {
            if (!isset($this->hash[$type])) {
                $this->hash[$type] = hash_file($type, $this->getFileName());
            }

            return $this->hash[$type];
        }

        /**
         * 文件尺寸转换，将大小将字节转为各种单位大小
         *
         * @param int $bytes 字节大小
         *
         * @return    string    转换后带单位的大小
         */
        public static function toSize($bytes)
        {                        //自定义一个文件大小单位转换函数
            if ($bytes >= (2 ** 40)) {                        //如果提供的字节数大于等于2的40次方，则条件成立
                $return = round($bytes / (1024 ** 4), 2);    //将字节大小转换为同等的T大小
                $suffix = 'TB';                                //单位为TB
            } elseif ($bytes >= (2 ** 30)) {                //如果提供的字节数大于等于2的30次方，则条件成立
                $return = round($bytes / (1024 ** 3), 2);    //将字节大小转换为同等的G大小
                $suffix = 'GB';                                //单位为GB
            } elseif ($bytes >= (2 ** 20)) {                    //如果提供的字节数大于等于2的20次方，则条件成立
                $return = round($bytes / (1024 ** 2), 2);    //将字节大小转换为同等的M大小
                $suffix = 'MB';                                //单位为MB
            } elseif ($bytes >= (2 ** 10)) {                    //如果提供的字节数大于等于2的10次方，则条件成立
                $return = round($bytes / (1024 ** 1), 2);    //将字节大小转换为同等的K大小
                $suffix = 'KB';                                //单位为KB
            } else {                                            //否则提供的字节数小于2的10次方，则条件成立
                $return = $bytes;                            //字节大小单位不变
                $suffix = 'Byte';                            //单位为Byte
            }

            return $return . ' ' . $suffix;                    //返回合适的文件大小和单位
        }

        /**
         * 获取上传后的文件名称
         *
         * @param void     没有参数
         *
         * @return string 上传后，新文件的名称
         */
        // public function getFileName(){return $this->newFileName;}
        /**
         * 获取上传前的文件名称
         */
        // public function getOriginName(){return $this->originName;}

        /** 获取文件类型 */
        // public function getFileType(){return $this->fileType;}
        /** 获取文件后缀 */
        // function getFileSuffix(){return $this->fileSuffix;}
        /** 获取文件大小 */
        // public function getFileSize(){return $this->fileSize;}

        /** 获取文件加密数据*/
        // public function getFileETag(){return $this->fileETag;}
        /** 返回文件的上次改变时间。*/
        // public function getFileCTime(){ return $this->fileCTime;}
        /** 返回文件的上次修改时间。*/
        // public function getFileMTime(){ return $this->fileMTime;}
        /**    返回文件的上次访问时间。*/
        // public function getFileATime(){ return $this->fileATime;}
        /**    返回文件的组 ID。*/
        // public function getFileGroup(){ return $this->fileGroup;}
        /**    返回文件的 inode 编号。*/
        // public function getFileInode(){ return $this->fileInode;}
        /**    文件的 user ID （所有者）。*/
        // public function getFileOwner(){ return $this->fileOwner;}
        /**    返回文件的权限。*/
        // public function getFilePerms(){ return $this->filePerms;}

        /**
         * 设置出错信息
         *
         * @param String $msg 错误信息
         */
        public function setError(string $msg)
        {
            $this->info['error'][] = $msg;
        }

        /**
         * 获取错误信息（支持多语言）
         *
         * @access public
         * @return string
         */
        /*
        private function getError() {
            $str = "上传文件<font color="red">{$this->getOriginName()}</font>时出错 : ";
            switch ($this->getErrorNum()) {
                case 4: $str .= "没有文件被上传"; break;
                case 3: $str .= "文件只有部分被上传"; break;
                case 2: $str .= "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值"; break;
                case 1: $str .= "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值"; break;
                case -1: $str .= "未允许类型"; break;
                case -2: $str .= "文件过大,上传的文件不能超过{$this->getMaxSize()}个字节"; break;
                case -3: $str .= "上传失败"; break;
                case -4: $str .= "建立存放上传文件目录失败，请重新指定上传目录"; break;
                case -5: $str .= "必须指定上传文件的路径"; break;
                default: $str .= "未知错误";
            }

            if (is_array($this->error)) {
                list($msg, $vars) = $this->error;
            } else {
                $msg  = $this->error;
                $vars = [];
            }
        //	return Lang::has($msg) ? Lang::get($msg, $vars) : $msg;

            return $str."<br>";
        }
        */

    }