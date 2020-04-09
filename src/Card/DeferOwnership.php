<?php

namespace Yosmy\Payment\Card;

use Yosmy;

/**
 * @di\service()
 */
class DeferOwnership
{
    /**
     * @var Ownership\Defer\CheckChargesByCard
     */
    private $checkChargesByCard;

    /**
     * @var Ownership\Defer\CheckChargesByUser
     */
    private $checkChargesByUser;

    /**
     * @param Ownership\Defer\CheckChargesByCard $checkChargesByCard
     * @param Ownership\Defer\CheckChargesByUser $checkChargesByUser
     */
    public function __construct(
        Ownership\Defer\CheckChargesByCard $checkChargesByCard,
        Ownership\Defer\CheckChargesByUser $checkChargesByUser
    ) {
        $this->checkChargesByCard = $checkChargesByCard;
        $this->checkChargesByUser = $checkChargesByUser;
    }

    /**
     * @param string $card
     * @param int    $amount
     *
     * @throws NotDeferrableOwnershipException
     */
    public function defer(
        string $card,
        int $amount
    ) {
        try {
            $this->checkChargesByUser->check($card, $amount);
        } catch (NotDeferrableOwnershipException $e) {
            throw $e;
        }

        try {
            $this->checkChargesByCard->check($card, $amount);
        } catch (NotDeferrableOwnershipException $e) {
            throw $e;
        }
    }
}