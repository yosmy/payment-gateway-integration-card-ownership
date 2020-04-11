<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * @di\service()
 */
class CollectOwnerships
{
    /**
     * @var Payment\GatherCustomer
     */
    private $gatherCustomer;

    /**
     * @var Payment\CollectCards
     */
    private $collectCards;

    /**
     * @var PickOwnership
     */
    private $pickOwnership;

    /**
     * @param Payment\GatherCustomer $gatherCustomer
     * @param Payment\CollectCards $collectCards
     * @param PickOwnership        $pickOwnership
     */
    public function __construct(
        Payment\GatherCustomer $gatherCustomer,
        Payment\CollectCards $collectCards,
        PickOwnership $pickOwnership
    ) {
        $this->gatherCustomer = $gatherCustomer;
        $this->collectCards = $collectCards;
        $this->pickOwnership = $pickOwnership;
    }

    /**
     * @param string $user
     *
     * @return Ownership[]
     */
    public function collect(
        string $user
    ): array {
        $customer = $this->gatherCustomer->gather($user);

        /** @var Payment\Card[] $cards */
        $cards = $this->collectCards->collect(
            null,
            $customer->getUser(),
            null,
            false,
            null,
            null
        );

        $ownerships = [];

        foreach ($cards as $card) {
            try {
                $ownerships[] = $this->pickOwnership->pick(
                    $card
                );
            } catch (NonexistentOwnershipException $e) {
                continue;
            }
        }

        return $ownerships;
    }
}