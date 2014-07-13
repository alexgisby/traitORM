<?php

namespace Solution10\traitORM;
use Doctrine\DBAL\Connection;

/**
 * Class Repository
 *
 * Trait that eases the use of the Repository pattern.
 * This sucker does all the saving and loading you would want.
 *
 * @package Solution10\traitORM
 */
trait Repository
{
    /**
     * @var Connection
     */
    protected $_conn;

    abstract public function tableName();
    abstract public function itemFactory($rawData);

    public function primaryKey()
    {
        return 'id';
    }

    public function setConnection(Connection $conn)
    {
        $this->_conn = $conn;
    }

    /**
     * ---------------- CRUD Functions ----------------------
     */

    public function saveItem(RepoItemInterface $item)
    {
        // Work out if this item is saved or not for a save/update
        return ($item->isValueSet($this->primaryKey()))?
                    $this->_updateItem($item)
                    : $this->_createItem($item);
    }

    protected function _createItem(RepoItemInterface $item)
    {
        $sql = 'INSERT INTO ' . $this->tableName() . ' SET ';
        $sql .= implode(' = ?, ', array_keys($item->getChanges())) . ' = ?';

        $stmt = $this->_conn->prepare($sql);
        foreach(array_values($item->getChanges()) as $idx => $value) {
            $stmt->bindValue($idx + 1, $value);
        }

        $result = $stmt->execute();

        // Grab the insert ID to flesh out this model:
        $iid = $this->_conn->lastInsertId();
        $item->setValue($this->primaryKey(), $iid);
        $item->setAsSaved();

        return $result;
    }

    protected function _updateItem(RepoItemInterface $item)
    {
        $changed = $item->getChanges();

        $sql = 'UPDATE ' . $this->tableName() . ' SET ';
        $sql .= implode(' = ?, ', array_keys($changed)) . ' = ?';
        $sql .= ' WHERE ' . $this->primaryKey() . ' = ?';

        $stmt = $this->_conn->prepare($sql);
        foreach(array_values($changed) as $idx => $value) {
            $stmt->bindValue($idx + 1, $value);
        }
        $stmt->bindValue(count($changed)+1, $item->getValue($this->primaryKey()));

        $result = $stmt->execute();

        // Mark the item as saved:
        $item->setAsSaved();

        return $result;
    }

    /**
     * ------------- Common Find Operations -------------
     */

    /**
     * @param $id
     * @return RepoItemInterface
     */
    public function findById($id)
    {
        $query = $this->_conn->prepare('
            SELECT
                *
            FROM
                `'.$this->tableName().'`
            WHERE
                `'.$this->primaryKey().'` = :idValue
            LIMIT
                1;
        ');
        $query->bindValue('idValue', $id);
        $query->execute();

        return $this->itemFactory($query->fetch());
    }
}