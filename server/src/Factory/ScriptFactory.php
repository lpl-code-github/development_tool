<?php

namespace App\Factory;

use App\Entity\Script;

class ScriptFactory
{
    /**
     * Create an instance
     */
    public function create($name, $description, $path, $properties): ?Script
    {
        // Perform some verification
        // ...

        $script = new Script();
        $script->setName($name);
        $script->setDescription($description);
        $script->setPath($path);
        $script->setProperties($properties);
        return $script;
    }
}