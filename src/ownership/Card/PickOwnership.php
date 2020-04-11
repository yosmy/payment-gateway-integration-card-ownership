<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * @di\service()
 */
class PickOwnership
{
    /**
     * @var Ownership\PickStatus
     */
    private $pickStatus;

    /**
     * @param Ownership\PickStatus $pickStatus
     */
    public function __construct(
        Ownership\PickStatus $pickStatus
    ) {
        $this->pickStatus = $pickStatus;
    }

    /**
     * @param Payment\Card $card
     *
     * @return Ownership
     *
     * @throws NonexistentOwnershipException
     */
    public function pick(
        Payment\Card $card
    ): Ownership {
        try {
            $status = $this->pickStatus->pick($card);
        } catch (Ownership\NonexistentStatusException $e) {
            throw new NonexistentOwnershipException();
        }

        return new Ownership(
            $card->getId(),
            $status->isProved()
        );
    }
}