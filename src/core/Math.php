<?php
    declare (strict_types=1);

    namespace mark\core;

    /**
     * 对PHP Math方法进行完善，
     * 完善原方法中只能对小数点之后进行取舍的操作
     *
     * Class Math
     * @package mark\core
     */
    class Math
    {
        private function __construct()
        {
        }

        /**
         * 进一法取整
         *
         * @param     $value
         * @param int $precision
         *
         * @return float|int
         */
        public static function ceil($value, $precision = 0)
        {
            return ceil($value / (10 ** (int)$precision)) * (10 ** (int)$precision);
        }

        /**
         * 舍去法取整
         *
         *
         * @param     $value
         * @param int $precision
         *
         * @return float|int
         */
        public static function floor($value, $precision = 0)
        {
            return floor($value / (10 ** (int)$precision)) * (10 ** (int)$precision);
        }

        /**
         * 对浮点数进行四舍五入
         *
         * @param     $value
         * @param int $precision
         *
         * @return float|int
         */
        public static function round($value, $precision = 0)
        {
            return round($value / (10 ** (int)$precision)) * (10 ** (int)$precision);
        }

        /**
         * 货币转大写 2.0
         * @param string $money
         * @deprecated
         */
        public static function cny_to_upcase(string $money)
        {
            return self::cnytoupper($money);
        }

        /**
         * 货币转大写 3.0
         *
         * @param string $money
         *
         * @return string
         */
        public static function cnytoupper(string $money)
        {
            if ($money === '' || $money === null || empty($money)) {
                return '零圆整';
            }

            $digitArr1 = [1 => '', 2 => '拾', 3 => '佰', 4 => '仟'];
            $Array = [0 => '', 1 => '萬', 2 => '億'];
            $intArr = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
            $decimalArr = [0 => '角', 1 => '分', 2 => '厘', 3 => '毫'];
            $int = null;
            $decimal = null;

            if (false !== strpos($money, '.')) {
                $spreator = explode('.', $money);
                $int = reset($spreator);
                $decimal = end($spreator);
            } else {
                $int = (string)$money;
            }

            $combine = '';
            $residue = floor((strlen($int) / 4));
            $mol = strlen($int) % 4;
            for ($b = $residue + 1; $b >= 1;) {
                $length = $b == ($residue + 1) ? $mol : 4;
                $b--;
                $st = substr($int, (int)($b * (-4)) - 4, $length);
                if ($st !== '') {
                    for ($a = 0, $aMax = strlen($st); $a < $aMax; $a++) {
                        if ((int)$st[$a] === 0) {
                            $combine .= '零';
                        } else {
                            $combine .= $intArr[(int)$st[$a]] . $digitArr1[strlen($st) - $a];
                        }
                    }
                    $combine .= $Array[$b];
                }
            }

            $combine1 = '';
            if ($decimal !== null || (int)$decimal !== 0 || $decimal != '') {
                for ($i = 0; $i < (strlen($decimal) < 4 ? strlen($decimal) : 4); $i++) {
                    if ((int)$decimal[$i] === 0) {
                        $combine1 .= '';
                    } else {
                        $combine1 .= $intArr[(int)$decimal[$i]] . $decimalArr[$i];
                    }
                }
            } else {
                $combine1 .= '整';
            }

            $combine = $combine . '圆' . $combine1;
            $j = 0;
            $slen = strlen($combine);
            while ($j < $slen) {
                $m = substr($combine, $j, 6);
                if ($m === '零圆' || $m === '零萬' || $m === '零億' || $m === '零零') {
                    $left = substr($combine, 0, $j);
                    $right = substr($combine, $j + 3);
                    $combine = $left . $right;
                    $j = $j - 3;
                    $slen = $slen - 3;
                }
                $j = $j + 3;
            }

            return $combine;
        }

    }