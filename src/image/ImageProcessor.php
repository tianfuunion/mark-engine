<?php

declare (strict_types=1);

namespace mark\image;

use Imagick;
use ImagickDraw;
use ImagickPixel;
use mark\filesystem\Explorer;

/**+==============================
 * |  名称：MarkEngine
 * +--------------------------------------------------
 * |  文件：Image Processor php
 * +--------------------------------------------------
 * |  概要: 图片处理类.
 * +--------------------------------------------------
 * |  版权：Copyright (c) 2017~2020 https://mark.tianfu.ink All rights reserved.
 * +--------------------------------------------------
 * |  许可：Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +--------------------------------------------------
 * |  作者：Author: Mark <mark@tianfuunion.cn>
 * +--------------------------------------------------
 * |  创建时间: 2018-12-12
 * +--------------------------------------------------
 * |  修改时间: 2019-11-05
 * +--------------------------------------------------
 **
 * 注意：方法中的文件路径有待测试
 *
 * @version 1.0
 * @time    2019-06-12 17:16:00
 * @TODO    ：1、资源的输出，echo 改为 return后图片不显示 \n ：2、mysql title为中文后，name查询到的结果不显示后缀
 **==============================
 * Class ImageProcessor
 *
 * @package mark\image
 */
class ImageProcessor {

    private $maxsize;    //限制文件上传大小，单位是字节,可以使用set()设置

    private $israndname = true;    //设置是否随机重命名 false为不随机,可以使用set()设置
    private $thumb = array();        //设置缩放图片,可以使用set()设置
    private $watermark = array();    //设置为图片加水印,可以使用set()设置
    private $originName;        //源文件名
    private $tmpFileName;        //临时文件名
    private $fileType;            //文件类型(文件后缀)
    private $fileSize;            //文件大小
    private $newFileName;        //新文件名

    private $errorNum = 0;        //错误号
    private $errorMess = '';        //错误报告消息

    private $storage_path;
    private $bucket_path;
    private $images_path;
    private $thumbs_path;

    private $video_path;
    private $temp_path;
    private $file_path;
    private $cache_path;
    /**@var Imagick */
    private $image;
    private $type;

    private $fileinfo; // 文件信息，文件名，后缀
    private $param; //请求参数
    // private $processor; // 图片处理规则

    private $logcat;

    /**
     * ImageProcessor constructor.
     *
     * @param null   $param
     * @param string $path
     */
    public function __construct($param = null, string $path) {
        if ($param != null) {
            $this->param = $param;
        } else {
            $this->logcat("debug", 'ImageProcessor::__construct(param is Null)');
        }

        /*
            if($processor != null){
                $this->processor = $processor;
            }
        */

        if ($path && Explorer::isdir($path)) {
            // $this->storage_path = $_SERVER["DOCUMENT_ROOT"] . "/storage/";
            $this->storage_path = rtrim($path, DIRECTORY_SEPARATOR);    // TODO：处理需要处理路径分割符问题
            $this->bucket_path = $this->storage_path . '/bucket/';
            $this->images_path = $this->storage_path . '/images/';
            $this->thumbs_path = $this->storage_path . '/thumbs/';

            // $this->video_path	= $this->storage_path . "/video/";
            $this->temp_path = $this->storage_path . '/temp/';
            // $this->file_path	= $this->storage_path . "/file/";
            $this->cache_path = $this->storage_path . '/cache/';

            Explorer::isdir($this->storage_path);
            Explorer::isdir($this->bucket_path);
            Explorer::isdir($this->images_path);
            Explorer::isdir($this->thumbs_path);
            Explorer::isdir($this->temp_path);
            Explorer::isdir($this->cache_path);
            // $this->isDirectory($this->file_path);
            // $this->isDirectory($this->video_path);
        } else {
            $this->Logcat('debug', 'ImageProcessor::isDirectory()' . $path);
        }

    }

    /**
     * 析构函数 * 销毁图像资源
     *
     */
    public function __destruct() {
        if ($this->image !== null) {
            $this->image->clear();
            $this->image->destroy();
        }
    }

    /**
     * 用于设置成员属性（$path, $allowtype,$maxsize, $israndname, $thumb,$watermark）
     * 可以通过连贯操作一次设置多个属性值
     *
     * @param string $key 成员属性名(不区分大小写)
     * @param mixed  $val 为成员属性设置的值
     *
     * @return    object        返回自己对象$this
     */
    public function set($key, $val) {
        $key = strtolower($key);
        if (array_key_exists($key, get_class_vars(get_class($this)))) {
            $this->setOption($key, $val);
        }

        return $this;
    }

