<?php
    declare (strict_types=1);

    namespace mark\date;

    /**
     * Class Time
     * PHP 获取常用的起始时间戳和结束时间戳的时间处理类：
     * 一、常用时间戳
     * 1、前年、去年、今年、明年、后年
     * 2、前季度、上季度、当季度、下季度、后季度；当年的4个季度
     * 3、前月、上月、当月、下月、后月；当年的12个月起始时间戳
     * 4、前周、上周、本周、下周、后周
     * 5、前天、昨天、今天、明天、后天
     *
     * 二、根据传入的时间戳计算出相应的时间戳。
     * 例：传入 {timestamp} 计算出当天的起始时间戳，Input选择时不易选择当天的起始时间
     *
     * 四、时间戳转换
     * 跨时区
     *
     * timestamp.class.php
     * <{$timestamp.today|date_format:"%Y-%m-%d %H:%M:%S"}>
     */
    class Time
    {

        /**
         * 返回前天开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function beforeday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, (int)date('m', $time), (int)date('d', $time) - 2, (int)date('Y', $time));
            }

            return mktime(23, 59, 59, (int)date('m', $time), (date('d', $time) - 2), (int)date('Y', $time));
        }

        /**
         * 返回昨日开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function yesterday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, (int)date('m', $time), (int)date('d', $time) - 1, (int)date('Y', $time));
            }

            return mktime(0, 0, 0, (int)date('m', $time), (int)date('d', $time), (int)date('Y', $time)) - 1;
        }

        /**
         * 返回今日开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function today($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, (int)date('m', $time), (int)date('d', $time), (int)date('Y', $time));
            }

            return mktime(23, 59, 59, (int)date('m', $time), (int)date('d', $time), (int)date('Y', $time));
        }

        /**
         * 返回明天开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function tomorrow($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(23, 59, 59, (int)date('m', $time), (int)date('d', $time), (int)date('Y', $time)) + 1;
            }

            return mktime(23, 59, 59, (int)date('m', $time), (int)date('d', $time) + 1, (int)date('Y', $time));
        }

        /**
         * 返回后天开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function afterday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, (int)date('m', $time), (int)date('d', $time) + 2, (int)date('Y', $time));
            }

            return mktime(23, 59, 59, (int)date('m', $time), (int)date('d', $time) + 2, (int)date('Y', $time));
        }

        /**
         * 返回周开始或结束的时间戳
         *
         * @param string $flag 默认为周的开始，start null 为开始，end 为结束
         * @param null $time 默认为本周
         *
         * @return false|float|int
         */
        public static function week($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('this week Monday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('next week Monday', $time))) - 1;
        }

        /**
         * 返回本周一开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function monday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('this week Monday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('this week Tuesday', $time))) - 1;
        }

        /**
         * 返回本周二开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function tuesday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('this week Tuesday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('this week Wednesday', $time))) - 1;
        }

        /**
         * 返回本周三开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function wednesday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('this week Wednesday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('this week Thursday', $time))) - 1;
        }

        /**
         * 返回本周四开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function thursday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('this week Thursday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('this week Friday', $time))) - 1;
        }

        /**
         * 返回本周五开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function friday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('this week Friday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('this week Saturday', $time))) - 1;
        }

        /**
         * 返回本周六开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function saturday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('this week Saturday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('this week Sunday', $time))) - 1;
        }

        /**
         * 返回本周日开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function sunday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('this week Sunday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('next week Monday', $time))) - 1;
        }

        /**
         * 返回前周开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function beforeWeek($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('last week Monday', $time))) - 7 * 86400;
            }

            return strtotime(date('Y-m-d', strtotime('last week Monday', $time))) - 1;
        }

        /**
         * 返回上周开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function lastWeek($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('last week Monday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('this week Monday', $time))) - 1;
        }

        /**
         * 返回下周开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function nextWeek($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('next week Monday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('next week Sunday', $time))) + 86399;
        }

        /**
         * 返回后周开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function afterWeek($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('next week Sunday', $time))) + 86400;
            }

            return strtotime(date('Y-m-d', strtotime('next week Sunday', $time))) + 8 * 86399;
        }

        /**
         * 返回上周一开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function lastweekMonday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('last week Monday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('last week Monday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function lastweekTuesday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('last week Tuesday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('last week Tuesday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function lastweekWednesday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('last week Wednesday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('last week Wednesday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function lastweekThursday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('last week Thursday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('last week Thursday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function lastweekFriday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('last week Friday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('last week Friday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function lastweekSaturday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('last week Saturday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('last week Saturday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function lastweekSunday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('last week Sunday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('last week Sunday', $time))) + 86399;
        }

        /**
         * 返回下周一开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function nextweekMonday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('next week Monday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('next week Monday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function nextweekTuesday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('next week Tuesday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('next week Tuesday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function nextweekWednesday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('next week Wednesday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('next week Wednesday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function nextweekThursday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('next week Thursday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('next week Thursday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function nextweekFriday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('next week Friday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('next week Friday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function nextweekSaturday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('next week Saturday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('next week Saturday', $time))) + 86399;
        }

        /**
         * @param string $flag
         * @param null $time
         *
         * @return false|float|int
         */
        public static function nextweekSunday($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return strtotime(date('Y-m-d', strtotime('next week Sunday', $time)));
            }

            return strtotime(date('Y-m-d', strtotime('next week Sunday', $time))) + 86399;
        }

        /**
         * 返回前月开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function beforeMonth($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, (int)date('m', $time) - 2, 1, (int)date('Y', $time));
            }

            return mktime(23, 59, 59, (int)date("m", $time) - 1, 0, (int)date("Y", $time));
        }

        /**
         * 返回上个月开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function lastMonth($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, (int)date('m', $time) - 1, 1, (int)date('Y', $time));
            }

            return mktime(23, 59, 59, (int)date("m", $time), 0, (int)date("Y", $time));
        }

        /**
         * 返回本月开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function month($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, (int)date('m', $time), 1, (int)date('Y', $time));
            }

            return mktime(23, 59, 59, (int)date("m", $time), (int)date("t", $time), (int)date("Y", $time));
        }

        /**
         * 返回下月开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function nextMonth($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(23, 59, 59, (int)date('m', $time), (int)date('t', $time), (int)date('Y', $time)) + 1;
            }

            return mktime(0, 0, 0, (int)date("m", $time) + 1, (int)date("t", $time), (int)date("Y", $time)) - 1;
        }

        /**
         * 返回后月开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function afterMonth($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, (int)date('m', $time) + 2, 1, (int)date('Y', $time));
            }

            return mktime(23, 59, 59, (int)date("m", $time) + 2, (int)date("t", $time), (int)date("Y", $time));
        }

        /**
         * 返回今年开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function year($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, 1, 1, (int)date('Y', $time));
            }

            return mktime(23, 59, 59, 12, 31, (int)date("Y", $time));
        }

        /**
         * 返回前年开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function beforeYear($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, 1, 1, date('Y', $time) - 2);
            }

            return mktime(23, 59, 59, 12, 31, date('Y', $time) - 2);
        }

        /**
         * 返回去年开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function lastYear($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, 1, 1, date('Y', $time) - 1);
            }

            return mktime(23, 59, 59, 12, 31, date('Y', $time) - 1);
        }

        /**
         * 返回明年开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function nextYear($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, 1, 1, date('Y', $time) + 1);
            }

            return mktime(23, 59, 59, 12, 31, date('Y', $time) + 1);
        }

        /**
         * 返回后年开始或结束的时间戳
         *
         * @param string $flag
         * @param null $time
         *
         * @return false|int
         */
        public static function afterYear($flag = '', $time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            if ($flag == '' || $flag === 'start' || $flag === 'begin') {
                return mktime(0, 0, 0, 1, 1, date('Y', $time) + 2);
            }

            return mktime(23, 59, 59, 12, 31, date('Y', $time) + 2);
        }

        /**
         * @param null $time
         *
         * @return false|float|int
         */
        public static function dayOf($time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }

            return strtotime(date('Y-m-d', strtotime('next week Sunday', $time))) + 86399;
        }

        /**
         * 获取几天前零点到现在/昨日结束的时间戳
         *
         * @param int $day 天数
         * @param bool $now 返回现在或者昨天结束时间戳
         *
         * @return array
         */
        public static function dayToNow($day = 1, $now = true): array
        {
            $end = time();
            if (!$now) {
                [$foo, $end] = self::yesterday();
            }

            return array(mktime(0, 0, 0, date('m'), date('d') - $day, date('Y')), $end);
        }

        /**
         * 返回几天前的时间戳
         *
         * @param int $day
         *
         * @return int
         */
        public static function daysAgo($day = 1): int
        {
            return time() - self::daysToSecond($day);
        }

        /**
         * 返回几天后的时间戳
         *
         * @param int $day
         *
         * @return int
         */
        public static function daysAfter($day = 1): int
        {
            return time() + self::daysToSecond($day);
        }

        /**
         * 天数转换成秒数
         *
         * @param int $day
         *
         * @return int
         */
        public static function daysToSecond($day = 1): int
        {
            return $day * 86400;
        }

        /**
         * 周数转换成秒数
         *
         *
         * @param int $week
         *
         * @return float|int
         */
        public static function weekToSecond($week = 1)
        {
            return self::daysToSecond() * 7 * $week;
        }

        /**
         * 获取日期对应的星期
         * 参数$date为输入的日期数据，格式如：2018-6-22
         *
         * @param $date
         *
         * @return mixed
         */
        public static function weekForData($date)
        {
            //强制转换日期格式
            $date_str = date('Y-m-d', strtotime($date));
            //封装成数组
            $arr = explode('-', $date_str);
            //参数赋值
            //年
            $year = $arr[0];
            //月，输出2位整型，不够2位右对齐
            $month = sprintf('%02d', $arr[1]);
            //日，输出2位整型，不够2位右对齐
            $day = sprintf('%02d', $arr[2]);
            //时分秒默认赋值为0；
            $hour = $minute = $second = 0;
            //转换成时间戳
            $strap = mktime($hour, $minute, $second, $month, $day, $year);
            //获取数字型星期几
            $number_wk = date('w', $strap);
            //自定义星期数组
            $weekArr = array('0', '1', '2', '3', '4', '5', '6');

            //获取数字对应的星期
            return $weekArr[$number_wk];
        }

        /**
         * 获取一周日期
         *
         * @param        $time
         * @param string $format
         *
         * @return array
         */
        public static function getDateForWeek($time, $format = 'Y-m-d'): array
        {
            $week = date('w', $time);
            $weekname = array('星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日');
            //星期日排到末位
            if (empty($week)) {
                $week = 7;
            }
            $data = array();
            for ($i = 0; $i <= 6; $i++) {
                $data[$i]['date'] = date($format, strtotime('+' . $i + 1 - $week . ' days', $time));
                $data[$i]['week'] = $weekname[$i];
            }

            return $data;
        }

        /**
         * 时间戳转星期
         *
         * @param null $time
         *
         * @return mixed
         */
        public static function getWeekByDate($time = null)
        {
            if ($time == null || $time == '' || empty($time)) {
                $time = time();
            }
            $week = date('w', $time);
            //星期日排到末位
            if (empty($week)) {
                $week = 7;
            }
            $weekname = array('星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日');

            return $weekname[$week - 1];
        }

        /**
         * 获取毫秒级别的时间戳
         *
         * @return array|string
         */
        public static function getMillisecond()
        {
            //获取毫秒的时间戳
            $time = explode(' ', microtime());
            $time = $time[1] . ($time[0] * 1000);
            $time2 = explode('.', $time);
            $time = $time2[0];

            return $time;
        }

        /**
         * 根据输入的时间戳转换成时间字符串，
         * 最小处理时间为 毫秒MS
         * <p>
         * 此方法尚存在BUG,超过 24 小时输出错误，
         *
         * @param        $timestamp
         * @param string $accuracy
         *
         * @return string
         */
        public static function timer($timestamp, $accuracy = 's'): string
        {
            $timestamp = (int)$timestamp;

            if ($timestamp <= 1000 && $accuracy === 'ms') {
                $format = 'u';
            } elseif ($timestamp <= 60 && $accuracy === 's') {
                $format = 's';
            } elseif ($timestamp <= 60 * 60 && $accuracy === 's') {
                $format = 'i:s';
            } elseif ($timestamp <= 60 * 60 * 24 && $accuracy === 's') {
                $format = 'H:i:s';
            } elseif ($timestamp <= 60 * 60 * 24 * 30 && $accuracy === 's') {
                $format = 'd H:i:s';
            } elseif ($timestamp <= 60 * 60 * 24 * 30 * 12 && $accuracy === 's') {
                $format = 'm-d H:i:s';
            } else {
                $format = 'Y-m-d H:i:s';
            }

            return date($format, $timestamp) . '毫秒';
        }

        /**
         * 微信聊天消息时间显示说明
         * 1、当天的消息，以每5分钟为一个跨度的显示时间；
         * 2、消息超过1天、小于1周，显示星期+收发消息的时间；
         * 3、消息大于1周，显示手机收发时间的日期。
         *
         * 三、消息时间计算：
         * 1、刚刚、5分钟以内
         * 2、今天：20：24
         * 3、昨天：昨天14：10
         * 4、前天：前天10：24
         * 5、周一、周二｛超过3天周显示为本周｝
         * 6、3.8 10：24｛超过本周则带上日期｝
         * 7、2018 11.11 10：24 ｛超过本年则带上年度｝
         *
         * @param        $time
         * @param string $accuracy
         *
         * @return false|string
         */
        public static function getChatTime($time, $accuracy = 'm')
        {
            $now = time();
            if ($now > $time && ($now - $time) <= 300) {
                $result = '刚刚';
            } elseif ($time >= self::today() && $time <= self::today('end')) {
                if ($accuracy === 's') {
                    $result = date('H:i:s', $time);
                } elseif ($accuracy === 'm') {
                    $result = date('H:i', $time);
                } else {
                    $result = date('H:i', $time);
                }
            } elseif ($time >= self::yesterday() && $time <= self::yesterday('end')) {
                if ($accuracy === 's') {
                    $result = '昨天' . date('H:i:s', $time);
                } elseif ($accuracy === 'm') {
                    $result = '昨天' . date('H:i', $time);
                } else {
                    $result = '昨天' . date('H:i', $time);
                }
            } elseif ($time >= self::beforeday() && $time <= self::beforeday('end')) {
                if ($accuracy === 's') {
                    $result = '前天' . date('H:i:s', $time);
                } elseif ($accuracy === 'm') {
                    $result = '前天' . date('H:i', $time);
                } else {
                    $result = '前天' . date('H:i', $time);
                }
            } elseif ($time >= self::week() && $time <= self::week('end')) {
                if ($accuracy === 's') {
                    $result = self::weekForData($time) . date('H:i:s', $time);
                } elseif ($accuracy === 'm') {
                    $result = self::weekForData($time) . date('H:i', $time);
                } else {
                    $result = self::weekForData($time) . date('H:i', $time);
                }
            } else if ($accuracy === 's') {
                $result = date('Y-m-d H:i:s', $time);
            } elseif ($accuracy === 'm') {
                $result = date('Y-m-d H:i', $time);
            } else {
                $result = date('Y-m-d H:i', $time);
            }

            return $result;
        }

    }