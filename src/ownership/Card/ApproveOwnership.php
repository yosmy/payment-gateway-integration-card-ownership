<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * This class can be used to approve an existent ownership, but also for a card with no pending ownership
 *
 * @di\service()
 */
class ApproveOwnership
{
    /**
     * @var Ownership\StartStatus
     */
    private $startStatus;

    /**
     * @var Ownership\FinishStatus
     */
    private $finishStatus;

    /**
     * @var Payment\Card\Ownership\CloseProcess
     */
    private $closeProcess;

    /**
     * @var AnalyzePostApproveOwnershipSuccess[]
     */
    private $analyzePostApproveOwnershipSuccessServices;

    /**
     * @di\arguments({
     *     analyzePostApproveOwnershipSuccessServices: '#yosmy.payment.card.post_approve_ownership_success'
     * })
     *
     * @param Ownership\StartStatus                $startStatus
     * @param Ownership\FinishStatus               $finishStatus
     * @param Ownership\CloseProcess               $closeProcess
     * @param AnalyzePostApproveOwnershipSuccess[] $analyzePostApproveOwnershipSuccessServices
     */
    public function __construct(
        Ownership\StartStatus $startStatus,
        Ownership\FinishStatus $finishStatus,
        Ownership\CloseProcess $closeProcess,
        array $analyzePostApproveOwnershipSuccessServices
    ) {
        $this->startStatus = $startStatus;
        $this->finishStatus = $finishStatus;
        $this->closeProcess = $closeProcess;
        $this->analyzePostApproveOwnershipSuccessServices = $analyzePostApproveOwnershipSuccessServices;
    }

    /**
     * @param Payment\Card $card
     * @param string       $operator
     * @param string       $reason
     */
    public function approve(
        Payment\Card $card,
        string $operator,
        string $reason
    ) {
        try {
            $this->closeProcess->close(
                $card
            );
        } catch (Ownership\NonexistentProcessException $e) {
            $this->startStatus->start($card);
        }

        $this->finishStatus->finish($card);

        foreach ($this->analyzePostApproveOwnershipSuccessServices as $analyzePostApproveOwnershipSuccess) {
            $analyzePostApproveOwnershipSuccess->analyze(
                $card,
                $operator,
                $reason
            );
        }
    }
}