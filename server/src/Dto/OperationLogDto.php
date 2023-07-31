<?php

namespace App\Dto;


use App\Entity\OperationLog;

class OperationLogDto
{
    private $id;

    private $name;

    private $url;

    private $method;

    private $status_code;

    private $message;

    private $request_body;

    private $status;

    private $type;

    private ?\DateTimeInterface $created_at;

    /**
     * @param OperationLog $operationLog
     */
    public function __construct(OperationLog $operationLog)
    {
        $this->id = $operationLog->getId();
        $this->name = $operationLog->getName();
        $this->url = $operationLog->getUrl();
        $this->method = $operationLog->getMethod();
        $this->status_code = $operationLog->getStatusCode();
        $this->message = $operationLog->getMessage();
        $this->request_body = $operationLog->getRequestBody();
        $this->status = $operationLog->getStatus();
        $this->type = $operationLog->getType();
        $this->created_at = $operationLog->getCreatedAt();
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method): void
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * @param mixed $status_code
     */
    public function setStatusCode($status_code): void
    {
        $this->status_code = $status_code;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getRequestBody()
    {
        return $this->request_body;
    }

    /**
     * @param mixed $request_body
     */
    public function setRequestBody($request_body): void
    {
        $this->request_body = $request_body;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    public function toArray(array $fields = []): array
    {
        $resultArray = array();
        $resultArray["id"] = $this->getId();
        if (in_array("name", $fields)) {
            $resultArray["name"] = $this->getName();
        }
        if (in_array("method", $fields)) {
            $resultArray["method"] = $this->getMethod();
        }
        if (in_array("status", $fields)) {
            $resultArray["status"] = $this->getStatus();
        }
        if (in_array("message", $fields)) {
            $resultArray["message"] = $this->getMessage();
        }
        if (in_array("url", $fields)) {
            $resultArray["url"] = $this->getUrl();
        }
        if (in_array("created_at", $fields)) {
            $resultArray["created_at"] = $this->getCreatedAt()->format('Y-m-d H:i:s');
        }
        if (in_array("request_body", $fields)) {
            $resultArray["request_body"] = $this->getRequestBody();
        }
        if (in_array("type", $fields)) {
            $resultArray["type"] = $this->getType();
        }
        if (in_array("status_code", $fields)) {
            $resultArray["status_code"] = $this->getStatusCode();
        }

        return $resultArray;
    }
}
