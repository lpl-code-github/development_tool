<?php

namespace App\Controller\Functional;

use App\Controller\BaseController;
use App\Factory\ExceptionFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends BaseController
{
    private ParameterBagInterface $parameterBag;

    /**
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }


    /**
     * @Route("/uploadFile", name="上传一个文件", methods={"POST"})
     * // 增加一种type类型 需要在OperationLogFactory的handleUploadFileRewriteActionName中增加一种类型处理
     * @throws \Exception
     */
    public function executeUpload(Request $request): Response
    {
        // 定义所允许的文件后缀
        $allowedExtensions = ['sh'];

        $file = $request->files->get('file');
        $type = $request->query->get('type') ?? null;

        if (!$type) {
            throw ExceptionFactory::WrongFormatException("缺少文件类型type参数");
        }
        if (!$file) {
            throw ExceptionFactory::WrongFormatException("文件上传失败");
        }
        // 检查文件后缀是否合法
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, $allowedExtensions)) {
            throw ExceptionFactory::WrongFormatException("不允许上传该类型的文件");
        }
        // 检查type是否合法，并且确定保存到哪个目录下
        switch ($type) {
            case "script":
                $targetDirectory = BASE_PATH . $this->parameterBag->get('script_file_path');
                break;
            default:
                throw ExceptionFactory::WrongFormatException("文件类型type参数不合法");
        }

        // 获取文件名并确保长度不超过操作系统文件名允许的最大长度
        $originalFilename = $file->getClientOriginalName();
        $filename = substr($originalFilename, 0, 255 - strlen($extension) - 1);

        // 防止文件重名
        $filename = $this->getAvailableFilename($filename, $targetDirectory);

        // 移动上传的文件到目标目录
        $file->move($targetDirectory, $filename);

        // 返回文件路径
        $filePath = $targetDirectory . $filename;
        $resultArray = [
            "data" => [
                "path" => $filePath
            ]
        ];
        $response = new Response();
        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/downloadFile", name="下载一个文件",methods={"POST"})
     * @throws \Exception
     */
    public function downloadFile(Request $request): Response
    {
        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        $this->validateNecessaryParameters($data, [
            'path' => self::STRING_TYPE
        ]);

        $filepath = $data['path'];
        // 检查文件是否存在
        $filesystem = new Filesystem();
        if (!$filesystem->exists($filepath)) {
            throw ExceptionFactory::NotFoundException("文件未找到");
        }

        // 创建文件响应
        $response = new BinaryFileResponse($filepath);
        $response->headers->set('Content-Type', 'application/octet-stream');

        return $response;
    }

    /**
     * 查找某目录下所有文件，如果有重名的就在文件名后加数字, 返回一个新的filename
     * @throws \Exception
     */
    private function getAvailableFilename($filename, $targetDirectory): string
    {
        $finder = new Finder();
        $finder->files()->in($targetDirectory);
        $existingFiles = [];
        foreach ($finder as $f) {
            $existingFiles[] = $f->getFilename();
        }

        $index = 1;
        $extension = pathinfo($filename, PATHINFO_EXTENSION); // 获取后缀
        $uniqueFilename = pathinfo($filename, PATHINFO_FILENAME);// 获取文件名
        $temp = $uniqueFilename;
        while (in_array($uniqueFilename . '.' . $extension, $existingFiles)) {
            $uniqueFilename = $temp . ' (' . $index . ')';
            $index++;
        }

        return $uniqueFilename.'.'.$extension;
    }
}