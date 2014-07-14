<?php

namespace Solution10\traitORM;

/**
 * Interface StorageDelegate
 *
 * The storage delegate handles the actual reading and writing of an item.
 * traitORM provides a basic database provider for your usage, but you could
 * easily turn this into a REST service or the like.
 *
 * @package     Solution10\traitORM
 * @author      Alex Gisby <alex@solution10.com>
 * @license     MIT
 */
interface StorageDelegateInterface
{
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
    public function insertData($type, array $data);

    /**
     * Updates a record in the store. See insertData() for notes on $type.
     *
     * @param   string  $type   The broad type of this data as a hint for storage location.
     * @param   array   $id     key-value pair denoting primary key field and value: ['user_id' => 1]
     * @param   array   $data   Data to update with
     * @return  mixed
     */
    public function updateData($type, array $id, array $data);


    /**
     * Deletes a record from the data store.
     *
     * @param   string  $type   The broad type of this data as a hint for storage location.
     * @param   array   $id     key-value pair denoting primary key field and value: ['user_id' => 1]
     * @return  bool            Whether the result was a success or not.
     */
    public function deleteData($type, array $id);

    /**
     * Fetch an item by a given ID. One of the few built in queries to traitORM
     *
     * @param   array   $id     key-value pair denoting primary key field and value: ['user_id' => 1]
     * @return  mixed           raw data representing this item or NULL if not found.
     */
    public function findById(array $id);

    /**
     * Runs a query against the data store. This is left really open so you can decide what's best
     * for your data store.
     *
     * The result will be passed back in whatever format you deem best.
     *
     * @param   mixed   $query
     * @param   mixed   $params
     * @return  mixed
     */
    public function query($query, $params);
}