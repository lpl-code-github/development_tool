<?php

namespace App\Entity;

use App\Repository\RecordApiInfoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecordApiInfoRepository::class)
 */
class RecordApiInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $old_url;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $old_method;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $new_url;

    /**
     * @ORM\Column(type="string", length=10,nullable=true)
     */
    private $new_method;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $request;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $response;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getOldUrl()
    {
        return $this->old_url;
    }

    /**
     * @param mixed $old_url
     */
    public function setOldUrl($old_url): void
    {
        $this->old_url = $old_url;
    }

    /**
     * @return mixed
     */
    public function getOldMethod()
    {
        return $this->old_method;
    }

    /**
     * @param mixed $old_method
     */
    public function setOldMethod($old_method): void
    {
        $this->old_method = $old_method;
    }

    /**
     * @return mixed
     */
    public function getNewUrl()
    {
        return $this->new_url;
    }

    /**
     * @param mixed $new_url
     */
    public function setNewUrl($new_url): void
    {
        $this->new_url = $new_url;
    }

    /**
     * @return mixed
     */
    public function getNewMethod()
    {
        return $this->new_method;
    }

    /**
     * @param mixed $new_method
     */
    public function setNewMethod($new_method): void
    {
        $this->new_method = $new_method;
    }


    public function getRequest()
    {
        $temp = str_replace("/", "", $this->request??'[]');
        return json_decode($temp, true);
    }

    public function setRequest($request): self
    {
        $this->request = json_encode($request);
        return $this;
    }

    public function getResponse()
    {
        $temp = str_replace("/", "", $this->response??'[]');
        return json_decode($temp, true);
    }

    public function setResponse($response): self
    {
        $this->response = json_encode($response);
        return $this;
    }
}
