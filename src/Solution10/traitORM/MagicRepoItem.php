<?php

namespace Solution10\traitORM;

/**
 * Class MagicRepoItem
 *
 * Trait that extends from RepoItem to add the magic
 * methods in. Allows magic to be optional.
 *
 * @package     Solution10\traitORM
 * @author      Alex Gisby <alex@solution10.com>
 * @license     MIT
 */
trait MagicRepoItem
{
    use RepoItem;

    public function __get($name)
    {
        return $this->getValue($name);
    }

    public function __set($name, $value)
    {
        return $this->setValue($name, $value);
    }

    public function __isset($name)
    {
        return $this->isValueSet($name);
    }
}