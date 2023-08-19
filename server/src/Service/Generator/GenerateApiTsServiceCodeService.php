<?php

namespace App\Service\Generator;

use App\Utils\GenerateUtil;
class GenerateApiTsServiceCodeService
{

    /**
     * 生成Ts的api Service
     * @param string $controller
     * @param array $data
     * @param bool $isType
     * @param string $requestModalSuffix
     * @param string $responseModalSuffix
     * @return string
     */
    public function generateTsServiceCodeByController(string $controller, array $data, $isType = false, string $requestModalSuffix = "", string $responseModalSuffix = ""): string
    {
        // 如果要生成type  开头import需要引入
        $requestTypes =array();
        $responseTypes =array();
        if ($isType){
            foreach ($data as $item) {
                $function = GenerateUtil::upperFirst(
                    GenerateUtil::removePrefixFromArray(
                        $item['function'],
                        array("handle","execute")
                    )
                );

                $requestTypes[] = $function.$requestModalSuffix;
                $responseTypes[] = $function.$responseModalSuffix;
            }
        }
        $requestType = count($requestTypes)!=0 ? implode(', ', $requestTypes) : "";
        $responseType = count($responseTypes)!=0 ? implode(', ', $responseTypes) : "";

        $objectName = GenerateUtil::removeController($controller);
        $objectNameLowerFirst = GenerateUtil::lowerFirst($objectName); // 转为小写

        $tsServiceCode = <<<EOF
/*
  api-$objectNameLowerFirst.service.ts
 */
import { Injectable } from '@angular/core';
import { ApiConnectionService } from '../../../api-connection.service';
import { Observable } from 'rxjs';
import { $requestType } from './api-$objectNameLowerFirst.request.model';
import { $responseType } from './api-$objectNameLowerFirst.response.model';

@Injectable({
  providedIn: 'root'
})
export class Api{$objectName}Service {
  constructor(private apiConnectionService: ApiConnectionService) { }


EOF;
        foreach ($data as $item) {
            $name = $item['name'];
            $path = $item['path'];
            $method = $item['method'];
            $function = GenerateUtil::lowerFirst(
                GenerateUtil::removePrefixFromArray(
                    $item['function'],
                    array("handle","execute")
                )
            );

            $generateFunctionCode = $this->generateFunction($name, $path, $method, $function, $isType, $requestModalSuffix,$responseModalSuffix);

            $tsServiceCode .= $generateFunctionCode."\n\n";
        }

        return $tsServiceCode . "}\n\n";
    }

    /**
     * 创建单个请求的function
     * @param $name
     * @param $path
     * @param $method
     * @param $function
     * @param string $requestModalSuffix
     * @param string $responseModalSuffix
     * @return string
     */
    private function generateFunction($name, $path, $method, $function, $isType = false, string $requestModalSuffix = "", string $responseModalSuffix = ""): string
    {
        switch ($method){
            case "GET":
                $operation = "get";
                break;
            case "POST":
                $operation = "post";
                break;
            case "PUT":
                $operation = "put";
                break;
            case "DELETE":
                $operation = "delete";
                break;
            case "PATCH":
                $operation = "patch";
                break;
            default:
                $operation = $method;
                break;
        }

        $requestType = $isType ? GenerateUtil::upperFirst($function).$requestModalSuffix : 'any'; // 设置参数类型
        $responseType = $isType ? GenerateUtil::upperFirst($function).$responseModalSuffix : 'any'; // 设置返回值类型

        return <<<EOF
  /**
   * $name
   */  
  public $function(data: $requestType): Observable<$responseType>{
    const path = '$path';
    return this.apiConnectionService.$operation(path, data);
  }
EOF;
    }
}