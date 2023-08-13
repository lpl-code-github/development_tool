<?php

namespace App\Factory;

use App\Entity\Tag;

class TagFactory
{
    /**
     * Create an instance
     */
    public function create($name, $color): ?Tag
    {
        // Perform some verification
        // ...

        $tag = new Tag();
        $tag->setName($name);
        $tag->setColor($color);
        return $tag;
    }
}