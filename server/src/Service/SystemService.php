<?php

namespace App\Service;

use App\Factory\ExceptionFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Process;

class SystemService
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        ParameterBagInterface $parameterBag
    )
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * 获取系统硬件信息
     * 包括cpu占用率 内存占用率
     * @param string $type 硬件类型
     * @return float
     */
    public function getSystemInfo(string $type)
    {
        switch ($type){
            case 'cpu':
                return (float) shell_exec($this->parameterBag->get('cpu_usage_command'));
            case 'memory':
                return (float) shell_exec($this->parameterBag->get('memory_usage_command'));
            case 'top5_ps':
                return (string) shell_exec($this->parameterBag->get('top5_ps_command'));
            default:
                return 0.00;
        }
    }
}