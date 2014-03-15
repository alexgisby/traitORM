<?php
namespace Solution10\traitORM\Tests;

/**
 * Active Record Tests
 */
class ActiveRecordTest extends Util\TestCase
{
    /**
     * Returns a test object that implements the Active Record trait
     */
    protected function _newTraitObject()
    {
        return $this->getObjectForTrait('Solution10\\traitORM\\ActiveRecord');
    }

    /**
     * This is perhaps slightly mistrustful of PHPUnit, but it means we cover off
     * our own test cases logic. Can't hurt right?
     */
    public function testObjectImplementsActiveRecord()
    {
        $object = $this->_newTraitObject();
        $this->assertArrayHasKey('Solution10\\traitORM\\ActiveRecord', class_uses($object));
    }


    public function testBasicSetGet()
    {
        $object = $this->_newTraitObject();
        $object->name = 'Alex';
        $this->assertEquals('Alex', $object->name);
    }

    public function testBasicIsset()
    {
        $object = $this->_newTraitObject();
        $this->assertFalse(isset($object->name));
        $object->name = 'Alex';
        $this->assertTrue(isset($object->name));
    }

    public function testBasicDiff()
    {
        $object = $this->_newTraitObject();
        $object->name = 'Alex';
        $this->assertEquals([
            'name' => [
                'original' => null,
                'changed' => 'Alex'
            ]
        ], $object->diff());
    }

    public function testEmptyDiffAfterSave()
    {
        $object = $this->_newTraitObject();
        $object->name = 'Alex';
        $object->save();
        $this->assertEquals([], $object->diff());
    }

    public function testDiffChangedProperties()
    {
        $object = $this->_newTraitObject();
        $object->name = 'Alex';
        $object->save();
        $object->name = 'Alexander';

        $this->assertEquals([
            'name' => [
                'original' => 'Alex',
                'changed' => 'Alexander'
            ]
        ], $object->diff());
    }

    public function testGetSetWithChangedProperty()
    {
        $object = $this->_newTraitObject();
        $object->name = 'Alex';
        $object->save();
        $object->name = 'Alexander';
        $this->assertEquals('Alexander', $object->name);
    }
}