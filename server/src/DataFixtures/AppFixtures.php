<?php

namespace App\DataFixtures;

use App\Entity\AnonymicityType;
use App\Entity\Script;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->initialDefaultScript($manager);

        $manager->flush();
    }

    private function initialDefaultScript(ObjectManager $manager)
    {
        $insertObjectInfoArray = array(
            array(
                'name'=>'load r1 fixtrue(默认的)',
                'description'=>'运行这个脚本，将r1的env中所在数据库加载Fixture',
                'path' => '/var/app/server/resource/scripts/r1-fixtrues.sh',
                'properties' => []
            ),
            array(
                'name'=>'load r1 fixtrue',
                'description'=>'运行这个脚本，将命令行参数指定的数据库加载Fixture',
                'path' => '/var/app/server/resource/scripts/r1-fixtrues.sh',
                'properties' => ["--dbhost backenddevelopercontainer_mysql_1","--dbname r1_postman_test","--dbuser root","--dbpwd rootpassword"]
            ),
        );
        foreach ($insertObjectInfoArray as $insertObjectInfo)
        {
            $object = new Script();
            $object->setName($insertObjectInfo['name']);
            $object->setDescription($insertObjectInfo['description']);
            $object->setPath($insertObjectInfo['path']);
            $object->setProperties($insertObjectInfo['properties']);
            $manager->persist($object);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['defaultData'];
    }
}
