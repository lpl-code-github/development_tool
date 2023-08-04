<?php

namespace App\Service\Generator;

use App\Utils\GenerateUtil;
use ReflectionClass;

class GenerateFactoryCodeService
{
    /**
     * 生成Factory代码
     *
     * @param string $entityClass
     * @return string
     * @throws \ReflectionException
     */
    public function generateFactoryCode(string $entityClass): string
    {
        $reflectionClass = new ReflectionClass($entityClass);
        $className = $reflectionClass->getShortName();
        $properties = $this->getEntityProperties($reflectionClass);

        $factoryCode = <<<EOF
<?php

namespace App\Factory;

use App\Entity\\${className};

class ${className}Factory
{		
    /**
     * Create an instance
     */

EOF;
        $generateFunctionCode = $this->generateFunctionCode($properties, $className);
        $factoryCode .= $generateFunctionCode;

        $factoryCode .= "}"; //类结尾

        return $factoryCode;
    }

    /**
     * 获取实体类属性
     *
     * @param \ReflectionClass $reflectionClass
     * @return array
     */
    private function getEntityProperties(\ReflectionClass $reflectionClass): array
    {
        return $reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE);
    }

    /**
     * 生成方法名及参数，以及内部的set
     * @param array $properties
     * @param string $className
     * @return string
     */
    private function generateFunctionCode(array $properties,string $className): string
    {
        $paramsArray = array();
        foreach ($properties as $property) {
            $paramsArray[] = "$".GenerateUtil::lowerFirst($property->getName());
        }
        $paramsString = implode(', ', $paramsArray);

        // 创建方法名及参数
        $functionCode  = "    public function create(" . $paramsString . "): ?" . $className . "\n";
        $functionCode .= "    {\n";
        $functionCode .= "        // Perform some verification\n";
        $functionCode .= "        // ...\n\n";

        // new Object
        $lowerFirstClassName = GenerateUtil::lowerFirst($className);
        $functionCode .= "        \$$lowerFirstClassName = new $className();\n";

        // 生成set值的代码
        $setValueCode = $this->generateSetValueCode($properties, $className);
        $functionCode .= $setValueCode;

        // 生成retrun的代码
        $functionCode .= "        return \$$lowerFirstClassName;\n";

        // 方法结尾
        $functionCode .= "    }\n";
        return $functionCode;
    }


    /**
     * 生成set值的代码
     *
     * @param array $properties
     * @param string $className
     * @return string
     */
    private function generateSetValueCode(array $properties,string $className): string
    {
        $setValueCode = "";

        $lowerFirstClassName = GenerateUtil::lowerFirst($className);
        foreach ($properties as $property) {
            $propertyName = $property->getName();

            $upperFirstPropertyName = GenerateUtil::upperFirst($propertyName);
            $lowerFirstPropertyName = GenerateUtil::lowerFirst($property->getName());
            if ($propertyName == "id"){
                continue;
            }elseif ($propertyName == "active"){
                $setCode = "        $".$lowerFirstClassName."->set".$upperFirstPropertyName."(1);\n";
            }else{
                $setCode = "        $".$lowerFirstClassName."->set".$upperFirstPropertyName."($".$lowerFirstPropertyName.");\n";
            }

            $setValueCode .= $setCode;
        }

        return $setValueCode;
    }
}
