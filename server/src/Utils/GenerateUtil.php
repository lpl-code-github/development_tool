<?php

namespace App\Utils;

class GenerateUtil
{
    /**
     * 字符串首字母转为小写
     * @param $str
     * @return mixed
     */
    public static function lowerFirst($str)
    {
        if (empty($str)) {
            return $str;
        }
        // 在设置方法变量名时，如果参数有_ 则需要去掉并把_后边的第一个字母大写
        $str = self::explodeSomeText($str);
        $str[0] = strtolower($str[0]);
        return $str;
    }

    /**
     * 字符串首字母转为大写
     * @param $str
     * @return mixed
     */
    public static function upperFirst($str)
    {
        if (empty($str)) {
            return $str;
        }
        // 在设置get set方法时，如果参数有_ 则需要去掉并把_后边的第一个字母大写
        $str = self::explodeSomeText($str);
        $str[0] = strtoupper($str[0]);
        return $str;
    }

    /**
     * 将一个单词转为复数
     * @param $word
     * @return string
     */
    public static function plural($word): string
    {
        $plural = $word;
        $lastChar = strtolower(substr($word, -1));

        switch ($lastChar) {
            case 'y':
                $plural = substr($word, 0, -1) . 'ies';
                break;
            case 's':
                $plural .= 'es';
                break;
            default:
                $plural .= 's';
        }

        return $plural;
    }

    /**
     * 去除Controller字符
     * @param $str
     * @return array|string|string[]|null
     */
    public static function removeController($str)
    {
        // 使用正则表达式匹配并替换掉末尾的 "Controller"
        return preg_replace('/Controller$/', '', $str);
    }

    /**
     * 去除下划线并将下划线后的字符转为大写
     * @param $str
     * @return void
     */
    public static function explodeSomeText($str): string
    {
        if (strpos($str, '_') !== false) {
            $parts = explode('_', $str);
            $parts = array_map(function ($part) {
                return ucfirst($part);
            }, $parts);
            $str = implode('', $parts);
        }
        return $str;
    }

    /**
     * 判断数组是关联数组还是普通数组
     * @param $array
     * @return bool 返回true代表是关联数组，否则是普通数组
     */
    public static function isArrayAssociative($array): bool
    {
        if (!is_array($array)) {
            return false;
        }

        $keys = array_keys($array);

        if (array_keys($keys) !== $keys) {
            return true;
        }

        for ($i = 0; $i < count($keys); $i++) {
            if ($i !== $keys[$i]) {
                return true;
            }
        }

        return false;
    }

    /**
     * 遍历$targets，查看一个字符串是否存在以某个元素为开头的子字符串，如果有去掉并返回
     * @param $string
     * @param $targets
     * @return false|mixed|string
     */
    public static function removePrefixFromArray($string, $targets) {
        foreach ($targets as $prefix) {
            if (strpos($string, $prefix) === 0) {
                return substr($string, strlen($prefix));
            }
        }
        return $string;
    }
}