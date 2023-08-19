<?php
//
// 测试用，此代码应该放在r1
//namespace App\EventSubscriber;
//
//use App\Entity\RecordApiInfo;
//use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Component\EventDispatcher\EventSubscriberInterface;
//use Symfony\Component\HttpKernel\Event\RequestEvent;
//use Symfony\Component\HttpKernel\Event\ResponseEvent;
//use Symfony\Component\HttpKernel\KernelEvents;
//
//class RecordSubscriber implements EventSubscriberInterface
//{
//    private $entityManager;
//
//    public function __construct(
//        EntityManagerInterface $entityManager
//    )
//    {
//        $this->entityManager = $entityManager;
//    }
//
//    public static function getSubscribedEvents(): array
//    {
//        return [
//            KernelEvents::REQUEST => ['onKernelRequest', 999],
//            KernelEvents::RESPONSE => ['onKernelResponse', 999]
//        ];
//    }
//
//    public function onKernelRequest(RequestEvent $event): void
//    {
//        $request = $event->getRequest();
//        $method = $request->getMethod();
//        $sep_array = explode('/index.php', $_SERVER['PHP_SELF']);
//        if ($sep_array[0] != '') {
//            $temp_array = explode($sep_array[0], $request->getRequestUri());
//            $uri = end($temp_array);
//        } else {
//            $uri = $request->getRequestUri();
//        }
//        $pos = strpos($uri, "?"); // 找到问号的位置
//        if ($pos !== false) {
//            $uri = substr($uri, 0, $pos); // 截取问号前面的内容
//        }
//
//        /**
//         * 获取请求内容
//         */
//        if ($method == "GET") { // get请求 要将所有的key value转为关联数组
//            $param = $request->query->all();
//            if (array_key_exists('_url', $param)) {
//                unset($param['_url']);
//            }
//        } else { // post delete put patch请求
//            $param = json_decode($request->getContent(), true);
//            // 如果存在token，去掉token
//            if (array_key_exists('token', $param)) {
//                unset($param['token']);
//            }
//        }
//
//
//        // 查找url对应的所有所有响应请求
//        $recordApiInfo = $this->entityManager->getRepository(RecordApiInfo::class)->findOneBy(
//            [
//                'old_url' => $uri,
//                'old_method' => $method
//            ]
//        );
//        if ($recordApiInfo) { // 存在
//            $recordRequests = $recordApiInfo->getRequest();
//
//            /**
//             * 查找是否存在相同key的请求体
//             */
//            $push = true; // 是否需要push新的数组
//
//            foreach ($recordRequests as $recordRequest) {
//                // 判断外层
//                if (empty(array_diff(array_keys($recordRequest), array_keys($param))) && empty(array_diff(array_keys($param), array_keys($recordRequest)))) {
//                    // 检查每个数组对象的key是否和新的数组的key完全相同
//                    if (array_key_exists('data', $param) && array_key_exists('data', $recordRequest)) { // 如果返回体包含data
//                        if (empty(array_diff(array_keys($recordRequest['data']), array_keys($param['data']))) && empty(array_diff(array_keys($param['data']), array_keys($recordRequest['data'])))) {
//                            $push = false; // 如果完全相同则不需要push
//                            break;
//                        }
//                    }else{
//                        $push = false; // 如果完全相同则不需要push
//                        break;
//                    }
//                }
//            }
//
//            if ($push) {
//                $recordRequests[] = $param;
//                $recordApiInfo->setRequest($recordRequests);
//            }
//        } else {
//            $recordApiInfo = new RecordApiInfo();
//            $recordRequests[] = $param;
//            $recordApiInfo->setOldUrl($uri);
//            $recordApiInfo->setOldMethod($method);
//            $recordApiInfo->setRequest($recordRequests);
//        }
//
//
//        $this->entityManager->persist($recordApiInfo);
//        $this->entityManager->flush();
//    }
//
//    public function onKernelResponse(ResponseEvent $event): void
//    {
//        $request = $event->getRequest();
//        $response = $event->getResponse();
//        $rsp = json_decode($response->getContent(), true);
//        $method = $request->getMethod();
//        $sep_array = explode('/index.php', $_SERVER['PHP_SELF']);
//        if ($sep_array[0] != '') {
//            $temp_array = explode($sep_array[0], $request->getRequestUri());
//            $uri = end($temp_array);
//        } else {
//            $uri = $request->getRequestUri();
//        }
//        $pos = strpos($uri, "?"); // 找到问号的位置
//        if ($pos !== false) {
//            $uri = substr($uri, 0, $pos); // 截取问号前面的内容
//        }
//
//        // 查找url对应的所有所有响应请求
//        $recordApiInfo = $this->entityManager->getRepository(RecordApiInfo::class)->findOneBy(
//            [
//                'old_url' => $uri,
//                'old_method' => $method
//            ]
//        );
//        if ($recordApiInfo) { // 存在
//            $recordResponses = $recordApiInfo->getResponse();
//
//            /**
//             * 查找是否存在相同key的响应体
//             */
//            $push = true; // 是否需要push新的数组
//
//            foreach ($recordResponses as $recordResponse) {
//                // 判断外层
//                if (empty(array_diff(array_keys($recordResponse), array_keys($rsp))) && empty(array_diff(array_keys($rsp), array_keys($recordResponse)))) {
//                    // 检查每个数组对象的key是否和新的数组的key完全相同
//                    if (array_key_exists('data', $rsp) && array_key_exists('data', $recordResponse)) { // 如果返回体包含data
//                        if (empty(array_diff(array_keys($recordResponse['data']), array_keys($rsp['data']))) && empty(array_diff(array_keys($rsp['data']), array_keys($recordResponse['data'])))) {
//                            $push = false; // 如果完全相同则不需要push
//                            break;
//                        }
//                    }else{
//                        $push = false; // 如果完全相同则不需要push
//                        break;
//                    }
//                }
//            }
//            if ($push) {
//                $recordResponses[] = $rsp;
//                $recordApiInfo->setResponse($recordResponses);
//            }
//
//        } else {
//            $recordApiInfo = new RecordApiInfo();
//            $recordResponses[] = $rsp;
//            $recordApiInfo->setOldUrl($uri);
//            $recordApiInfo->setOldUrl($method);
//            $recordApiInfo->setResponse($recordResponses);
//        }
//
//        $this->entityManager->persist($recordApiInfo);
//        $this->entityManager->flush();
//    }
//}