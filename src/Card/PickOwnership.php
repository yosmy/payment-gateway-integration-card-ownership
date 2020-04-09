<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment\NonexistentCardException;
use Yosmy\Payment\PickCard;
use LogicException;

/**
 * @di\service()
 */
class PickOwnership
{
    /**
     * @var PickCard
     */
    private $pickCard;

    /**
     * @var Ownership\ManageStatusCollection
     */
    private $manageStatusCollection;

    /**
     * @param PickCard                         $pickCard
     * @param Ownership\ManageStatusCollection $manageStatusCollection
     */
    public function __construct(
        PickCard $pickCard,
        Ownership\ManageStatusCollection $manageStatusCollection
    ) {
        $this->pickCard = $pickCard;
        $this->manageStatusCollection = $manageStatusCollection;
    }

    /**
     * @param string $user
     * @param string $card
     *
     * @return Ownership
     *
     * @throws NonexistentOwnershipException
     */
    public function pick(
        string $user,
        string $card
    ) {
        try {
            $card = $this->pickCard->pick($card, null);
        } catch (NonexistentCardException $e) {
            throw new LogicException(null, null, $e);
        }

        /** @var Ownership\Status $status */
        $status = $this->manageStatusCollection->findOne([
            'user' => $user,
            'fingerprint' => $card->getFingerprint(),
        ]);

        if (!$status) {
            throw new NonexistentOwnershipException();
        }

        /** @var Ownership\Status $status */
        $status = $this->manageStatusCollection->findOne([
            'user' => $user,
            'fingerprint' => $card->getFingerprint(),
        ]);

        return new Ownership(
            $card->getFingerprint(),
            $status->isCompleted()
        );
    }
}