<?php

namespace Solution10\traitORM\Tests;

class MagicRepoItemTest extends Util\TestCase
{
    /**
     * @return object Solution10\traitORM\MagicRepoItem
     */
    protected function _newTraitObject()
    {
        return $this->getObjectForTrait('Solution10\\traitORM\\MagicRepoItem');
    }

    public function testObjectImplementsRepoItem()
    {
        $object = $this->_newTraitObject();
        $this->assertArrayHasKey('Solution10\\traitORM\\MagicRepoItem', class_uses($object));
    }

    public function testSetGetValue()
    {
        $object = $this->_newTraitObject();
        $object->name = 'Alex';
        $this->assertEquals('Alex', $object->name);
    }

    public function testGetUnknownValue()
    {
        $object = $this->_newTraitObject();
        $this->assertNull($object->unknown);
    }

    public function testIsSet()
    {
        $object = $this->_newTraitObject();
        $this->assertFalse(isset($object->name));
        $object->name = 'Alex';
        $this->assertTrue(isset($object->name));
    }

    public function testSetAsSaved()
    {
        $object = $this->_newTraitObject();
        $object->name = 'Alex';

        // Test that name remains after save:
        $this->assertEquals($object, $object->setAsSaved());
        $this->assertEquals('Alex', $object->name);
    }

    public function testChanges()
    {
        $object = $this->_newTraitObject();
        $object->name = 'Alex';

        $this->assertTrue($object->hasChanges());
        $this->assertEquals([
            'name' => 'Alex'
        ], $object->getChanges());
    }

    public function testSaveClearsChanges()
    {
        $object = $this->_newTraitObject();
        $object->name = 'Alex';
        $object->city = 'London';

        $this->assertTrue($object->hasChanges());
        $object->setAsSaved();
        $this->assertFalse($object->hasChanges());
        $this->assertEquals([], $object->getChanges());
    }

    public function testLoadFromRepoResource()
    {
        $object = $this->_newTraitObject();

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
}