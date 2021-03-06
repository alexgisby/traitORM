<?php

namespace Solution10\traitORM\Tests;

class RepoItemTest extends Util\TestCase
{
    /**
     * @return object Solution10\traitORM\RepoItem
     */
    protected function newTraitObject()
    {
        return $this->getObjectForTrait('Solution10\\traitORM\\RepoItem');
    }

    public function testObjectImplementsRepoItem()
    {
        $object = $this->newTraitObject();
        $this->assertArrayHasKey('Solution10\\traitORM\\RepoItem', class_uses($object));
    }

    public function testSetValue()
    {
        $object = $this->newTraitObject();
        $this->assertEquals($object, $object->setValue('name', 'Alex'));
    }

    public function testSetGetValue()
    {
        $object = $this->newTraitObject();
        $object->setValue('name', 'Alex');
        $this->assertEquals('Alex', $object->getValue('name'));
    }

    public function testGetUnknownValue()
    {
        $object = $this->newTraitObject();
        $this->assertNull($object->getValue('unknown'));
    }

    public function testSetValues()
    {
        $object = $this->newTraitObject();
        $this->assertEquals($object, $object->setValues([
            'name' => 'Alex',
            'faveColour' => 'red',
        ]));

        $this->assertEquals('Alex', $object->getValue('name'));
        $this->assertEquals('red', $object->getValue('faveColour'));
    }

    public function testIsValueSet()
    {
        $object = $this->newTraitObject();
        $this->assertFalse($object->isValueSet('name'));
        $object->setValue('name', 'Alex');
        $this->assertTrue($object->isValueSet('name'));
    }

    public function testSetAsSaved()
    {
        $object = $this->newTraitObject();
        $object->setValue('name', 'Alex');

        // Test that name remains after save:
        $this->assertEquals($object, $object->setAsSaved());
        $this->assertEquals('Alex', $object->getValue('name'));
    }

    public function testChanges()
    {
        $object = $this->newTraitObject();
        $object->setValue('name', 'Alex');

        $this->assertTrue($object->hasChanges());
        $this->assertEquals([
            'name' => 'Alex'
        ], $object->getChanges());
    }

    public function testSaveClearsChanges()
    {
        $object = $this->newTraitObject();
        $object->setValues(['name' => 'Alex', 'city' => 'London']);

        $this->assertTrue($object->hasChanges());
        $object->setAsSaved();
        $this->assertFalse($object->hasChanges());
        $this->assertEquals([], $object->getChanges());
    }

    public function testLoadFromRepoResource()
    {
        $object = $this->newTraitObject();

        $object->loadFromRepoResource([
            'name' => 'Alex',
            'city' => 'London',
        ]);

        // That function should load everything into original immediately
        // and not report as changes:
        $this->assertFalse($object->hasChanges());
        $this->assertEquals([], $object->getChanges());

        // check that the values did actually save
        $this->assertEquals('Alex', $object->getValue('name'));
        $this->assertEquals('London', $object->getValue('city'));
    }

    public function testGetOriginal()
    {
        $object = $this->newTraitObject();
        $object->loadFromRepoResource([
            'name' => 'Alex',
            'city' => 'London',
        ]);

        $object->setValue('name', 'Jake');
        $this->assertEquals('Alex', $object->getOriginal('name'));
        $this->assertEquals('London', $object->getOriginal('city'));
    }

    public function testGetOriginalPostSave()
    {
        $object = $this->newTraitObject();
        $object->loadFromRepoResource([
            'name' => 'Alex',
            'city' => 'London',
        ]);

        $object->setValue('name', 'Jake');
        $object->setAsSaved();

        $this->assertEquals('Jake', $object->getOriginal('name'));
        $this->assertEquals('London', $object->getOriginal('city'));
    }

    public function testIsLoaded()
    {
        $object = $this->newTraitObject();
        $this->assertFalse($object->isLoaded());
        $object->setValue('name', 'Alex');
        $object->setAsSaved();
        $this->assertTrue($object->isLoaded());

        // And check if loaded from resource:
        $loadedObject = $this->newTraitObject();
        $loadedObject->loadFromRepoResource([
            'id' => 1,
            'name' => 'Alex',
        ]);
        $this->assertTrue($loadedObject->isLoaded());
    }
}