    /**
     * 为单个成员属性设置值
     *
     * @param $key
     * @param $val
     */
    private function setOption($key, $val) {
        $this->$key = $val;
    }

    /**
     * 根据请求参数生成文件名
     * 注意：按请求参数(手写的)存储生成后的文件，可能有冗余。如果请求参数为后台按功能生成的，可避免有多余参数（参考AliOss图片处理）。
     *
     * @param $param
     *
     * @return string
     */
    private function joinParam($param) {
        // 二、图片处理样式：
        $format = array(
            // 1、图像：
            'object' => 'example.jpg',
            'path' => '',
            'format' => 'webp', // format,jpeg 格式转换
            'x-coss-process' => 'tf_image_format', //自定义图片处理规则
            'name' => 'tf_image_format', //自定义图片处理规则
            'interlace' => 'bool', //渐进显示：用以开启先模糊后清晰的呈现方式，仅对格式为 JPG 的图片有效
            'orient' => 'auto|bool', // 自适应方向：会根据图片中 EXIF 旋转信息先旋转后进行缩略，建议默认开启。
            'quality' => '90', // 图片质量：（相对质量、绝对质量）[90%]质量参数仅对 JPG 和 WEBP 的图片有效，且对 WEBP 来说，相对质量等价于绝对质量。

            'region' => 'henan',// （地区）

            /*
            2、缩略方式：｛
                不使用缩略：resize=none|null
                等比缩小：1-100（100则不缩小）
                等比放大：100-1000（100则不放大）
                指定宽高绽放：(1--4096px)
                {
                1、宽度固定，高度自适应：resize=lfit,w_200
                2、高度固定，宽度自适应：resize=lfit,h_200
                3、固定宽高，按长边缩放：resize=lfit,w_200,h_200
                4、固定宽高，按短边缩放：resize=mfit,w_200,h_200
                5、固定宽高，缩略填充：resize=pad,w_200,h_200
                6、固定宽高，居中裁剪：resize=fill,w_200,h_200
                7、强制宽高：resize=fixed,w_200,h_200
            }｝*/
            'resize' => 'null|none|cover|lfit|mfit|pad|fill|fixed',
            'scale' => '100', // 等比缩放比例｛1-100-1000｝（默认100不缩放）
            'width' => '375', // 指定宽高绽放:宽度（1--4096）px
            'height' => '225', // 指定宽高绽放:高度（1--4096）px
            'limit' => 'bool', // 限制放大（开关式，默认开）如果请求的图片比原图大，默认返回的仍是原图。如果想取到放大的图片，请关闭此选项。

            //	3、效果
            'bright' => '0', // 图片亮度：bright=（-100 -- +100）（0为不变）
            'contrast' => '0', // 图片对比度：contrast=（-100 -- +100）(0为不变)
            'sharpen' => '', // 图片锐化：sharpen,399（50 -- 399）（是否开启）
            'blur_radius' => '1-50', // 图片模糊：blur=｛模糊半径（radius_1--50）,模糊标准差（standard_1--50）｝
            'blur_standard' => '1-50',
            'rotate' => '0', // 旋转角度（0--360 默认0）

            /** 4、水印
             * 图片预览失败，可能原因：
             * 若有图片水印，请确保水印文件路径正确，并且所在 Bucket 的 ACL 为 公共读；
             * 若您正在使用 高级编辑，请确保样式代码正确。
             */
            'watermark' => 'null|none|text|image', // 水印方式：不使用水印：（默认）

            // 图片水印：
            'water_image' => 'image_path',
            //图片水印路径<input id="watermark.image.path" placeholder="请输入当前 Bucket 下的图片路径。" height="100%" autocomplete="off" value="" data-spm-anchor-id="5176.8466084.0.i71.40cf1450hgaZ9V">

            'water_pre' => 'bool',//水印图预处理:(是否开启)
            'water_ratio' => 10, // 占比：(1--100)水印图占主图的百分比，将利用它根据主图的大小来动态调整水印图片的大小。
            'water_bright' => 0,// 亮度：(-100 -- +100)
            'water_contrast' => 0, // 对比度：(-100 -- +100)

            // 文字水印
            'water_text' => 'base64_encode(string)', //文字内容：1-16个字符（中文算一个，可以有特殊字符，base64加密）
            'water_font' => 'none', //文字字体、大小：（选择式:使用默认字体,文泉驿正黑,文泉微米黑,方正书宋,方正楷体,方正黑体,方正仿宋,DroidSans Fallback)（1--999px）
            'water_size' => '10',
            'water_color' => 'ffffff', // 水印文字颜色：color_853737,（使用RGB式，其它方式为参照选择，最终转换为RGB）
            'water_fill' => 'null|bool', // 文字铺满：,（是否启用，默认关闭）
            'water_shadow' => '10', // 文字阴影：{shadow_100（是否启用，默认关闭）,阴影透明度：（1--100）（默认100）}
            'water_opacity' => '1', // 水印透明度：t_40（0--100）（默认100）（水印位置：与图片水印一致）	}	]

            'water_gravity' => 'center', // 水印位置：上g_north、下g_south、右g_east、左g_west、（左下g_sw、右下g_se、左上g_nw、右上g_ne、中间g_center）默认右下
            'water_x' => '10', // 水平边距（1--4096）默认10px
            'water_y' => '10', // 垂直边距（1--4096）默认10px
            'expires' => 0,    // 缩略图过期时间
        );
        // 返回参数交集，
        $result = array_intersect_key($param, $format);
        // 参数排序
        if (ksort($result)) {
            $result['ksort'] = true;
        }
        // 参数值转字符串
        $separated = implode(',', $result);

        // 参数加密 & 加指定后缀
        return md5($separated) . '.' . $result['format'];
    }

