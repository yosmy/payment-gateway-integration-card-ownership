<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class StartStatus
{
    /**
     * @var ManageStatusCollection
     */
    private $manageStatusCollection;

    /**
     * @param ManageStatusCollection $manageStatusCollection
     */
    public function __construct(
        ManageStatusCollection $manageStatusCollection
    ) {
        $this->manageStatusCollection = $manageStatusCollection;
    }

    /**
     * @param Payment\Card $card
     */
    public function start(
        Payment\Card $card
    ) {
        $this->manageStatusCollection->insertOne([
            '_id' => $card->getId(),
            'proved' => false
        ]);
    }
}