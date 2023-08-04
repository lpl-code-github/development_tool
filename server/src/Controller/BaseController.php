<?php

namespace App\Controller;

use App\Factory\ExceptionFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    const ARRAY_TYPE = "array";
    const OBJECT_TYPE = "object";
    const BOOL_TYPE = "bool";
    const INT_TYPE = "int";
    const FLOAT_TYPE = "float";
    const STRING_TYPE = "string";
    const NULL_TYPE = "null";

    /**
     * @throws \Exception
     */
    protected function validateNecessaryParameters(array $params, array $options)
    {
        foreach ($options as $key => $type) {
            if (!array_key_exists($key, $params)) {
                throw ExceptionFactory::WrongFormatException("The required parameters " . $key . " is missing");
            }
            $this->validateParameterType($key, $params[$key], $type);
        }
    }

    /**
     * @throws \Exception
     */
    private function validateParameterType($key, $value, $type)
    {
        switch ($type) {
            case 'string':
                if (!is_string($value)) {
                    throw ExceptionFactory::WrongFormatException("Parameter " . $key . " should be of string type");
                }
                break;
            case 'int':
                if (!is_integer($value) && !is_integer(filter_var($value, FILTER_VALIDATE_INT)))  {
                    throw ExceptionFactory::WrongFormatException("Parameter " . $key . " should be of int type");
                }
                break;
            case 'float':
                if (!is_numeric($value)) {
                    throw ExceptionFactory::WrongFormatException("Parameter " . $key . " should be of float type");
                }
                break;
            case 'bool':
                if (!is_bool($value)) {
                    throw ExceptionFactory::WrongFormatException("Parameter " . $key . " should be of boolean type");
                }
                break;
            case 'array':
                if (!is_array($value) || $this->isArrayAssociative($value)) {
                    throw ExceptionFactory::WrongFormatException("Parameter " . $key . " should be of array type");
                }
                break;
            case 'object':
                if (!is_array($value) || !$this->isArrayAssociative($value)) {
                    throw ExceptionFactory::WrongFormatException("Parameter " . $key . " should be of object type");
                }
                break;
            case 'null':
                if (!is_null($value)) {
                    throw ExceptionFactory::WrongFormatException("Parameter " . $key . " should is null");
                }
                break;
            default:
                break;
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
}