<?php

namespace Solution10\traitORM\Tests\Stubs;

use Solution10\traitORM\StorageDelegateInterface;

class ArrayStorageDelegate implements StorageDelegateInterface
{
    /**
     * Obviously, you'd never do this for real, it's just that the Unit Tests
     * can look inside the store and verify things.
     *
     * @var     array
     */
    public $store = [];

    /**
     * Creates a new record in the data store.
     * $type is a broad type of this item, generally used as a hint for the storage delegate
     * to know where to put it. For example, which database table, or REST resource type.
     *
     * $data should be a key-value array.
     *
     * The return value should be the new primary key of this object!
     *
     * @param   string  $type   A broad type for the storage delegate to use to know where to store this.
     * @param   array   $data   Data to store.
     * @return  mixed
     */
    public function insertData($type, array $data)
    {
        $this->store[$type][] = $data;
        return count($this->store)-1;
    }

    /**
     * Updates a record in the store. See insertData() for notes on $type.
     *
     * @param   string  $type   The broad type of this data as a hint for storage location.
     * @param   array   $id     key-value pair denoting primary key field and value: ['user_id' => 1]
     * @param   array   $data   Data to update with
     * @return  mixed
     */
    public function updateData($type, array $id, array $data)
    {
        $pkValue = array_values($id)[0];

        foreach ($data as $key => $value) {
            $this->store[$type][$pkValue][$key] = $value;
        }

        return true;
    }


    /**
     * Deletes a record from the data store.
     *
     * @param   string  $type   The broad type of this data as a hint for storage location.
     * @param   array   $id     key-value pair denoting primary key field and value: ['user_id' => 1]
     * @return  bool            Whether the result was a success or not.
     */
    public function deleteData($type, array $id)
    {
        $pkValue = array_values($id)[0];
        unset($this->store[$type][$pkValue]);
        return true;
    }

    /**
     * Fetch an item by a given ID. One of the few built in queries to traitORM
     *
     * @param   array   $id     key-value pair denoting primary key field and value: ['user_id' => 1]
     * @return  mixed           raw data representing this item
     */
    public function findById(array $id)
    {
        $pkField = array_keys($id)[0];
        $pkValue = array_values($id)[0];
        return $this->query($pkField, $pkValue);
    }

    /**
     * Runs a query against the data store.
     *
     * This is as basic as we get, loops through each item looking doing this:
     *
     *  $item[$query] == $value;
     *
     * If true, you get that item in the results.
     *
     * @param   mixed   $query
     * @param   mixed   $params
     * @return  mixed
     */
    public function query($query, $params)
    {
        $results = array();
        if (isset($this->store['arraystore'])) {
            foreach ($this->store['arraystore'] as $data) {
                if ($data[$query] === $params) {
                    $results[] = $data;
                }
            }
        }
        return $results;
    }
}
