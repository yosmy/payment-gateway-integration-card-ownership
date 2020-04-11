<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class DeleteStatus
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
    public function delete(
        Payment\Card $card
    ) {
        $this->manageStatusCollection->deleteOne([
            '_id' => $card->getId(),
        ]);
    }
}