<?php

namespace App\Service\Generator;

use App\Utils\GenerateUtil;

class GeneratePostmanTestService
{
    /**
     * 生成postman的测试 js代码
     * @param $data
     * @return string
     */
    function generatorPostmanTest($data): string
    {
        // 调用 generateSchema() 函数，生成 JSON Schema
        $schema =  $this->generateSchema($data, null);

        // 将 JSON Schema 对象格式化成 JSON 数据，并进行缩进
        $schemaFormatted = json_encode($schema, JSON_PRETTY_PRINT);
        return $this->getJsonSchemaTest($schemaFormatted);
    }

    /**
     * 生成postman jsonSchema
     *
     * @param $data
     * @param $parentFiled
     * @return array|array[]|string[]
     */
    function generateSchema($data, $parentFiled): array
    {
        if (is_array($data)){
            if (GenerateUtil::isArrayAssociative($data)){
                $properties = array();
                $keys = array();
                foreach ($data as $k => $v) {
                    $keys[] = $k;
                    $properties[$k] = $this->generateSchema($v, $data);
                }

                // 返回包含属性集合和所属类型、必需字段的数组
                return [
                    'properties' => $properties,
                    'type' => 'object',
                    'required' => $keys,
                ];
            }else{
                $arr = $data;
                if (count($arr) == 0) { // 如果数组为空，则返回类型为数组的对象
                    return [
                        "type" => "array"
                    ];
                }

                if (is_array($parentFiled)&& !GenerateUtil::isArrayAssociative($parentFiled)) { // 判断父级对象的类型是否为数组
                    return []; // 如果父级对象是数组类型，则返回一个空的 JSON 对象
                } else { // 否则返回包含数组项类型和所属数组类型的对象
                    return [
                        "items" => $this->generateSchema($arr[0], $data),
                        "type" => "array"
                    ];
                }
            }
        }elseif (is_float($data)){
            return ['type' => 'integer'];
        }elseif (is_bool($data)){
            return ['type' => 'boolean'];
        }elseif (is_integer($data) || is_int($data)){
            return ['type' => 'integer'];
        } elseif (is_string($data)){
            $s = strval($data);
            if ($this->isStringInt($s)) {
                return ['type' => ['string', 'integer']];
            } else {
                return ['type' => 'string'];
            }
        }else{
            return ['type' => 'null'];
        }
    }

    /**
     * 判断数组是关联数组还是普通数组
     * @param $array
     * @return bool 返回true代表是关联数组，否则是普通数组
     */
    function isArrayAssociative($array): bool
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
     * 判断字符串是否可以成功解析为整型
     *
     * @param $s
     * @return bool
     */
    private function isStringInt($s): bool
    {
        return is_numeric($s) && strpos($s, '.') === false;
    }

    /*
     * 返回JsonSchemaTest测试模板
     */
    private function getJsonSchemaTest($schemaJson): string
    {
        // 写入 JavaScript 代码模板和 JSON Schema
        $jsTemplate = '// Get the Response
let response = pm.response.json();

pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});
        
const schema = %s;

pm.test("Schema is valid", function() {
   pm.expect(tv4.validate(response, schema)).to.be.true;
});';

        // 把json格式的schema写入js模版
        return sprintf($jsTemplate, $schemaJson);
    }
}