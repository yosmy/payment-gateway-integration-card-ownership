<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Mongo;

class Processes extends Mongo\Collection
{
    /**
     * @var Process[]
     */
    protected $cursor;
}