    /**
     * 获取源文件
     * TODO：文件信息与规则信息可分开或者再完善一下，（文件路径）
     * Array
     * (
     * [dirname] => /testweb
     * [basename] => test.txt
     * [extension] => txt
     * )
     *
     * @return bool|string
     */
    private function getSourceFile() {
        // $sourceFile = $this->images_path . $this->param["object"];
        $sourceFile = $this->param['path'] . $this->param['object'];
        header('X-Coss-Source: ' . $sourceFile);
        if (is_file($sourceFile) && file_exists(dirname($sourceFile))) {
            return $sourceFile;
        }

        return false;
    }

    /**
     * 获取生成后的文件
     * 注意：此文件的文件名与请求参数有关，不同的请求参数生成的文件不同
     * 遗留问题：PHP禁用mkdir(0777),权限。建立目录后无法再次写入文件
     * 问题备注：未查询目录权限，但可以获取到对应的缩略文件
     *
     * @return bool|string
     */
    private function getThumbFile() {
        $thumbFile = $this->getThumbFilePath();
        header('X-Coss-Thumbs: ' . $thumbFile);

        if (is_file($thumbFile) && file_exists(dirname($thumbFile))) {
            return $thumbFile;
        }

        return false;
    }

    /**
     * 获取缩略图文件路径
     *
     * @return string
     */
    private function getThumbFilePath() {
        return $this->thumbs_path . $this->joinParam($this->param);
    }

    /**
     * 检查缩略文件是否过期
     * True已过期
     * False未过期
     *
     * @return bool
     */
    private function isExpires() {
        header('X-expires:' . $this->param['expires']);

        return $this->param['expires'] != 0 && (time() - filemtime($this->getThumbFile())) > $this->param['expires'];
    }

