<?php

namespace Solution10\traitORM\Tests\Stubs;

use Solution10\traitORM\Repository;

/**
 * Dead simple Repository used for testing basis.
 */
class ArrayRepository
{
    use Repository;

    public function __construct()
    {
        // Set up the item factory:
        $this->setItemFactory(function ($rawData) {
            $item = new ArrayRepoItem();
            $item->loadFromRepoResource($rawData);
            return $item;
        });
    }

    public function type()
    {
        return 'arraystore';
    }
}
