<?php

namespace Solution10\traitORM\Tests;

class RepositoryTest extends Util\TestCase
{
    protected function newRepoObject()
    {
        $repo = new Stubs\ArrayRepository();
        $repo->setStorageDelegate(new Stubs\ArrayStorageDelegate());
        return $repo;
    }

    public function testObjectImplementsRepository()
    {
        $object = $this->newRepoObject();
        $this->assertArrayHasKey('Solution10\\traitORM\\Repository', class_uses($object));
    }
}