    /**
     * 获取对象文件
     *
     * @param string $outtype { "stream", "filepath" }
     *
     * @throws \ImagickException
     */
    public function getImage($outtype = 'stream') {
        $startTime = microtime(true);

        header('X-method:get');
        if (strtolower($this->param['format']) === 'jpg') {
            $this->param['format'] = 'jpeg';
        }

        $this->Logcat('info', "ImageProcessor::getImage()" . json_encode($this->param));

        $sourceFile = $this->getSourceFile();

        // 检测是否有已经生成的图片，有则直接返回
        if (!$this->param['process'] === 'source' && $thumbFile = $this->getThumbFile()) {
            if ($thumbFile !== false && $this->isExpires() == false) {
                if ($outtype === 'stream') {
                    $this->image = $pallete = new Imagick($thumbFile); // new一个新的画布对象
                    echo $pallete;
                } else {
                    echo $thumbFile;
                }
                exit(1);
            }
        } elseif ($sourceFile) {

        } else {
            $data["param"] = $this->param;

            $data["get"] = $_GET;
            $data["post"] = $_POST;
            $data["file"] = $_FILES;
            $data["server"] = $_SERVER;
            $data["session"] = $_SESSION;
            $data["request"] = $_REQUEST;
            $data["cookie"] = $_COOKIE;
            // $data["rawdata"]= $HTTP_RAW_POST_DATA;
            $data["head"] = getallheaders();

            // Log::info("Bucket::GET(410)" . json_encode($data));

            header("Status: 410 Invalid request file " . $this->param["object"]);
            header("X-status: 410 Invalid request file " . $this->param["object"]);
            header("X-object: " . $this->param["object"]);
            echo new Imagick($this->images_path . "default.jpg");
            exit(1);
        }

        // $this->image = $pallete = new Imagick(realpath($sourceFile)) ; // new一个新的画布对象
        $this->image = $pallete = new Imagick($sourceFile); // new一个新的画布对象
        // $pallete->newimage($this->param["width"], $this->param["height"], "transparent"); // 创建画布
        // $pallete->newimage($this->param["width"], $this->param["height"]); // 创建画布(可显示图片，但为原图片尺寸)
        // $pallete->drawImage($this->getSourceFile());

        /**
         * 宽高缩放
         */
        list($dst_w, $dst_h) = getimagesize($sourceFile); // 获取原图尺寸

        $resize = $this->param['resize'];
        header('X-coss-type: ' . gettype($resize));
        switch ($resize) {
            case 'zoom': // 等比缩放
                if (isset($this->param['scale'])) {
                    // $pallete->thumbnailImage($dst_w * intval($this->param["scale"]) / 100, $dst_h * intval($this->param["scale"]) / 100);
                    $w = $dst_w * (int)$this->param['scale'] / 100;
                    $h = $dst_h * (int)$this->param['scale'] / 100;

                    $pallete->thumbnailImage($w, $h);
                    header('X-coss-size: ' . $w . ' ' . $h);
                } else {
                    $pallete->thumbnailImage($dst_w, $dst_h);
                }
                break;

            case 'wfit': //宽度固定，高度自适应
                if (isset($this->param['width'])) {
                    $width = (int)$this->param["width"];
                    $scale = $width / $dst_w;
                    $pallete->thumbnailImage($this->param['width'], $dst_h * $scale, true, true);
                } else {
                    // 0、没有给出宽高值
                    $pallete->thumbnailImage($dst_w, $dst_h, true, true);
                }
                break;
            case 'hfit': // 高度固定，宽度自适应
                if (isset($this->param['height'])) {
                    $height = (int)$this->param["height"];
                    $scale = $height / $dst_h;
                    $pallete->thumbnailImage($dst_w * $scale, $this->param['height'], true, true);
                } else {
                    // 0、没有给出宽高值
                    $pallete->thumbnailImage($dst_w, $dst_h, true, true);
                }
                break;
            case 'lfit':// 3、固定宽高，按长边缩放：resize,m_lfit,w_200,h_200
                if (isset($this->param['width'], $this->param['height'])) {
                    $width = (int)$this->param["width"];
                    $height = (int)$this->param["height"];
                    if ($width >= $height) {
                        // 宽图
                        $scale = $width / $dst_w;
                        //		$pallete->thumbnailImage($width, $dst_h * $scale, true, true);
                        $pallete->thumbnailImage($width, $dst_h * $scale);
                    } else {
                        // 高图
                        $scale = $height / $dst_h;
                        $pallete->thumbnailImage($dst_w * $scale, $this->param['height'], true, true);
                    }
                } else {
                    if (isset($this->param['width'])) {
                        $width = (int)$this->param['width'];
                        // 1、宽度固定，高度自适应：resize,m_lfit,w_200
                        $scale = $width / $dst_w;
                        $pallete->thumbnailImage($this->param['width'], $dst_h * $scale, true, true);
                    } else {
                        if (isset($this->param['height'])) {
                            $height = (int)$this->param['height'];
                            //	2、高度固定，宽度自适应：resize,m_lfit,h_200
                            $scale = $height / $dst_h;
                            $pallete->thumbnailImage($dst_w * $scale, $this->param['height'], true, true);
                        } else {
                            // 0、没有给出宽高值
                            $pallete->thumbnailImage($dst_w, $dst_h, true, true);
                        }
                    }
                }
                break;
            case 'mfit': // 4、固定宽高，按短边缩放：resize,m_mfit,w_200,h_200
                $width = (int)$this->param["width"];
                $height = (int)$this->param["height"];
                if (isset($this->param['width'], $this->param['height'])) {
                    if ($width <= $height) {
                        // 宽图
                        $scale = $width / $dst_w;
                        $pallete->thumbnailImage($this->param['width'], $dst_h * $scale, true, true);
                    } else {
                        // 高图
                        $scale = $height / $dst_h;
                        $pallete->thumbnailImage($dst_w * $scale, $this->param['height'], true, true);
                    }
                } else {
                    if (isset($this->param['width'])) {
                        // 1、宽度固定，高度自适应：resize,m_lfit,w_200
                        $scale = $width / $dst_w;
                        $pallete->thumbnailImage($this->param['width'], $dst_h * $scale, true, true);
                    } else {
                        if (isset($this->param['height'])) {
                            //	2、高度固定，宽度自适应：resize,m_lfit,h_200
                            $scale = $height / $dst_h;
                            $pallete->thumbnailImage($dst_w * $scale, $this->param['height'], true, true);
                        } else {
                            // 0、没有给出宽高值
                            $pallete->thumbnailImage($dst_w, $dst_h, true, true);
                        }
                    }
                }
                break;
            case 'pad': // 5、固定宽高，缩略填充：resize,m_pad,w_200,h_200
                if (isset($this->param['width'], $this->param['height'])) {
                    $width = (int)$this->param['width'];
                    $height = (int)$this->param['height'];
                    if ($width >= $height) {
                        // 宽图
                        $scale = $width / $dst_w;
                        $pallete->thumbnailImage($this->param['width'], $dst_h * $scale, true, true);
                    } else {
                        // 高图
                        $scale = $height / $dst_h;
                        $pallete->thumbnailImage($dst_w * $scale, $this->param['height'], true, true);
                    }
                } else {
                    if (isset($this->param['width'])) {
                        $width = (int)$this->param['width'];
                        // 1、宽度固定，高度自适应：resize,m_lfit,w_200
                        $scale = $width / $dst_w;
                        $pallete->thumbnailImage($this->param['width'], $dst_h * $scale, true, true);
                    } else {
                        if (isset($this->param['height'])) {
                            $height = (int)$this->param['height'];
                            //	2、高度固定，宽度自适应：resize,m_lfit,h_200
                            $scale = $height / $dst_h;
                            $pallete->thumbnailImage($dst_w * $scale, $this->param['height'], true, true);
                        } else {
                            // 0、没有给出宽高值
                            $pallete->thumbnailImage($dst_w, $dst_h, true, true);
                        }
                    }
                }
                break;
            case 'fill': // 6、固定宽高，居中裁剪：resize,m_fill,w_200,h_200
                break;
            case 'fixed': // 7、强制宽高：resize,m_fixed,w_200,h_200
                break;
            default:
                $pallete->thumbnailImage($dst_w, $dst_h, true, true);
                break;
        }

        /**自适应方向**/
        if ($this->param['autoorient'] == true || $this->param['autoorient'] == 1) {
            $orientation = $pallete->getImageOrientation();
            header('X-coss-orient:' . $orientation);
            switch ($orientation) {
                case Imagick::ORIENTATION_BOTTOMRIGHT:
                    $pallete->rotateImage('#000', 180);
                    break;
                case Imagick::ORIENTATION_RIGHTTOP:
                    $pallete->rotateImage('#000', 90);
                    break;
                case Imagick::ORIENTATION_LEFTBOTTOM:
                    $pallete->rotateImage('#000', -90);
                    break;
            }
            $pallete->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
        }

        /**
         * 添加水印
         */
        if ($this->param['watermark'] === 'text') {
            // 文字水印
            header('X-water:text');

            /*
            函数说明：将字母和数字生成png图片
            函数参数：
            $text:需要生成图片的文字,string型
            $color:文字颜色,string型
            $szie:文字大小,int型
            $font:字体,string型
            $type:返回类型,逻辑型,true:返回图片地址; false:返回图片资源
            $src:保存图片的地址,string型
            */
            // function text ($text, $color, $size, $font, $type = false, $src = '');

            $draw = new ImagickDraw();        // new 画笔

            // 设置水印文本的9个位置
            switch ($this->param['water_gravity']) {
                case 'north': // 上
                    $draw->setgravity(Imagick::GRAVITY_NORTH);
                    break;
                case 'south': // 下
                    $draw->setgravity(Imagick::GRAVITY_SOUTH);
                    break;
                case 'west': // 左
                    $draw->setgravity(Imagick::GRAVITY_WEST);
                    break;
                case 'east': // 右
                    $draw->setgravity(Imagick::GRAVITY_EAST);
                    break;
                case 'center': // 中间
                    $draw->setgravity(Imagick::GRAVITY_CENTER);
                    break;
                case 'northwest': // 左上
                    $draw->setgravity(Imagick::GRAVITY_NORTHWEST);
                    break;
                case 'northeast': // 右上
                    $draw->setgravity(Imagick::GRAVITY_NORTHEAST);
                    break;
                case 'southwest': // 左下
                    $draw->setgravity(Imagick::GRAVITY_SOUTHWEST);
                    break;
                case 'southeast': // 右下
                default:
                    $draw->setgravity(Imagick::GRAVITY_SOUTHEAST);
                    break;
            }

            // $font = "include/font/" . $font . ".ttf";
            if (
                !isset($this->param['water_font']) ||
                $this->param['water_font'] === 'default' ||
                $this->param['water_font'] === 'none' ||
                $this->param['water_font'] === 'undefined') {
                $this->param['water_font'] = 'simhei.ttf';
            }
            // TODO：字体路径问题？不应该写成固定
            // $draw->setfont(COMMONS . "typeface/".$this->param["water_font"].".ttf"); // 设置字体
            // $draw->setfont(COMMONS . "/typeface/".$this->param["water_font"]); // 设置字体
            $typeface = $_SERVER['DOCUMENT_ROOT'] . '/public/commons/typeface/' . $this->param['water_font'];
            if (file_exists($typeface)) {
                $draw->setfont($_SERVER['DOCUMENT_ROOT'] . '/public/commons/typeface/' . $this->param['water_font']); // 设置字体
            }
            header('Typeface:' . $this->param['water_font']);
            header('Typeface_path:' . $typeface);

            if (isset($this->param['water_size']) && !empty($this->param['water_size'])) {
                $fontSize = (int)$this->param['water_size'];
                if ($fontSize <= 1) {
                    $fontSize = 1;
                } elseif ($fontSize >= 999) {
                    $fontSize = 999;
                }
                $draw->setFontSize($fontSize); // 设置字体大小
                header('Water_size:' . $fontSize);
            }
            // 设置字体颜色
            if (isset($this->param['water_color']) && !empty($this->param['water_color'])) {
                $draw->setFillColor(new ImagickPixel($this->param['water_color']));
                header('Water_color:' . $this->param['water_color']);
            } else {
                $draw->setFillColor(new ImagickPixel('#000000'));
            }

            // 设置字体透明度
            // if(isset($this->param["water_alpha"])){
            if (isset($this->param['water_opacity']) && !empty($this->param['water_opacity'])) {
                // $fillAlpha = intval($this->param["water_alpha"]);
                $fillAlpha = (int)$this->param['water_opacity'];

                if ($fillAlpha < 0) {
                    $fillAlpha = 0;
                } elseif ($fillAlpha > 100) {
                    $fillAlpha = 100;
                }
                // $draw->setFillAlpha($fillAlpha);
                $draw->setFillOpacity($fillAlpha / 100);
            }
            // 水印文字偏移量X
            if (isset($this->param['water_x'])) {
                $offsetx = (int)$this->param['water_x'];
                if ($offsetx < 1) {
                    $offsetx = 1;
                } elseif ($offsetx > 4096) {
                    $offsetx = 4096;
                }
            } else {
                $offsetx = 10;
            }
            // 水印文字偏移量Y
            if (isset($this->param['water_y'])) {
                $offsety = (int)$this->param['water_y'];
                if ($offsety < 1) {
                    $offsety = 1;
                } elseif ($offsety > 4096) {
                    $offsety = 4096;
                }
            } else {
                $offsety = 10;
            }
            // 水印文字旋转
            if (isset($this->param['water_rotate'])) {
                $offset_rotate = $this->param['water_rotate'];
                if ($offset_rotate < 0) {
                    $offset_rotate = 0;
                } elseif ($offset_rotate > 360) {
                    $offset_rotate = 360;
                }
            } else {
                $offset_rotate = 0;
            }

            if (isset ($this->param['water_under_color'])) {
                // $draw->setTextUnderColor("#".$this->param["water_under_color"]);
            }

            $draw->setTextEncoding('UTF-8');
            // 设置文字水印
            $waterText = base64_decode($this->param['water_text']);
            // $waterText = $this->param["water_text"];
            // 是否铺满整个图片
            if (isset($this->param['water_fill']) && $this->param['water_fill'] == 1) {
                $properties = $pallete->queryFontMetrics($draw, $waterText);
                /*
                ImageProcessor::queryFontMetrics()（{
                    "characterWidth":10,
                    "characterHeight":10,
                    "ascender":8,
                    "descender":-3,
                    "textWidth":0,
                    "textHeight":12,
                    "maxHorizontalAdvance":11,
                    "boundingBox":{"x1":0,"y1":-3,"x2":5,"y2":5},
                    "originX":0,
                    "originY":0
                }
                */
                // Log::info('ImageProcessor::queryFontMetrics()' . json_encode($properties));

                for ($i = 0, $iMax = (int)($this->param['width'] / $properties['textWidth'] + 5); $i <= $iMax; $i++) {
                    for ($j = 0, $jMax = (int)($this->param['height'] / $properties['textHeight'] + 10); $j <= $jMax; $j++) {
                        // 查看是否有旋转
                        if (isset($this->param['water_angle'])) {
                            $pallete->annotateImage(
                                $draw,
                                $properties['textWidth'] * $i + $offsetx,
                                $properties['textWidth'] * $offset_rotate / 100 * $j,
                                $offset_rotate,
                                $waterText
                            );
                        } else {
                            $pallete->annotateImage(
                                $draw,
                                ($properties['textWidth'] + $this->param['water_x']) * $i + $offsetx,
                                ($properties['textHeight'] + $this->param['water_y']) * $j + $offsetx,
                                $offset_rotate,
                                $waterText
                            );
                        }
                    }
                }

            } else {
                $pallete->annotateImage($draw, $offsetx, $offsety, $offset_rotate, $waterText); // 往画布中写入文本
            }
        } elseif ($this->param['watermark'] === 'image') {
            // 图片水印
            header('X-water:image');
        }

        $this->image->setImageFormat(strtolower($this->param['format'])); // 设置图片格式
        $this->image = $pallete;

        if ($outtype === 'stream') {
            echo $pallete;
        }
        // 如果图片不需要在当前php程序中输出，使用写入图片到磁盘函数，上面的设置header也可以去除
        $filename = $this->getThumbFilePath();

        if ($pallete->writeImage($filename) == true) {
            header('X-save:' . $filename);
            echo $filename;
        } else {
            echo $pallete;
        }

        $pallete->clear();
        $pallete->destroy(); //释放资源

        $this->Logcat(
            'ImageProcessor::getImage() RunTime：（' . round((microtime(true) - $startTime), 4) . '秒）（Param=>：' . json_encode(
                $this->param
            ) . '）'
        );

    }

    /**
     * 直接输出图片
     *
     * @param $imgsrc
     */
    public function getImg($imgsrc) {
        $info = getimagesize($imgsrc);
        $imgExt = image_type_to_extension($info[2], false);  //获取文件后缀
        $fun = "imagecreatefrom{$imgExt}";
        $imgInfo = $fun($imgsrc);                     //1.由文件或 URL 创建一个新图象。如:imagecreatefrompng ( string $filename )
        //$mime = $info['mime'];
        $mime = image_type_to_mime_type(exif_imagetype($imgsrc)); //获取图片的 MIME 类型
        header('Content-Type:' . $mime);
        $quality = 100;
        if ($imgExt === 'png') $quality = 9;        //输出质量,JPEG格式(0-100),PNG格式(0-9)
        $getImgInfo = "image{$imgExt}";
        $getImgInfo($imgInfo, null, $quality);    //2.将图像输出到浏览器或文件。如: imagepng ( resource $image )
        imagedestroy($imgInfo);
    }

    private function Logcat($level = 'info', $message = '') {
        $this->logcat[] = array($level, $message);
    }

    /**
     * @return mixed
     */
    public function getLog() {
        return $this->logcat;
    }

}