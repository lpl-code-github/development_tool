<?php

namespace App\Service\Generator;

use App\Utils\GenerateUtil;
use ReflectionClass;
use ReflectionException;
use Doctrine\Inflector\InflectorFactory;

class GenerateServiceCodeService
{
    /**
     * 通过实体类生成Service类
     * @param string $entityClass
     * @return string
     * @throws ReflectionException
     */
    public function generateServiceCode(string $entityClass): string
    {
        $reflectionClass = new ReflectionClass($entityClass);
        $className = $reflectionClass->getShortName();
        $classNameLower = GenerateUtil::lowerFirst($className); // 小写
        $classNameComplex = GenerateUtil::plural($className);// 大写复数形式


        // 定义php头部
        $serviceCode = <<<EOF
<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\\${className};
use App\Dto\\${className}Dto;

class {$className}Service
{   
    private EntityManagerInterface \$entityManager;

    public function __construct(
        EntityManagerInterface \$entityManager
    )
    {
        \$this->entityManager = \$entityManager;
    }
EOF;
        $serviceCode .= "\n\n";

        // 生成Handle Get的方法
        $getterGetCode = $this->generateHandleGetCode($className);
        $serviceCode .= $getterGetCode;
        $serviceCode .= "\n\n";
        // 生成Handle Post的方法
        $getterPostCode = $this->generateHandlePostCode($className);
        $serviceCode .= $getterPostCode;
        $serviceCode .= "\n\n";
        // 生成Handle Put的方法
        $getterPutCode = $this->generateHandlePutCode($className);
        $serviceCode .= $getterPutCode;
        $serviceCode .= "\n\n";
        // 生成Handle Post的方法
        $getterDeleteCode = $this->generateHandleDeleteCode($className);
        $serviceCode .= $getterDeleteCode;
        $serviceCode .= "\n";


        $serviceCode .= "}";

        return $serviceCode;
    }


    /**
     *  生成Handle Get的方法
     *
     * @param string $className
     * @return string
     */
    private function generateHandleGetCode(string $className): string
    {
        $classNameLower = GenerateUtil::lowerFirst($className); // 小写
        $classNameUpperComplex = GenerateUtil::plural($className);// 大写复数形式
        $classNameComplex = GenerateUtil::plural($classNameLower);// 小写复数形式

        return <<<EOF
    /**
     * @param array \$params
     * @param array \$returnFields
     * @return array
     */
    public function handleGet$classNameUpperComplex(array \$params, array \$returnFields): array
    {
        \$result = array();

        // example
        if (array_key_exists("id", \$params)) {
            \$$classNameLower = \$this->entityManager->getRepository($className::class)->findOneById(\$params['id']);
            \${$classNameLower}Dto = new ${className}Dto(\$$classNameLower);
            \$result[] = \$${classNameLower}Dto->toArray(\$returnFields);
        }

        if (array_key_exists("ids", \$params)) {
            \$$classNameComplex = \$this->entityManager->getRepository($className::class)->findByIds(\$params['ids']);
            foreach(\$$classNameComplex as \$$classNameLower){
                \${$classNameLower}Dto = new ${className}Dto(\$$classNameLower);
                \$result[] = \$${classNameLower}Dto->toArray(\$returnFields);
            }
        }

        return \$result;
    }
EOF;
    }

    /**
     *  生成Handle Post的方法
     *
     * @param string $className
     * @return string
     */
    private function generateHandlePostCode(string $className): string
    {
        $classNameLower = GenerateUtil::lowerFirst($className); // 小写
        $classNameUpperComplex = GenerateUtil::plural($className);// 大写复数形式
        $classNameComplex = GenerateUtil::plural($classNameLower);// 小写复数形式

        return <<<EOF
    /**
     * @param $className \$$classNameLower
     * @param array \$returnFields
     * @return array
     */
    public function handlePost$classNameUpperComplex($className \$$classNameLower, array \$returnFields): array
    {
        \$this->entityManager->persist(\$$classNameLower);
        \$this->entityManager->flush();

        \$${classNameLower}Dto = new ${className}Dto(\$$classNameLower);
        \$result[] = \$${classNameLower}Dto->toArray(\$returnFields);
        return \$result;
    }
EOF;
    }

    /**
     *  生成Handle Put的方法
     *
     * @param string $className
     * @return string
     */
    private function generateHandlePutCode(string $className): string
    {
        $classNameLower = GenerateUtil::lowerFirst($className); // 小写
        $classNameUpperComplex = GenerateUtil::plural($className);// 大写复数形式
        $classNameComplex = GenerateUtil::plural($classNameLower);// 小写复数形式

        return <<<EOF
    /**
     * @param array \$params
     * @param array \$returnFields
     * @return array
     */
    public function handlePut$classNameUpperComplex(array \$params, array \$returnFields): array
    {
        \$$classNameLower = \$this->entityManager->getRepository($className::class)->findOneById(\$params['id']);

        // example
        //if (array_key_exists('name', \$params)) {
        //    \$test->setName(\$params["name"]);
        //}

        \$this->entityManager->persist(\$$classNameLower);
        \$this->entityManager->flush();


        \$${classNameLower}Dto = new ${className}Dto(\$$classNameLower);
        \$result[] = \$${classNameLower}Dto->toArray(\$returnFields);
        return \$result;
    }
EOF;
    }

    /**
     *  生成Handle DELETE的方法
     *
     * @param string $className
     * @return string
     */
    private function generateHandleDeleteCode(string $className): string
    {
        $classNameLower = GenerateUtil::lowerFirst($className); // 小写
        $classNameUpperComplex = GenerateUtil::plural($className);// 大写复数形式
        $classNameComplex = GenerateUtil::plural($classNameLower);// 小写复数形式

        return <<<EOF
    /**
     * @param array \$params
     * @param array \$returnFields
     * @return array
     */
    public function handleDelete$classNameUpperComplex(array \$params, array \$returnFields): array
    {
        \$result = [];

        // example
        if (array_key_exists('id', \$params)) {
            \$$classNameLower = \$this->entityManager->getRepository($className::class)->findOneById(\$params['id']);
            \$result[] = \$this->delete$className(\$$classNameLower, \$returnFields);
        }

        if (array_key_exists('ids', \$params)) {
            \$$classNameComplex = \$this->entityManager->getRepository($className::class)->findByIds(\$params['ids']);
            foreach (\$$classNameComplex as \$$classNameLower) {
                \$result[] = \$this->delete$className(\$$classNameLower, \$returnFields);
            }
        }

        return \$result;
    }

    /**
     * @param $className \$$classNameLower
     * @param array \$returnFields
     * @return array
     */
    private function delete$className($className \$$classNameLower, array \$returnFields): array
    {
        \$${classNameLower}->setActive(0);
        \$this->entityManager->persist(\$$classNameLower);
        \$this->entityManager->flush();

        \$${classNameLower}Dto = new ${className}Dto(\$$classNameLower);
        return \$${classNameLower}Dto->toArray(\$returnFields);
    }
EOF;
    }
}