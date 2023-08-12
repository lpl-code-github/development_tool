<?php

namespace App\Entity;

use App\Repository\NewmanTaskRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewmanTaskRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class NewmanTask
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
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $html_report_path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $excel_report_path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cli_output_path;
    /**
     * @ORM\Column(type="smallint")
     */
    private $active;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $log;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getHtmlReportPath(): ?string
    {
        return $this->html_report_path;
    }

    public function setHtmlReportPath(string $html_report_path): self
    {
        $this->html_report_path = $html_report_path;

        return $this;
    }

    public function getExcelReportPath(): ?string
    {
        return $this->excel_report_path;
    }

    public function setExcelReportPath(?string $excel_report_path): self
    {
        $this->excel_report_path = $excel_report_path;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCliOutputPath()
    {
        return $this->cli_output_path;
    }

    /**
     * @param mixed $cli_output_path
     */
    public function setCliOutputPath($cli_output_path): void
    {
        $this->cli_output_path = $cli_output_path;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLog()
    {
        $temp = str_replace("/", "", $this->log);
        return json_decode($temp, true);
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
     * @param mixed $log
     */
    public function setLog($log): self
    {
        $this->log = json_encode($log);
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime());

        if($this->getCreatedAt() == NULL)
        {
            $this->setCreatedAt(new \DateTime());
        }
    }
}
