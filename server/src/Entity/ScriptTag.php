<?php

namespace App\Entity;

use App\Repository\ScriptTagRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScriptTagRepository::class)
 */
class ScriptTag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Script", inversedBy="scriptTag")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="script_id", referencedColumnName="id")
     * })
     */
    private $script;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="scriptTag")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     * })
     */
    private $tag;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * @param mixed $script
     */
    public function setScript($script): void
    {
        $this->script = $script;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag): void
    {
        $this->tag = $tag;
    }


}
