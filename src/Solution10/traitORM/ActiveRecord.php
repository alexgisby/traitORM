<?php namespace Solution10\traitORM;

/**
 * Active Record Trait Implementation.
 *
 * @uses        DoctrineDBAL
 * @package     Solution10\traitORM
 * @author      Alex Gisby <alex@solution10.com>
 * @license     MIT
 */
trait ActiveRecord
{
    /**
     * @var     array   Holds the original data of this model
     */
    protected $_original = [];

    /**
     * @var     array   Holds any changed info about the model
     */
    protected $_changed = [];

    /**
     * @var     string  Name of the database connection for this model
     */
    protected $_connectionName = 'default';

    /**
     * Magic set
     *
     * @param   string  $name   Name of the property to set
     * @param   mixed   $value  Value to set
     * @return  void
     */
    public function __set($name, $value)
    {
        $this->_changed[$name] = $value;
    }

    /**
     * Magic get. Will return the item in the model with this key,
     * or a property on this object, or null if nothing found.
     *
     * @param   string  $name  Key name to grab
     * @return  mixed
     */
    public function __get($name)
    {
        if(array_key_exists($name, $this->_changed)) {
            return $this->_changed[$name];
        }

        if(array_key_exists($name, $this->_original)) {
            return $this->_original[$name];
        }

        return null;
    }

    /**
     * Magic isset()
     *
     * @param   string  $name   Name of the property to check
     * @return  bool
     */
    public function __isset($name)
    {
        return (isset($this->_original[$name]) || isset($this->_changed[$name]));
    }

    /**
     * Allows you to set values through a method either one by one, or with
     * a keyed array.
     *
     * @param   string|array    $name   Either the name of the field to set, or the key-value array
     * @param   mixed           $value  Either the value, or leave null if you're using an array
     * @return  $this
     */
    public function set($name, $value = null)
    {
        if(!is_array($name)) {
            $name = [$name => $value];
        }
        foreach($name as $field => $value) {
            // Hand off to magic set.
            $this->$field = $value;
        }
        return $this;
    }

    /**
     * Allows you to get a value through a method, or you can retrieve multiple values at
     * once, by providing an array.
     *
     * @param   string|array    $name   Either a single field name, or an array of them
     * @return  mixed                   Could be anything really
     */
    public function get($name)
    {
        if(!is_array($name)) {
            return $this->$name;
        } else {
            $values = [];
            foreach($name as $n) {
                if(isset($this->$n)) {
                    $values[$n] = $this->$n;
                }
            }
            return $values;
        }
    }

    /**
     * Returns a key-value array of all the fields that have changed since the last save.
     * Array looks like this:
     *
     *      '{fieldname}' => [
     *          'original' => '{value}',
     *          'changed' => '{value}'
     *      ]
     *
     * If an item is not present in the diff, it has npt beed changed.
     *
     * @return  array
     */
    public function diff()
    {
        $diff = [];
        foreach($this->_changed as $key => $value) {
            if(!isset($this->_original[$key]) || $value !== $this->_original[$key]) {
                $diff[$key] = [
                    'original' => (isset($this->_original[$key]))? $this->_original[$key] : null,
                    'changed' => $value
                ];
            }
        }
        return $diff;
    }

    /**
     * Saves the model to the data store
     *
     * @return  bool    True for success, false for failure
     */
    public function save()
    {
        foreach($this->_changed as $key => $value) {
            $this->_original[$key] = $value;
        }
        return true;
    }

    /**
     * Sets/Gets the connection name for this class to use. When needed, the connection is
     * loaded from the Solution10\traitORM\ConnectionManager
     *
     * @param   string|null  $name   Pass a string to set the name, or null to get it
     * @return  $this|string
     */
    public function connectionName($name = null)
    {
        if($name === null) {
            return $this->_connectionName;
        }
        $this->_connectionName = $name;
        return $this;
    }

}
