<?php

namespace Yosmy\Payment\Card\Ownership\Defer;

use Yosmy;
use Yosmy\Payment;
use LogicException;
use Yosmy\Payment\Card\NotDeferrableOwnershipException;

/**
 * @di\service()
 */
class CheckChargesByUser
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
     * @var Payment\Charge\ComputeAmount
     */
    private $computeAmount;

    /**
     * @param Payment\PickCard             $pickCard
     * @param PickUser                     $pickDeferUser
     * @param Payment\Charge\ComputeAmount $computeAmount
     */
    public function __construct(
        Payment\PickCard $pickCard,
        PickUser $pickDeferUser,
        Payment\Charge\ComputeAmount $computeAmount
    ) {
        $this->pickCard = $pickCard;
        $this->pickDeferUser = $pickDeferUser;
        $this->computeAmount = $computeAmount;
    }

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

        $from = strtotime(sprintf(
            '%s -%s',
            date('Y-m-d H:i:s'),
            $user->getPeriod()
        ));

        $total = $this->computeAmount->execute(
            [$card->getUser()],
            null,
            $from,
            null
        );

        if ($total + $amount > $user->getAmount()) {
            throw new NotDeferrableOwnershipException();
        }
    }
}