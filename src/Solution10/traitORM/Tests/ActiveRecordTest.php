<?php

namespace Solution10\traitORM\Tests;
use Solution10\traitORM\Tests\Stubs\User as UserStub;

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

    public function testGetWithModelProperties()
    {
        // This object
        $object = new UserStub();
        $this->assertEquals($object->publicName, 'Alice');
    }

    /**
     * These should be set-able, but not appear in diffs etc
     */
    public function testSetWithModelProperties()
    {
        $object = new UserStub();

        $object->publicName = 'Allison';
        $this->assertEquals('Allison', $object->publicName);
        $this->assertEquals([], $object->diff());
    }

    public function testIssetWithProperties()
    {
        $object = new UserStub();
        $this->assertTrue(isset($object->publicName));
        $this->assertFalse(isset($object->protectedName));
        $this->assertFalse(isset($object->privateName));
    }

    /**
     * Testing the set function, rather than the magic set
     */
    public function testSetMethodNameValue()
    {
        $object = $this->_newTraitObject();
        $object->set('name', 'Alex');

        $this->assertEquals('Alex', $object->name);
    }

    public function testSetMethodArray()
    {
        $object = $this->_newTraitObject();
        $object->set([
            'name' => 'Alex',
            'email' => 'alex@solution10.com'
        ]);

        $this->assertEquals('Alex', $object->name);
        $this->assertEquals('alex@solution10.com', $object->email);
    }

    /**
     * Testing the get() function rather than magic get
     */
    public function testGetMethodString()
    {
        $object = $this->_newTraitObject();
        $object->name = 'Alex';

        $this->assertEquals('Alex', $object->get('name'));
    }

    public function testGetMethodArray()
    {
        $object = $this->_newTraitObject();
        $object->set([
            'name' => 'Alex',
            'email' => 'alex@solution10.com',
            'location' => 'London',
        ]);

        $this->assertEquals([
            'name' => 'Alex',
            'email' => 'alex@solution10.com',
        ], $object->get(['name', 'email']));
    }
}