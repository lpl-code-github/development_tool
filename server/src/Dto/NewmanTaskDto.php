<?php

namespace App\Dto;

use App\Entity\NewmanTask;

class NewmanTaskDto
{
    private $id;

    private $name;

    private $description;

    private $status;

    private $html_report_path;

    private $excel_report_path;

    private $cli_output_path;

    private $active;

    private $created_at;

    private $updated_at;

    private $log;

    public function __construct(NewmanTask $newmanTask)
    {
        $this->id = $newmanTask->getId();
        $this->name = $newmanTask->getName();
        $this->status = $newmanTask->getStatus();
        $this->description = $newmanTask->getDescription();
        $this->html_report_path = $newmanTask->getHtmlReportPath();
        $this->excel_report_path = $newmanTask->getExcelReportPath();
        $this->cli_output_path = $newmanTask->getCliOutputPath();
        $this->active = $newmanTask->getActive();
        $this->created_at = $newmanTask->getCreatedAt();
        $this->updated_at = $newmanTask->getUpdatedAt();
        $this->log = $newmanTask->getLog();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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


    public function getHtmlReportPath()
    {
        return $this->html_report_path;
    }

    public function setHtmlReportPath($html_report_path)
    {
        $this->html_report_path = $html_report_path;
    }

    public function getExcelReportPath()
    {
        return $this->excel_report_path;
    }

    public function setExcelReportPath($excel_report_path)
    {
        $this->excel_report_path = $excel_report_path;
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


    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param mixed $log
     */
    public function setLog($log): void
    {
        $this->log = $log;
    }


    public function toArray(array $fields = []): array
    {
        $resultArray = [];
        $resultArray["id"] = $this->getId();
        if (in_array("id", $fields)) {
            $resultArray["id"] = $this->getId();
        }
        if (in_array("name", $fields)) {
            $resultArray["name"] = $this->getName();
        }
        if (in_array("description", $fields)) {
            $resultArray["description"] = $this->getDescription();
        }
        if (in_array("status", $fields)) {
            $resultArray["status"] = $this->getStatus();
        }
        if (in_array("html_report_path", $fields)) {
            $resultArray["html_report_path"] = $this->getHtmlReportPath();
        }
        if (in_array("excel_report_path", $fields)) {
            $resultArray["excel_report_path"] = $this->getExcelReportPath();
        }
        if (in_array("cli_output_path", $fields)) {
            $resultArray["cli_output_path"] = $this->getExcelReportPath();
        }
        if (in_array("active", $fields)) {
            $resultArray["active"] = $this->getActive();
        }
        if (in_array("log", $fields)) {
            $resultArray["log"] = $this->getLog();
        }
        if (in_array("created_at", $fields)) {
            $resultArray["created_at"] = $this->getCreatedAt();
        }
        if (in_array("updated_at", $fields)) {
            $resultArray["updated_at"] = $this->getUpdatedAt();
        }
        return $resultArray;
    }
}