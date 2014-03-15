<?php

namespace Solution10\traitORM\Tests\Stubs;
use Solution10\traitORM\ActiveRecord;

class User
{
    use ActiveRecord;

    public $publicName = 'Alice';
    protected $protectedName = 'Bob';
    private $privateName = 'Charlie';
}