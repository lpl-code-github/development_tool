<?php

namespace App\Service\Generator;

use App\Utils\GenerateUtil;
class GenerateApiTsServiceCodeService
{

    public function generateTsServiceCodeByController(string $controller, array $data): string
    {
        $objectName = GenerateUtil::removeController($controller);

        $objectNameLowerFirst = GenerateUtil::lowerFirst($objectName); // 转为小写

        $tsServiceCode = <<<EOF
/*
  api-$objectNameLowerFirst.service.ts
 */
import { Injectable } from '@angular/core';
import { ApiConnectionService } from '../../../api-connection.service';
import { Observable } from 'rxjs';
import { } from './api-$objectNameLowerFirst.model';

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

            $generateFunctionCode = $this->generateFunction($objectName, $name, $path, $method, $function);

            $tsServiceCode .= $generateFunctionCode."\n\n";
        }

        return $tsServiceCode . "}\n\n";
    }

    /**
     * 创建单个请求的function
     * @param $objectName
     * @param $name
     * @param $path
     * @param $method
     * @param $function
     * @return string
     */
    private function generateFunction($objectName, $name, $path, $method, $function): string
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
        return <<<EOF
  /**
   * $name
   */  
  public $function(data: any): Observable<any>{
    const path = '$path';
    return this.apiConnectionService.$operation(path, data);
  }
EOF;
    }
}