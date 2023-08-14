<?php

namespace App\Service\Generator;

use App\Utils\GenerateUtil;

class GenerateApiTsInterfaceService
{
    private array $result;

    public function __construct(
        array $result
    )
    {
        $this->result = $result;
    }

    /**
     * 生成前台需要的ApiResponse的Interface
     * @param $data
     * @param string $interfaceName
     * @return string
     */
    function generateApiTsInterface($data, string $interfaceName = 'Example'): string
    {
        $this->generateTsCode($data, $interfaceName.'Response');
        $codes = array_reverse($this->result);  // 反转

        $result = "";
        foreach ($codes as $code) {
            $result .= $code . "\n";
        }
        return $result;
    }

    /**
     * 向类变量添加生成的代码
     * @param $data
     * @param string $interfaceName
     * @return void
     */
    public function generateTsCode($data, string $interfaceName = '')
    {
        $interface = "export interface " . $interfaceName . " {\n";

        foreach ($data as $key => $value) {
            $typeScriptType = $this->getTypeScriptType($key, $value, $interfaceName);
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
     * @return string
     */
    function getTypeScriptType($key, $value, $interfaceName): string
    {
        if (is_array($value)) {
            if (GenerateUtil::isArrayAssociative($value)) { // 对象类型
                // 给对象生成新的Interface
                $newInterfaceName = $interfaceName ? $interfaceName . GenerateUtil::upperFirst(GenerateUtil::explodeSomeText($key)) : GenerateUtil::upperFirst(GenerateUtil::explodeSomeText($key));
                $this->generateTsCode($value, $newInterfaceName);

                // 返回这个key的类型：新的Interface名
                return ': ' . $newInterfaceName;
            } else { // 数组类型
                // 1. 如果数组为空，则返回任意类型的数组
                if (count($value) == 0) {
                    return '?: any[]';
                }
                // 2. 数组不为空 遍历数组中的所有元素 设置类型
                foreach ($value as $v) {
                    // 如果子元素不是数组
                    if (!is_array($v)) {
                        $type = $this->getTypeScriptType($key, $v, $interfaceName);
                        return ':' . $type . '[]'; // 默认拿到第一个就返回
                    }

                    if (!GenerateUtil::isArrayAssociative($v)) { // 如果子元素是数组(数组套数组的情况)
                        return '?: any[]';
                    } else { // 如果子元素是对象
                        $newInterfaceName = $interfaceName ? $interfaceName . GenerateUtil::upperFirst(GenerateUtil::explodeSomeText($key)) : GenerateUtil::upperFirst(GenerateUtil::explodeSomeText($key));
                        $this->generateTsCode($v, $newInterfaceName) . "\n";
                        // 返回这个key的类型为为新的Interface名
                        return ': ' . $newInterfaceName . '[]'; // 默认拿到第一个就返回
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

