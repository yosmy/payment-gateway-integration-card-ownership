<?php

namespace Yosmy\Payment\Card;

use MongoDB\BSON\UTCDateTime;
use Yosmy;
use Yosmy\Payment;
use LogicException;

/**
 * @di\service()
 */
class StartOwnership
{
    /**
     * @var Payment\PickCard
     */
    private $pickCard;

    /**
     * @var Ownership\GenerateAmount
     */
    private $generateAmount;

    /**
     * @var Payment\ExecuteCharge
     */
    private $executeCharge;

    /**
     * @var Ownership\ManageProcessCollection
     */
    private $manageProcessCollection;

    /**
     * @var Ownership\ManageStatusCollection
     */
    private $manageStatusCollection;

    /**
     * @var Payment\VoidCharge
     */
    private $voidCharge;

    /**
     * @var Ownership\LogEvent
     */
    private $logEvent;

    /**
     * @param Payment\PickCard                  $pickCard
     * @param Ownership\GenerateAmount          $generateAmount
     * @param Payment\ExecuteCharge             $executeCharge
     * @param Ownership\ManageProcessCollection $manageProcessCollection
     * @param Ownership\ManageStatusCollection  $manageStatusCollection
     * @param Payment\VoidCharge                $voidCharge
     * @param Ownership\LogEvent                $logEvent
     */
    public function __construct(
        Payment\PickCard $pickCard,
        Ownership\GenerateAmount $generateAmount,
        Payment\ExecuteCharge $executeCharge,
        Ownership\ManageProcessCollection $manageProcessCollection,
        Ownership\ManageStatusCollection $manageStatusCollection,
        Payment\VoidCharge $voidCharge,
        Ownership\LogEvent $logEvent
    ) {
        $this->pickCard = $pickCard;
        $this->generateAmount = $generateAmount;
        $this->executeCharge = $executeCharge;
        $this->manageProcessCollection = $manageProcessCollection;
        $this->manageStatusCollection = $manageStatusCollection;
        $this->voidCharge = $voidCharge;
        $this->logEvent = $logEvent;
    }

    /**
     * @param string $user
     * @param string $card
     * @param string $description
     * @param string $statement
     *
     * @throws Payment\Exception
     */
    public function start(
        string $user,
        string $card,
        string $description,
        string $statement
    ) {
        try {
            $card = $this->pickCard->pick(
                $card,
                $user
            );
        } catch (Payment\NonexistentCardException $e) {
            throw new LogicException(null, null, $e);
        }

        /** @var Ownership\Status $status */
        $status = $this->manageStatusCollection->findOne([
            'user' => $user,
            'fingerprint' => $card->getFingerprint(),
        ]);

        if ($status) {
            // This happens when the card is deleted and re-added
            // and then the user starts the ownership mechanism again
            // We will reuse the existent ownership process and status
            return;
        }

        $amount1 = $this->generateAmount->generate(
            401,
            [444, 456],
            499
        );

        $amount2 = 1000 - $amount1;

        try {
            $charge1 = $this->executeCharge->execute(
                $card,
                $amount1,
                $description,
                $statement
            );
        } catch (Payment\Exception $e) {
            $this->logEvent->log(
                Ownership\Event::TYPE_FAILED_START,
                $user,
                $card->getFingerprint(),
                [
                    'message' => $e->getMessage()
                ]
            );

            throw $e;
        }

        try {
            $charge2 = $this->executeCharge->execute(
                $card,
                $amount2,
                $description,
                $statement
            );
        } catch (Payment\Exception $e) {
            $this->voidCharge->void($charge1->getId());

            $this->logEvent->log(
                Ownership\Event::TYPE_FAILED_START,
                $user,
                $card->getFingerprint(),
                [
                    'message' => $e->getMessage()
                ]
            );

            throw $e;
        }

        try {
            $this->manageStatusCollection->insertOne([
                '_id' => uniqid(),
                'user' => $user,
                'fingerprint' => $card->getFingerprint(),
                'completed' => false
            ]);
        } catch (Yosmy\Mongo\DuplicatedKeyException $e) {
            throw new LogicException(null, null, $e);
        }

        try {
            $this->manageProcessCollection->insertOne([
                '_id' => uniqid(),
                'user' => $user,
                'fingerprint' => $card->getFingerprint(),
                'attempts' => 0,
                'amount1' => $amount1,
                'amount2' => $amount2,
                'charge1' => $charge1->getId(),
                'charge2' => $charge2->getId(),
                'date' => new UTCDateTime($charge1->getDate() * 1000)
            ]);
        } catch (Yosmy\Mongo\DuplicatedKeyException $e) {
            throw new LogicException(null, null, $e);
        }

        $this->logEvent->log(
            Ownership\Event::TYPE_SUCCESSED_START,
            $user,
            $card->getFingerprint(),
            []
        );
    }
}