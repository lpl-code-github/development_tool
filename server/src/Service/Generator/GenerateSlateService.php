<?php

namespace App\Service\Generator;

use App\Utils\GenerateUtil;
use ReflectionClass;
use ReflectionException;
use Doctrine\Inflector\InflectorFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GenerateSlateService
{
    private ParameterBagInterface $parameterBag;
    private $inflector;

    /**
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }


    /**
     * 获取默认的slate文档
     */
    public function generateDefaultSlate(): string
    {
        $sourcePath = BASE_PATH . $this->parameterBag->get('default_slate_doc_path');
        return file_get_contents($sourcePath);
    }

    public function generateSlateByController(string $controller, array $data): string
    {
        $objectName = GenerateUtil::removeController($controller);
        $inflector = InflectorFactory::create()->build();
        $objectName = $inflector->singularize($objectName);// 转为单数
        $slateDoc = <<<EOF
# $objectName

EOF;
        foreach ($data as $item) {
            $name = $item['name'];
            $path = $item['path'];
            $method = $item['method'];

                $generateFunctionCode = $this->generateSimpleDoc($objectName, $name, $path, $method);

            $slateDoc .= $generateFunctionCode;
        }


        return $slateDoc;
    }

    /**
     * 创建单个请求的文档
     * @param $objectName
     * @param $name
     * @param $path
     * @param $method
     * @return string
     */
    private function generateSimpleDoc($objectName, $name, $path, $method): string
    {
        switch ($method){
            case "GET":
                $operation = "Get";
                break;
            case "POST":
                $operation = "Create";
                break;
            case "PUT":
                $operation = "Edit";
                break;
            case "DELETE":
                $operation = "Delete";
                break;
            case "PATCH":
                $operation = "Partial updates";
                break;
            default:
                $operation = $method;
                break;
        }
        return <<<EOF
## $operation $objectName

This endpoint can $name

### HTTP Request

`$method http://{baseUrl}$path`

> Request params :

```json-doc
{
    "data": {
       
    },
    "token": "{{admin_token}}"
}
```

```json
{
    "data": {
       
    },
    "token": "{{admin_token}}"
}
```

> Return object:

```json-doc
{
    "data": {
    
    },
    "token_user": {}
}
```

```json
{
    "data": {
      
    },
    "token_user": {}
}
```

### Query Parameters

| Key  | Type | Value | Description |
| ---- | ---- | ----- | ----------- |
|      |      |       |             |

### Return Data

| Key  | Type | Value | Description |
| ---- | ---- | ----- | ----------- |
|      |      |       |             |

## 

EOF;
    }
}