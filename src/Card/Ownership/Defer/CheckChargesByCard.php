<?php

namespace Yosmy\Payment\Card\Ownership\Defer;

use Yosmy;
use Yosmy\Payment;
use LogicException;
use Yosmy\Payment\Card\NotDeferrableOwnershipException;

/**
 * @di\service()
 */
class CheckChargesByCard
{
    /**
     * @var Payment\PickCard
     */
    private $pickCard;

    /**
     * @var PickUser
     */
    private $pickDeferUser;

    /**
     * @var Payment\CollectCards
     */
    private $collectCards;

    /**
     * @var Payment\Charge\ComputeAmount
     */
    private $computeAmount;

    /**
     * @param string $card
     * @param int    $amount
     *
     * @throws NotDeferrableOwnershipException
     */
    public function check(
        string $card,
        int $amount
    ) {
        try {
            $card = $this->pickCard->pick(
                $card,
                null
            );
        } catch (Payment\NonexistentCardException $e) {
            throw new LogicException(null, null, $e);
        }

        try {
            $user = $this->pickDeferUser->pick(
                $card->getUser()
            );
        } catch (NonexistentUserException $e) {
            throw new LogicException(null, null, $e);
        }

        $cardsWithSameFingerprint = $this->collectCards->collect(
            null,
            null,
            $card->getFingerprint(),
            true
        );

        $cards = [];

        foreach ($cardsWithSameFingerprint as $cardWithSameFingerprint) {
            $cards[] = $cardWithSameFingerprint->getId();
        }

        $from = strtotime(sprintf(
            '%s -%s',
            date('Y-m-d H:i:s'),
            $user->getPeriod()
        ));

        $total = $this->computeAmount->execute(
            null,
            $cards,
            $from,
            null
        );

        if ($total + $amount > $user->getAmount()) {
            throw new NotDeferrableOwnershipException();
        }
    }
}