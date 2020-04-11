<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class PickStatus
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
     *
     * @return Status
     *
     * @throws NonexistentStatusException
     */
    public function pick(
        Payment\Card $card
    ): Status {
        /** @var Status $status */
        $status = $this->manageStatusCollection->findOne([
            '_id' => $card->getId(),
        ]);

        if (!$status) {
            throw new NonexistentStatusException();
        }

        return $status;
    }
}