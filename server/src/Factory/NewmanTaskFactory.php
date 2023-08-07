<?php

namespace App\Factory;

use App\Entity\NewmanTask;

class NewmanTaskFactory
{
    /**
     * Create an instance
     */
    public function create($name, $description): ?NewmanTask
    {
        // Perform some verification
        // ...

        $newmanTask = new NewmanTask();
        $newmanTask->setName($name);
        $newmanTask->setDescription($description);
        $newmanTask->setActive(0);
        return $newmanTask;
    }
}