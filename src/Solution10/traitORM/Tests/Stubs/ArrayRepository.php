<?php

namespace Solution10\traitORM\Tests\Stubs;

use Solution10\traitORM\RepoItemInterface;
use Solution10\traitORM\Repository;

/**
 * Dead simple Repository used for testing basis.
 */
class ArrayRepository
{
    use Repository;

    public function type()
    {
        return 'arraystore';
    }

    /**
     * This is passed as a callback to RepositoryResult as a way of knowing
     * how to construct items from this repo. It's the RepoItem factory for
     * this Repository.
     *
     * @param   mixed   $rawData
     * @return  RepoItemInterface
     */
    public function itemFactory($rawData)
    {
        // TODO: Implement itemFactory() method.
    }
}
