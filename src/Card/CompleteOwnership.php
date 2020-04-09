<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;
use Yosmy\ReportError;
use LogicException;

/**
 * @di\service()
 */
class CompleteOwnership
{
    /**
     * @var Payment\PickCard
     */
    private $pickCard;

    /**
     * @var Ownership\ManageProcessCollection
     */
    private $manageProcessCollection;

    /**
     * @var Ownership\ManageStatusCollection
     */
    private $manageStatusCollection;

    /**
     * @var Ownership\LogEvent
     */
    private $logEvent;

    /**
     * @var Payment\AnalyzeCard[]
     */
    private $processCardListeners;

    /**
     * @var ReportError
     */
    private $reportError;

    /**
     * @di\arguments({
     *     processCardListeners: '#yosmy.payment.card.complete_ownership.card_involved'
     * })
     *
     * @param Payment\PickCard                    $pickCard
     * @param Ownership\ManageProcessCollection   $manageProcessCollection
     * @param Ownership\ManageStatusCollection    $manageStatusCollection
     * @param Ownership\LogEvent                  $logEvent
     * @param Payment\AnalyzeCard[]               $processCardListeners
     * @param ReportError                         $reportError
     */
    public function __construct(
        Payment\PickCard $pickCard,
        Ownership\ManageProcessCollection $manageProcessCollection,
        Ownership\ManageStatusCollection $manageStatusCollection,
        Ownership\LogEvent $logEvent,
        array $processCardListeners,
        ReportError $reportError
    ) {
        $this->pickCard = $pickCard;
        $this->manageProcessCollection = $manageProcessCollection;
        $this->manageStatusCollection = $manageStatusCollection;
        $this->logEvent = $logEvent;
        $this->processCardListeners = $processCardListeners;
        $this->reportError = $reportError;
    }

    /**
     * @param string $user
     * @param string $card
     * @param string $amount1
     * @param string $amount2
     *
     * @return Ownership\Process
     *
     * @throws Ownership\ExceededAttemptsException
     * @throws Ownership\InvalidAmountException
     */
    public function complete(
        string $user,
        string $card,
        string $amount1,
        string $amount2
    ) {
        // Remove all but numbers
        $amount1 = preg_replace('/[^0-9]/', '', $amount1);
        $amount2 = preg_replace('/[^0-9]/', '', $amount2);

        $amount1 = (int) $amount1;
        $amount2 = (int) $amount2;

        try {
            $card = $this->pickCard->pick(
                $card,
                $user
            );
        } catch (Payment\NonexistentCardException $e) {
            throw new LogicException(null, null, $e);
        }

        /** @var Ownership\Process $process */
        $process = $this->manageProcessCollection->findOne([
            'user' => $user,
            'fingerprint' => $card->getFingerprint(),
        ]);

        if (!$process) {
            throw new LogicException();
        }

        if ($process->getAttempts() == 3) {
            $this->logEvent->log(
                Ownership\Event::TYPE_BANNED_COMPLETED,
                $user,
                $card->getFingerprint(),
                [
                    'amount1' => $amount1,
                    'amount2' => $amount2,
                ]
            );

            throw new Ownership\ExceededAttemptsException();
        }

        $this->manageProcessCollection->updateOne(
            [
                'user' => $user,
                'fingerprint' => $card->getFingerprint(),
            ],
            [
                '$inc' => [
                    'attempts' => 1
                ]
            ]
        );

        if (
            (
                $process->getAmount1() != $amount1
                || $process->getAmount2() != $amount2
            )
            && (
                $process->getAmount1() != $amount2
                || $process->getAmount2() != $amount1
            )
        ) {
            $this->logEvent->log(
                Ownership\Event::TYPE_FAILED_COMPLETED,
                $user,
                $card->getFingerprint(),
                [
                    'amount1' => $amount1,
                    'amount2' => $amount2,
                ]
            );

            throw new Ownership\InvalidAmountException();
        }

        $this->manageProcessCollection->deleteOne(
            [
                'user' => $user,
                'fingerprint' => $card->getFingerprint(),
            ]
        );

        $this->manageStatusCollection->updateOne(
            [
                'user' => $user,
                'fingerprint' => $card->getFingerprint(),
            ],
            [
                '$set' => [
                    'completed' => true
                ]
            ]
        );

        $this->logEvent->log(
            Ownership\Event::TYPE_SUCCESSED_COMPLETED,
            $user,
            $card->getFingerprint(),
            []
        );

        foreach ($this->processCardListeners as $processCard) {
            try {
                $processCard->analyze(
                    $card
                );
            } catch (Payment\Exception $e) {
                $this->reportError->report($e);
                
                continue;
            }
        }

        return $process;
    }
}