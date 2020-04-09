<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Mongo\ManageCollection;

/**
 * @di\service({
 *     private: true
 * })
 */
class ManageProcessCollection extends ManageCollection
{
    /**
     * @di\arguments({
     *     uri: "%mongo_uri%",
     *     db:  "%mongo_db%"
     * })
     *
     * @param string $uri
     * @param string $db
     */
    public function __construct(
        string $uri,
        string $db
    ) {
        parent::__construct(
            $uri,
            $db,
            'yosmy_payment_card_ownership_processes',
            [
                'typeMap' => array(
                    'root' => Process::class,
                ),
            ]
        );
    }
}
