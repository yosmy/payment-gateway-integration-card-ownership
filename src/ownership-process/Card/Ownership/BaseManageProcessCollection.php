<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Mongo;

class BaseManageProcessCollection extends Mongo\BaseManageCollection implements ManageProcessCollection
{
    /**
     * @param string $uri
     * @param string $db
     * @param string $collection
     * @param string $root
     */
    public function __construct(
        string $uri,
        string $db,
        string $collection,
        string $root
    ) {
        parent::__construct(
            $uri,
            $db,
            $collection,
            [
                'typeMap' => array(
                    'root' => $root,
                ),
            ]
        );
    }
}
