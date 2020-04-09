<?php

namespace Yosmy\Payment\Card\Ownership\Defer;

use Yosmy\Mongo\DuplicatedKeyException;
use LogicException;

/**
 * @di\service({
 *     private: true
 * })
 */
class AddUser
{
    /**
     * @var ManageUserCollection
     */
    private $manageCollection;

    /**
     * @param ManageUserCollection $manageCollection
     */
    public function __construct(
        ManageUserCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string $id
     * @param string $period
     * @param int    $times
     * @param int    $amount
     */
    public function add(
        string $id,
        string $period,
        int $times,
        int $amount
    ) {
        try {
            $this->manageCollection->insertOne([
                '_id' => $id,
                'period' => $period,
                'times' => $times,
                'amount' => $amount,
            ]);
        } catch (DuplicatedKeyException $e) {
            throw new LogicException(null, null, $e);
        }
    }
}
