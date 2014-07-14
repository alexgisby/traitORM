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

    public function testDefaultPrimaryKeyField()
    {
        $repo = $this->newRepoObject();
        $this->assertEquals('id', $repo->primaryKeyField());
    }


    public function testSetGetStorageDelegate()
    {
        $object = $this->newRepoObject();

        $newDelegate = new Stubs\ArrayStorageDelegate();
        $newDelegate->propertyToCheck = 'Test';
        $this->assertEquals(
            $object,
            $object->setStorageDelegate($newDelegate)
        );

        $this->assertEquals($newDelegate, $object->getStorageDelegate());
        $this->assertEquals('Test', $object->getStorageDelegate()->propertyToCheck);
    }

    public function testItemFactory()
    {
        $repo = $this->newRepoObject();
        $didFire = false;

        $factory = function ($rawData) use (&$didFire) {
            $didFire = true;
            return new Stubs\ArrayRepoItem($rawData);
        };

        $repo->setItemFactory($factory);
        $this->assertEquals($factory, $repo->getItemFactory());

        // Check that it does fire:
        $repo->newRepoItem();
        $this->assertTrue($didFire, 'itemFactory did fire custom callback');
    }

    /**
     * @expectedException       \Solution10\traitORM\Exception\RepositoryException
     * @expectedExceptionCode   Solution10\traitORM\Exception\RepositoryException::NO_FACTORY_DEFINED
     */
    public function testItemFactoryNoFactory()
    {
        $repo = $this->newRepoObject();
        $repo->setItemFactory(null);
        $repo->newRepoItem();
    }

    public function testNewRepoItem()
    {
        $repo = $this->newRepoObject();

        $item = $repo->newRepoItem();
        $this->assertInstanceOf('Solution10\\traitORM\\RepoItemInterface', $item);
    }



}
