<?php

namespace Solution10\traitORM;

/**
 * Class Repository
 *
 * Add this trait to a class to make it become a "Repository".
 * Repos handle the saving and loading of items within it, and they
 * delegate the actual saving and loading to StorageDelegate implementers.
 * This means that your repo can be powered by anything, a REST API or
 * a database. Your choice my friend.
 *
 * @package     Solution10\traitORM
 * @author      Alex Gisby <alex@solution10.com>
 * @license     MIT
 */
trait Repository
{
    /**
     * @var     StorageDelegate
     */
    protected $_storage;

    /**
     * Returns the broad type of this repository. This is used in the
     * StorageDelegate calls to help work out where to plonk things.
     *
     * @return  string
     */
    abstract public function type();

    /**
     * Returns the primary key field name for this repository.
     *
     * @return  string
     */
    public function primaryKeyField()
    {
        return 'id'; // Override as appropriate
    }

    /**
     * This is passed as a callback to RepositoryResult as a way of knowing
     * how to construct items from this repo. It's the RepoItem factory for
     * this Repository.
     *
     * @param   mixed               $rawData
     * @return  RepoItemInterface
     */
    abstract public function itemFactory($rawData);

    /**
     * Sets the storage delegate for this Repository.
     *
     * @param   StorageDelegate     $storage
     * @return  $this
     */
    public function setConnection(StorageDelegate $storage)
    {
        $this->_storage = $storage;
        return $this;
    }

    /**
     * ---------------- CRUD Functions ----------------------
     */

    /**
     * Persists an item in the data store. Saves it in other words. Works for create and update,
     * just pass in the object you want saving and it will do the rest.
     *
     * This is a shortcut for createItem and updateItem.
     *
     * @param   RepoItemInterface   $item
     * @return  RepoItemInterface   Item that we persisted
     */
    public function saveItem(RepoItemInterface $item)
    {
        // Work out if this item is saved or not for a save/update
        return ($item->isValueSet($this->primaryKeyField()))?
                    $this->_updateItem($item)
                    : $this->_createItem($item);
    }

    /**
     * Creates a new item within the data store.
     *
     * @param   RepoItemInterface   $item   Item to create for
     * @return  RepoItemInterface
     */
    public function createItem(RepoItemInterface $item)
    {
        $iid = $this->_storage->insertData($this->type(), $item->getChanges());

        // Update the primary key and set as saved:
        $item->setValue($this->primaryKeyField(), $iid);
        $item->setAsSaved();
        return $item;
    }

    /**
     * Updates an item within the data store.
     *
     * @param   RepoItemInterface   $item
     * @return  mixed
     */
    public function updateItem(RepoItemInterface $item)
    {
        $this->_storage->updateData(
            $this->type(),
            [$this->primaryKeyField() => $item->getValue($this->primaryKeyField())],
            $item->getChanges()
        );

        // Mark as saved and return:
        $item->setAsSaved();
        return $item;
    }

    /**
     * Removes an item from the data store.
     *
     * @param   RepoItemInterface   $item
     * @return  bool
     */
    public function deleteItem(RepoItemInterface $item)
    {
        return $this->_storage->deleteData(
            $this->type(),
            [$this->primaryKeyField() => $item->getValue($this->primaryKeyField())]
        );
    }
}
