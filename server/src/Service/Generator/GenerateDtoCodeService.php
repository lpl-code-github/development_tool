<?php

namespace App\Service\Generator;

use App\Utils\GenerateUtil;
use ReflectionClass;
use ReflectionException;

class GenerateDtoCodeService
{
    /**
     * 通过实体类生成Dto类
     *
     * @param string $entityClass
     * @return string
     * @throws ReflectionException
     */
    public function generateDtoCode(string $entityClass): string
    {
        $reflectionClass = new ReflectionClass($entityClass);
        $className = $reflectionClass->getShortName();
        $properties = $this->getEntityProperties($reflectionClass);

        // 定义php头部
        $dtoCode = <<<EOF
<?php
        
namespace App\Dto;

use App\Entity\\${className};

class ${className}Dto
{

EOF;
        // 生成属性
        foreach ($properties as $property) {
            $propertyCode = $this->generatePropertyCode($property);
            $dtoCode .= $propertyCode;
        }
        // 生成构造器
        $constructorCode = $this->generateConstructorCode($className, $properties);
        $dtoCode .= $constructorCode;
        // 生成get set方法
        $getterSetterCode = $this->generateGetterSetterCode($properties);
        $dtoCode .= $getterSetterCode;
        $dtoCode .= "\n";
        // 生成toArray方法
        $toArrayCode = $this->generateToArrayCode($properties);
        $dtoCode .= $toArrayCode;


        $dtoCode .= "}";

        return $dtoCode;
    }

    /**
     * 获取实体类中所有的似有属性
     *
     * @param \ReflectionClass $reflectionClass
     * @return array
     */
    private function getEntityProperties(\ReflectionClass $reflectionClass): array
    {
        return $reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE);
    }

    /**
     * 生成属性到dto类
     *
     * @param \ReflectionProperty $property
     * @return string
     */
    private function generatePropertyCode(\ReflectionProperty $property): string
    {
        $propertyName = $property->getName();
        return "    private $" . $propertyName . ";\n\n";
    }

    /**
     * 生成构造器到dto类
     *
     * @param string $className
     * @param array $properties
     * @return string
     */
    private function generateConstructorCode(string $className, array $properties): string
    {
        $lowerFirstClassName = GenerateUtil::lowerFirst($className);

        $constructorCode = "    public function __construct({$className} \$$lowerFirstClassName)\n";
        $constructorCode .= "    {\n";
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $upperFirstPropertyName = GenerateUtil::upperFirst($propertyName);
            $constructorCode .= "        \$this->$propertyName = \${$lowerFirstClassName}->get{$upperFirstPropertyName}();\n";
        }
        $constructorCode .= "    }\n\n";
        return $constructorCode;
    }

    /**
     * 生成get set方法到dto类
     *
     * @param array $properties
     * @return string
     */
    private function generateGetterSetterCode(array $properties): string
    {
        $getterSetterCode = "";
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $upperFirstPropertyName = GenerateUtil::upperFirst($propertyName);
            $getterCode = <<<EOF
    public function get$upperFirstPropertyName()
    {
        return \$this->$propertyName;
    }

    public function set$upperFirstPropertyName(\$$propertyName)
    {
        \$this->$propertyName = \$$propertyName;
    }

EOF;
            $getterSetterCode .= $getterCode;
        }
        return $getterSetterCode;
    }

    /**
     * 生成 toArray() 方法到dto类
     *
     * @param array $properties
     * @return string
     */
    private function generateToArrayCode(array $properties): string
    {
        $toArrayCode = "    public function toArray(array \$fields = []): array\n";
        $toArrayCode .= "    {\n";
        $toArrayCode .= "        \$resultArray = [];\n";
        $toArrayCode .= "        \$resultArray[\"id\"] = \$this->getId();\n";
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if ($propertyName == "active"){
                continue;
            }
            if ($propertyName == "id"){
                continue;
            }
            $upperFirstPropertyName = GenerateUtil::upperFirst($propertyName);
            $toArrayCode .= "        if (in_array(\"$propertyName\", \$fields)) {\n";
            $toArrayCode .= "            \$resultArray[\"$propertyName\"] = \$this->get$upperFirstPropertyName();\n";
            $toArrayCode .= "        }\n";
        }
        $toArrayCode .= "        return \$resultArray;\n";
        $toArrayCode .= "    }\n";
        return $toArrayCode;
    }
}
