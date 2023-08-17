<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/functional/uploadFile' => [[['_route' => '上传一个文件', '_controller' => 'App\\Controller\\Functional\\FileController::executeUpload'], null, ['POST' => 0], null, false, false, null]],
        '/functional/downloadFile' => [[['_route' => '下载一个文件', '_controller' => 'App\\Controller\\Functional\\FileController::downloadFile'], null, ['POST' => 0], null, false, false, null]],
        '/functional/generatePostmanTest' => [[['_route' => '生成POSTMAN测试', '_controller' => 'App\\Controller\\Functional\\GeneratorController::executeGeneratePostmanTest'], null, ['POST' => 0], null, false, false, null]],
        '/functional/generateTsInterface' => [[['_route' => '生成Api响应的TypeScript Interface', '_controller' => 'App\\Controller\\Functional\\GeneratorController::executeGenerateApiTsInterface'], null, ['POST' => 0], null, false, false, null]],
        '/functional/generateTsService' => [[['_route' => '生成Ts调用api的service', '_controller' => 'App\\Controller\\Functional\\GeneratorController::executeGenerateApiTsService'], null, ['GET' => 0], null, false, false, null]],
        '/functional/generateCode' => [[['_route' => '生成Resource类型的接口代码', '_controller' => 'App\\Controller\\Functional\\GeneratorController::executeGenerateCode'], null, ['GET' => 0], null, false, false, null]],
        '/functional/generateSlateDoc' => [[['_route' => '生成slate文档', '_controller' => 'App\\Controller\\Functional\\GeneratorController::executeGenerateSlateDoc'], null, ['GET' => 0], null, false, false, null]],
        '/functional/runNewman' => [[['_route' => '进行newman测试', '_controller' => 'App\\Controller\\Functional\\NewmanController::executeRunNewman'], null, ['POST' => 0], null, false, false, null]],
        '/functional/clearCache' => [[['_route' => '清除R1缓存', '_controller' => 'App\\Controller\\Functional\\RiskIdController::executeClearCache'], null, ['POST' => 0], null, false, false, null]],
        '/functional/getApiInfo' => [[['_route' => '获取RISKID所有API信息', '_controller' => 'App\\Controller\\Functional\\RiskIdController::executeGetApiInfo'], null, ['GET' => 0], null, false, false, null]],
        '/functional/getFileLists' => [[['_route' => '获取RISKID所有Entity列表', '_controller' => 'App\\Controller\\Functional\\RiskIdController::executeGetFileLists'], null, ['GET' => 0], null, false, false, null]],
        '/functional/switchStatus' => [[['_route' => '获取快捷开关操作', '_controller' => 'App\\Controller\\Functional\\RiskIdController::executeSwitchStatus'], null, ['GET' => 0], null, false, false, null]],
        '/functional/quickSwitch' => [[['_route' => '快捷开关操作', '_controller' => 'App\\Controller\\Functional\\RiskIdController::executeQuickSwitch'], null, ['PUT' => 0], null, false, false, null]],
        '/functional/getDatabaseList' => [[['_route' => '获取RiskId所在DB中所有的数据库', '_controller' => 'App\\Controller\\Functional\\RiskIdController::executeGetDatabaseList'], null, ['GET' => 0], null, false, false, null]],
        '/functional/runScript' => [[['_route' => '运行一个脚本', '_controller' => 'App\\Controller\\Functional\\ScriptController::executeRunScript'], null, ['POST' => 0], null, false, false, null]],
        '/functional/getSystemStatus' => [[['_route' => '获取系统状态', '_controller' => 'App\\Controller\\Functional\\SystemController::getSystemStatus'], null, ['GET' => 0], null, false, false, null]],
        '/functional/getPs' => [[['_route' => '获取系统排名前5的进程', '_controller' => 'App\\Controller\\Functional\\SystemController::getPs'], null, ['GET' => 0], null, false, false, null]],
        '/resource/databaseBackup' => [
            [['_route' => '获取数据库备份信息', '_controller' => 'App\\Controller\\Resource\\DatabaseBackupController::executeGet'], null, ['GET' => 0], null, false, false, null],
            [['_route' => '备份数据库', '_controller' => 'App\\Controller\\Resource\\DatabaseBackupController::executePost'], null, ['POST' => 0], null, false, false, null],
            [['_route' => '更新数据库备份信息', '_controller' => 'App\\Controller\\Resource\\DatabaseBackupController::executePut'], null, ['PUT' => 0], null, false, false, null],
            [['_route' => '删除数据库备份', '_controller' => 'App\\Controller\\Resource\\DatabaseBackupController::executeDelete'], null, ['DELETE' => 0], null, false, false, null],
        ],
        '/resource/databaseBackup/import' => [[['_route' => '导入备份到数据库', '_controller' => 'App\\Controller\\Resource\\DatabaseBackupController::executeImportDb'], null, ['POST' => 0], null, false, false, null]],
        '/resource/newman_tasks' => [
            [['_route' => 'get newmanTasks', '_controller' => 'App\\Controller\\Resource\\NewmanTasksController::executeGet'], null, ['GET' => 0], null, false, false, null],
            [['_route' => '创建测试任务', '_controller' => 'App\\Controller\\Resource\\NewmanTasksController::executePost'], null, ['POST' => 0], null, false, false, null],
            [['_route' => '更新Task的信息', '_controller' => 'App\\Controller\\Resource\\NewmanTasksController::executePut'], null, ['PUT' => 0], null, false, false, null],
            [['_route' => '删除一个Task', '_controller' => 'App\\Controller\\Resource\\NewmanTasksController::executeDelete'], null, ['DELETE' => 0], null, false, false, null],
        ],
        '/resource/operation_log/type' => [[['_route' => '获取日志类型', '_controller' => 'App\\Controller\\Resource\\OperationLogController::getOperationLogType'], null, ['GET' => 0], null, false, false, null]],
        '/resource/operation_log' => [[['_route' => '查询日志', '_controller' => 'App\\Controller\\Resource\\OperationLogController::getOperationLog'], null, ['GET' => 0], null, false, false, null]],
        '/resource/scripts' => [
            [['_route' => 'get scripts', '_controller' => 'App\\Controller\\Resource\\ScriptsController::executeGet'], null, ['GET' => 0], null, false, false, null],
            [['_route' => '保存一个新的脚本', '_controller' => 'App\\Controller\\Resource\\ScriptsController::executePost'], null, ['POST' => 0], null, false, false, null],
            [['_route' => '更新脚本', '_controller' => 'App\\Controller\\Resource\\ScriptsController::executePut'], null, ['PUT' => 0], null, false, false, null],
            [['_route' => '删除一个脚本', '_controller' => 'App\\Controller\\Resource\\ScriptsController::executeDelete'], null, ['DELETE' => 0], null, false, false, null],
        ],
        '/resource/tags' => [
            [['_route' => 'get tags', '_controller' => 'App\\Controller\\Resource\\TagController::executeGet'], null, ['GET' => 0], null, false, false, null],
            [['_route' => '保存一个新的标签', '_controller' => 'App\\Controller\\Resource\\TagController::executePost'], null, ['POST' => 0], null, false, false, null],
        ],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [
            [['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
