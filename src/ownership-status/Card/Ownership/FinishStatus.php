<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class FinishStatus
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
    public function finish(
        Payment\Card $card
    ) {
        $this->manageStatusCollection->updateOne(
            [
                '_id' => $card->getId(),
            ],
            [
                '$set' => [
                    'proved' => true
                ]
            ]
        );
    }
}