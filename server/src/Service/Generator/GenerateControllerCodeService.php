<?php

namespace App\Service\Generator;

use App\Utils\GenerateUtil;
use ReflectionClass;
use ReflectionException;
use Doctrine\Inflector\InflectorFactory;

class GenerateControllerCodeService
{
    /**
     * 通过实体类生成Controller代码
     * @param string $entityClass
     * @return string
     * @throws ReflectionException
     */
    public function generateControllerCode(string $entityClass): string
    {
        $reflectionClass = new ReflectionClass($entityClass);
        $className = $reflectionClass->getShortName();
        $classNameLower = GenerateUtil::lowerFirst($className); // 小写
        $classNameComplex = GenerateUtil::plural($className);// 大写复数形式


        // 定义php头部
        $controllerCode = <<<EOF
<?php

namespace App\Controller\Resource;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\\${className}Service;
use App\Factory\\${className}Factory;

class {$classNameComplex}Controller extends BaseController
{
    // Define default return fields
    const RETURN_FIELD = array(
        
    );
    
    private ${className}Service $${classNameLower}Service;
    private ${className}Factory $${classNameLower}Factory;

    public function __construct(
        ${className}Service $${classNameLower}Service,
        ${className}Factory $${classNameLower}Factory
    )
    {
        \$this->${classNameLower}Service = $${classNameLower}Service;
        \$this->${classNameLower}Factory = $${classNameLower}Factory;
    }
EOF;
        $controllerCode .= "\n\n";

        // 生成get请求的方法
        $getterGetCode = $this->generateGetCode($className);
        $controllerCode .= $getterGetCode;
        $controllerCode .= "\n\n";
        // 生成get请求的方法
        $getterPostCode = $this->generatePostCode($className);
        $controllerCode .= $getterPostCode;
        $controllerCode .= "\n\n";
        // 生成get请求的方法
        $getterPutCode = $this->generatePutCode($className);
        $controllerCode .= $getterPutCode;
        $controllerCode .= "\n\n";
        // 生成get请求的方法
        $getterDeleteCode = $this->generateDeleteCode($className);
        $controllerCode .= $getterDeleteCode;
        $controllerCode .= "\n";

        $controllerCode .= "}";

        return $controllerCode;
    }


    /**
     * 生成get请求的方法
     *
     * @param string $className
     * @return string
     */
    private function generateGetCode(string $className): string
    {
        $classNameLower = GenerateUtil::lowerFirst($className); // 小写
        $classNameUpperComplex = GenerateUtil::plural($className);// 大写复数形式
        $classNameComplex = GenerateUtil::plural($classNameLower);// 小写复数形式

        return <<<EOF
    /**
     * @Route("/$classNameComplex", name="get $classNameComplex", methods={"GET"})
     * @throws \Exception
     */
    public function executeGet(Request \$request): Response
    {
        \$response = new Response();

        \$returnFields = \$request->query->get("return_fields") ?? [];
        \$id = \$request->query->get("id") ?? null;
        \$ids = \$request->query->get("id") ?? null;

        // validate params
        // ...

        \$queryParams = array();
        if (\$id) {
            \$queryParams['id'] = \$id;
        }
        if (\$ids) {
            \$queryParams['ids'] = \$ids;
        }

        // processing
        \$resultArray['data'] = \$this->${classNameLower}Service->handleGet${classNameUpperComplex}(\$queryParams, \$returnFields);

        \$response->setContent(json_encode(\$resultArray));
        return \$response;
    }
EOF;
    }

    private function generatePostCode(string $className): string
    {
        $classNameLower = GenerateUtil::lowerFirst($className); // 小写
        $classNameUpperComplex = GenerateUtil::plural($className);// 大写复数形式
        $classNameComplex = GenerateUtil::plural($classNameLower);// 小写复数形式

        return <<<EOF
    /**
     * @Route("/$classNameComplex", name="save $classNameComplex", methods={"POST"})
     * @throws \Exception
     */
    public function executePost(Request \$request): Response
    {
        \$response = new Response();
        \$resultArray = array();

        \$params = json_decode(\$request->getContent(), true);
        \$this->validateNecessaryParameters(\$params, ['data' => self::OBJECT_TYPE]);
        \$data = \$params['data'];

        // validate params
        // example ...
        //\$this->validateNecessaryParameters(\$data, [
        //    'name' => self::STRING_TYPE
        //]);

        // processing
        \$${classNameLower} = \$this->${classNameLower}Factory->create(
        //    \$data['name'],
        //    \$data['type'],
        );
        // processing
        \$resultArray['data'] = \$this->${classNameLower}Service->handlePost${classNameUpperComplex}(\$${classNameLower}, self::RETURN_FIELD);

        \$response->setContent(json_encode(\$resultArray));
        return \$response;
    }
EOF;
    }

    private function generatePutCode(string $className): string
    {
        $classNameLower = GenerateUtil::lowerFirst($className); // 小写
        $classNameUpperComplex = GenerateUtil::plural($className);// 大写复数形式
        $classNameComplex = GenerateUtil::plural($classNameLower);// 小写复数形式

        return <<<EOF
    /**
     * @Route("/$classNameComplex", name="update $classNameComplex", methods={"PUT"})
     * @throws \Exception
     */
    public function executePut(Request \$request): Response
    {
        \$response = new Response();

        \$params = json_decode(\$request->getContent(), true);
        \$this->validateNecessaryParameters(\$params, ['data' => self::OBJECT_TYPE]);
        \$data = \$params['data'];

        // validate params
        // ...

        // processing
        \$resultArray['data'] = \$this->${classNameLower}Service->handlePut${classNameUpperComplex}(\$data,  self::RETURN_FIELD);

        \$response->setContent(json_encode(\$resultArray));
        return \$response;
    }
EOF;
    }

    private function generateDeleteCode(string $className): string
    {
        $classNameLower = GenerateUtil::lowerFirst($className); // 小写
        $classNameUpperComplex = GenerateUtil::plural($className);// 大写复数形式
        $classNameComplex = GenerateUtil::plural($classNameLower);// 小写复数形式

        return <<<EOF
    /**
     * @Route("/$classNameComplex", name="remove $classNameComplex", methods={"DELETE"})
     * @throws \Exception
     */
    public function executeDelete(Request \$request): Response
    {
        \$response = new Response();

        \$params = json_decode(\$request->getContent(), true);
        \$this->validateNecessaryParameters(\$params, ['data' => self::OBJECT_TYPE]);
        \$data = \$params['data'];

        // validate params
        // ...

        // processing
        \$resultArray['data'] = \$this->${classNameLower}Service->handleDelete${classNameUpperComplex}(\$data, self::RETURN_FIELD);

        \$response->setContent(json_encode(\$resultArray));
        return \$response;
    }
EOF;
    }
}