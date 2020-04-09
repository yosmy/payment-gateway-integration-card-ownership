<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * @di\service()
 */
class CollectOwnerships
{
    /**
     * @var Payment\PickUser
     */
    private $pickUser;

    /**
     * @var Payment\CollectCards
     */
    private $collectCards;

    /**
     * @var PickOwnership
     */
    private $pickOwnership;

    /**
     * @param Payment\PickUser     $pickUser
     * @param Payment\CollectCards $collectCards
     * @param PickOwnership        $pickOwnership
     */
    public function __construct(
        Payment\PickUser $pickUser,
        Payment\CollectCards $collectCards,
        PickOwnership $pickOwnership
    ) {
        $this->pickUser = $pickUser;
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
    ) {
        try {
            $user = $this->pickUser->pick($user);
        } catch (Payment\NonexistentUserException $e) {
            return [];
        }

        $cards = $this->collectCards->collect(
            null,
            $user->getId(),
            null,
            false
        );

        $ownerships = [];

        foreach ($cards as $card) {
            try {
                $ownerships[] = $this->pickOwnership->pick(
                    $user->getId(),
                    $card->getId()
                );
            } catch (NonexistentOwnershipException $e) {
                continue;
            }
        }

        return $ownerships;
    }
}