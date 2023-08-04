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
     * 去除下划线并将下划线后的字符转为大写
     * @param $str
     * @return void
     */
    private static function explodeSomeText($str): string
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
}