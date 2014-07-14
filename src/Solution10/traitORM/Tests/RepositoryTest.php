<?php

namespace Solution10\traitORM\Tests;

use Solution10\traitORM\Tests\Stubs\ArrayRepository;
use Solution10\traitORM\Tests\Stubs\ArrayStorageDelegate;

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

    public function testCreateItem()
    {
        $storage = new ArrayStorageDelegate();
        $repo = new ArrayRepository();
        $repo->setStorageDelegate($storage);

        // Go ahead and store an item:
        $item = $repo->newRepoItem();
        $item->setValue('name', 'Alex');
        $repo->createItem($item);

        // Verify that it's in the store and has an ID:
        $this->assertTrue($item->isValueSet('id'));
        $this->assertTrue(isset($storage->store['arraystore'][0]));
        $this->assertEquals('Alex', $storage->store['arraystore'][0]['name']);
    }

    public function testUpdateItem()
    {
        $storage = new ArrayStorageDelegate();
        $repo = new ArrayRepository();
        $repo->setStorageDelegate($storage);

        $item = $repo->newRepoItem();
        $item->setValue('name', 'Alex');
        $repo->createItem($item);

        // Right, now edit this and re-store it:
        $item->setValue('name', 'Monkey');
        $this->assertTrue($item->hasChanges());
        $repo->updateItem($item);
        $this->assertFalse($item->hasChanges());

        // Check that the data store has updated:
        $this->assertEquals('Monkey', $storage->store['arraystore'][0]['name']);
    }

    public function testSaveItem()
    {
        $storage = new ArrayStorageDelegate();
        $repo = new ArrayRepository();
        $repo->setStorageDelegate($storage);

        $item = $repo->newRepoItem();
        $item->setValue('name', 'Alex');
        $repo->saveItem($item);
        $this->assertTrue($item->isValueSet('id'));
        $this->assertTrue(isset($storage->store['arraystore'][0]));
        $this->assertEquals('Alex', $storage->store['arraystore'][0]['name']);

        // Right, now edit this and re-store it using saveItem()
        $item->setValue('name', 'Monkey');
        $this->assertTrue($item->hasChanges());
        $repo->saveItem($item);
        $this->assertFalse($item->hasChanges());

        // Check that the data store has updated:
        $this->assertEquals('Monkey', $storage->store['arraystore'][0]['name']);
    }

    public function testDeleteItem()
    {
        $storage = new ArrayStorageDelegate();
        $repo = new ArrayRepository();
        $repo->setStorageDelegate($storage);

        $item = $repo->newRepoItem();
        $item->setValue('name', 'Alex');
        $repo->saveItem($item);

        // Now delete the item:
        $this->assertTrue($repo->deleteItem($item));

        // Verify it's gone in the data store:
        $this->assertFalse(isset($storage->store['arraystore'][0]));
    }

    public function testFindById()
    {
        $storage = new ArrayStorageDelegate();
        $repo = new ArrayRepository();
        $repo->setStorageDelegate($storage);

        // Hard-insert an item to save faffing about:
        $storage->store['arraystore'][0] = [
            'id' => 1,
            'name' => 'Alex'
        ];

        $item = $repo->findById(1);

        $this->assertNotNull($item);
        $this->assertEquals(1, $item->getValue('id'));
        $this->assertEquals('Alex', $item->getValue('name'));
    }

    public function testFindByIdNotFound()
    {
        $repo = $this->newRepoObject();
        $item = $repo->findById(27);

        $this->assertFalse($item->isLoaded());
    }
}
