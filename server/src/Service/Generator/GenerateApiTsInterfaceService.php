<?php

namespace App\Service\Generator;

use App\Utils\GenerateUtil;

class GenerateApiTsInterfaceService
{
    public array $result;

    public function __construct(
        array $result
    )
    {
        $this->result = $result;
    }

    function generateTokenUserImport(): string
    {
        return "import { TokenUserResponse } from '../../../api-token-user.model';\n\n";
    }

    public function generateTsNote(string $interfaceName = 'Example', $prefix="", $suffix='Response', $isSimple = true): string
    {
        if ($isSimple){
            $interfaceNote = "// $prefix$interfaceName$suffix";
        }else{
            $interfaceNote = <<<EOF
/**
 * $prefix$interfaceName$suffix
 */
EOF;
        }

        return  $interfaceNote."\n";
    }

    /**
     * @param int $count
     * @param string $interfaceName
     * @param string $suffix
     * @return string
     */
    function generateType(int $count = 0, string $interfaceName = 'Example', $suffix='Response'): string
    {

        $result = "export type $interfaceName$suffix = ";
        if ($count == 0){
            return $result. "any\n\n";
        }

        $extends = array();
        for ($i = 1; $i <= $count; $i++) {
            $extends[] = "$interfaceName$suffix".$i;
        }
        $result .= implode(' | ', $extends);
        $result .= "\n\n";

        return $result;

    }

    /**
     * 生成前台需要的ApiResponse的Interface
     * @param $data
     * @param string $interfaceName
     * @param string $suffix
     * @param bool $isImport
     * @param int $allowDepth // 深度
     * @return string
     */
    public function generateSimpleApiTsInterface($data, string $interfaceName = 'Example', string $suffix='Response', bool $isImport = true , int $allowDepth = 3): string
    {

        $this->generateTsCode($data, $interfaceName.$suffix, 0, $allowDepth); // 第三个参数代表嵌套层数
        $codes = array_reverse($this->result);  // 反转

        $result = $isImport ? $this->generateTokenUserImport() : "";
        foreach ($codes as $code) {
            $result .= $code . "\n";
        }
        return $result;
    }

    /**
     * 向类变量添加生成的代码
     * @param $data
     * @param string $interfaceName
     * @param int $depth 当前深度
     * @param int $allowDepth 允许的深度
     * @return void
     */
    public function generateTsCode($data, string $interfaceName = '', int $depth = 0, int $allowDepth = 3)
    {
        $interface = "export interface " . GenerateUtil::upperFirst($interfaceName) . " {\n";

        foreach ($data as $key => $value) {
            if ($key == "token_user"){
                $typeScriptType = ': TokenUserResponse';
            }else{
                $typeScriptType = $this->getTypeScriptType($key, $value, $interfaceName, $depth, $allowDepth);
            }
            $interface .= "  $key" . $typeScriptType . ",\n";
        }

        // 去除最后一个元素的空格
        $interface = rtrim($interface, ",\n") . "\n";
        $interface .= "}\n";

        $this->result[] = $interface;
    }

    /**
     * 获取元素的类型
     *
     * @param $key
     * @param $value
     * @param $interfaceName
     * @param int $depth // 当前深度
     * @param int $allowDepth//允许的深度
     * @return string
     */
    function getTypeScriptType($key, $value, $interfaceName, int $depth ,int $allowDepth = 3): string
    {
        if (is_array($value)) {
            if (GenerateUtil::isArrayAssociative($value)) { // 对象类型
                if ($depth >= $allowDepth) {
                    return ': any';
                }
                // 给对象生成新的Interface
                $newInterfaceName = $interfaceName ? $interfaceName . GenerateUtil::upperFirst(GenerateUtil::explodeSomeText($key)) : GenerateUtil::upperFirst(GenerateUtil::explodeSomeText($key));
                $this->generateTsCode($value, $newInterfaceName, $depth + 1,$allowDepth);
                // 返回这个key的类型：新的Interface名
                return ': ' . GenerateUtil::upperFirst($newInterfaceName);
            } else { // 数组类型
                // 1. 如果数组为空，则返回任意类型的数组
                if (count($value) == 0) {
                    return '?: any[]';
                }
                // 2. 数组不为空 遍历数组中的所有元素 设置类型
                foreach ($value as $v) {
                    // 如果子元素不是数组
                    if (!is_array($v)) {
                        $type = $this->getTypeScriptType($key, $v, $interfaceName, $depth, $allowDepth);
                        return  $type . '[]'; // 默认拿到第一个就返回
                    }

                    if (!GenerateUtil::isArrayAssociative($v)) { // 如果子元素是数组(数组套数组的情况)
                        return '?: any[]';
                    } else { // 如果子元素是对象
                        if (array_key_exists("-1", $v)){ // -1的情况
                            return ': any[]'; // 默认拿到第一个就返回
                        }
                        if ($depth >= $allowDepth) {
                            return ': any[]';
                        }
                        $newInterfaceName = $interfaceName ? $interfaceName . GenerateUtil::upperFirst(GenerateUtil::explodeSomeText($key)) : GenerateUtil::upperFirst(GenerateUtil::explodeSomeText($key));
                        $this->generateTsCode($v, $newInterfaceName, $depth + 1, $allowDepth) . "\n";
                        // 返回这个key的类型为为新的Interface名
                        return ': ' . GenerateUtil::upperFirst($newInterfaceName) . '[]'; // 默认拿到第一个就返回
                    }
                }
                return ': []';
            }
        } elseif (is_bool($value) || strtolower($value) === "true" || strtolower($value) === "false") {
            if (is_bool($value)) {
                return ': boolean';
            } else { // string类型的bool
                return '?: boolean | string';
            }
        } elseif (is_integer($value) || is_int($value) || is_float($value)) {
            return ': number';
        } elseif (is_string($value)) {
            $s = strval($value);
            if ($this->isStringInt($s)) {
                return '?: number | string';
            } else {
                return ': string';
            }
        } else {
            return '?: null';
        }
    }

    /**
     * 判断字符串是否可以成功解析为整型
     *
     * @param $s
     * @return bool
     */
    private function isStringInt($s): bool
    {
        return is_numeric($s) && strpos($s, '.') === false;
    }
}
