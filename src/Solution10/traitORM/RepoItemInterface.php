<?php

namespace Solution10\traitORM;

/**
 * Interface RepoItemInterface
 *
 * This interface allows you to define RepoItems that don't implement
 * the RepoItem trait to interact with repositories.
 *
 * RepoItem and MagicRepoItem provided by this package will both always
 * implement this method, so if you want to be lazy, just use them!
 *
 * @package     Solution10\traitORM
 * @author      Alex Gisby <alex@solution10.com>
 * @license     MIT
 */
interface RepoItemInterface
{
    /**
     * Populates the repo item with an initial hunk of data.
     * This is different from setValues in that it makes the
     * item think it's 'saved' and not modified. Use this for
     * filling objects from a database load.
     *
     * @param   array   $initialData
     * @return  $this
     */
    public function loadFromRepoResource(array $initialData);

    /**
     * Sets a value into the object. Will cause the object to be marked as changed.
     *
     * @param   string  $key
     * @param   mixed   $value
     * @return  $this
     */
    public function setValue($key, $value);

    /**
     * Sets multiple key/values in one go. Will cause the object to be marked as changed.
     *
     * @param   array   $data
     * @return  $this
     */
    public function setValues(array $data);

    /**
     * Gets a value out of the object. Will return the 'newest' value for this possible,
     * so if you load from a Repo, but change the value, this function returns the new value
     * that you set. To get the old value, use original().
     *
     * @param   string  $key
     * @return  null
     */
    public function getValue($key);

    /**
     * Returns the original (non-changed) value of the given key.
     *
     * @param   string  $key
     * @return  null
     */
    public function getOriginal($key);

    /**
     * Returns whether a value is set. Equivalent of isset().
     *
     * @param   string  $key
     * @return  bool
     */
    public function isValueSet($key);

    /**
     * Returns a key/value array of changed properties on this object.
     *
     * @return  array
     */
    public function getChanges();

    /**
     * Returns whether this object has changes waiting for save or not.
     *
     * @return  bool
     */
    public function hasChanges();

    /**
     * Marks this object as saved, clearing the changes and overwriting the original values.
     * Repositories should call this on objects they save.
     *
     * @return  $this
     */
    public function setAsSaved();

    /**
     * Whether this item has been loaded (or saved previously) to the database.
     * Handy for assessing the state of findById() queries in repos.
     *
     * @return  bool
     */
    public function isLoaded();
}
