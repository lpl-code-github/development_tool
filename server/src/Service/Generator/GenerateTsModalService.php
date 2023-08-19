<?php

namespace App\Service\Generator;



use App\Entity\RecordApiInfo;
use App\Factory\ExceptionFactory;
use App\Utils\GenerateUtil;
use Doctrine\ORM\EntityManagerInterface;

class GenerateTsModalService
{

    private EntityManagerInterface $entityManager;

    private GenerateApiTsInterfaceService $generateApiTsInterfaceService;

    public function __construct(
        EntityManagerInterface $entityManager,
        GenerateApiTsInterfaceService $generateApiTsInterfaceService
    )
    {
        $this->entityManager = $entityManager;
        $this->generateApiTsInterfaceService = $generateApiTsInterfaceService;
    }


    /**
     * 生成modal
     * @param $endpoints //某个controller下所有的api信息
     * @param $suffix // modal中interface或者type的后缀
     * @param $type // modal的类型 request或response
     * @return string
     * @throws \Exception
     */
    function getModalResult($endpoints, $suffix, $type): string
    {
        // 如果是响应体 顶部写TokenUserImport
        $tsInterface = $type == "response" ? $this->generateApiTsInterfaceService->generateTokenUserImport() : "";

        // 遍历r1的所有$endpoints
        foreach ($endpoints as $endpoint){
            if ($endpoint['method'] == "ANY"){
                $endpoint['method'] = "GET";
            }elseif($endpoint['method'] == "PATCH|PUT"){
                $endpoint['method'] = "PUT";
            }
            // 通过path和method去找记录
            $recordApiInfo = $this->entityManager->getRepository(RecordApiInfo::class)->findOneBy(
                [
                    'new_url' => $endpoint['path'],
                    'new_method' => $endpoint['method']
                ]
            );

            // interface的名字为去掉handle和execute剩下的单词首字母大写，再+$suffix
            $function = GenerateUtil::upperFirst(
                GenerateUtil::removePrefixFromArray(
                    $endpoint['function'],
                    array("handle","execute")
                )
            );

            // 根据不同的类型选择获取记录，$values是表里记录的各种请求或响应的组合
            switch ($type){
                case "response":
                    $values = $recordApiInfo->getResponse();
                    break;
                case "request":
                    $values = $recordApiInfo->getRequest();
                    break;
                default:
                    throw ExceptionFactory::InternalServerException("类型错误");
            }

            $values = array_filter($values, function($item) {
                return !empty($item);
            });// 去掉空数组

            // 生成type的注释
            $tsInterface .= $this->generateApiTsInterfaceService->generateTsNote($function, 'Type: ',$suffix, false);
            // 根据$values的数量生成type允许的interface
            $tsInterface .= $this->generateApiTsInterfaceService->generateType(count($values) ,$function, $suffix);

            foreach ($values as $key => $value){
                if (empty($value)){
                    continue;
                }
                // 循环中generateApiTsInterfaceService只有一个实例，需要重置
                $this->generateApiTsInterfaceService->result = array();
                // 生成interface注释(单行注释)
                $tsInterface .= $this->generateApiTsInterfaceService->generateTsNote($function, '',$suffix. ($key+1));
                // 生成interface 默认深度2层
                $tsInterface .= $this->generateApiTsInterfaceService->generateSimpleApiTsInterface($value, $function.$suffix. ($key+1),false,2);
            }
        }

        return $tsInterface;
    }
}

