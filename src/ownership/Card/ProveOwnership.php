<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * @di\service()
 */
class ProveOwnership
{
    /**
     * @var Payment\Card\Ownership\GatherProcess
     */
    private $gatherProcess;

    /**
     * @var Payment\Card\Ownership\FinishStatus
     */
    private $finishStatus;

    /**
     * @var Payment\Card\Ownership\FinishProcess
     */
    private $finishProcess;

    /**
     * @var AnalyzePreProveOwnership[]
     */
    private $analyzePreProveOwnershipServices;

    /**
     * @var AnalyzePostProveOwnershipSuccess[]
     */
    private $analyzePostProveOwnershipSuccessServices;

    /**
     * @var AnalyzePostProveOwnershipFail[]
     */
    private $analyzePostProveOwnershipFailServices;

    /**
     * @di\arguments({
     *     analyzePreProveOwnershipServices:         '#yosmy.payment.card.pre_prove_ownership',
     *     analyzePostProveOwnershipSuccessServices: '#yosmy.payment.card.post_prove_ownership_success',
     *     analyzePostProveOwnershipFailServices:    '#yosmy.payment.card.post_prove_ownership_fail'
     * })
     *
     * @param Payment\Card\Ownership\GatherProcess           $gatherProcess
     * @param Payment\Card\Ownership\FinishStatus            $finishStatus
     * @param Payment\Card\Ownership\FinishProcess           $finishProcess
     * @param AnalyzePreProveOwnership[]|null                $analyzePreProveOwnershipServices
     * @param AnalyzePostProveOwnershipSuccess[]|null        $analyzePostProveOwnershipSuccessServices
     * @param AnalyzePostProveOwnershipFail[]|null           $analyzePostProveOwnershipFailServices
     */
    public function __construct(
        Payment\Card\Ownership\GatherProcess $gatherProcess,
        Payment\Card\Ownership\FinishStatus $finishStatus,
        Payment\Card\Ownership\FinishProcess $finishProcess,
        ?array $analyzePreProveOwnershipServices,
        ?array $analyzePostProveOwnershipSuccessServices,
        ?array $analyzePostProveOwnershipFailServices
    ) {
        $this->gatherProcess = $gatherProcess;
        $this->finishStatus = $finishStatus;
        $this->finishProcess = $finishProcess;
        $this->analyzePreProveOwnershipServices = $analyzePreProveOwnershipServices;
        $this->analyzePostProveOwnershipSuccessServices = $analyzePostProveOwnershipSuccessServices;
        $this->analyzePostProveOwnershipFailServices = $analyzePostProveOwnershipFailServices;
    }

    /**
     * @param Payment\Card $card
     * @param int          $amount
     *
     * @return Ownership\Process
     *
     * @throws Payment\KnownException
     */
    public function prove(
        Payment\Card $card,
        int $amount
    ): Ownership\Process {
        foreach ($this->analyzePreProveOwnershipServices as $analyzePreProveOwnership) {
            try {
                $analyzePreProveOwnership->analyze(
                    $card,
                    $amount
                );
            } catch (Payment\KnownException $e) {
                foreach ($this->analyzePostProveOwnershipFailServices as $analyzePostProveOwnershipFail) {
                    $analyzePostProveOwnershipFail->analyze(
                        $card,
                        $amount,
                        $e
                    );
                }

                throw $e;
            }
        }

        // Needed for returning
        $process = $this->gatherProcess->gather(
            $card
        );

        $closed = $this->finishProcess->finish(
            $card,
            $amount
        );

        if (!$closed) {
            $e = new Payment\KnownException('La cantidad entrada es incorrecta');

            foreach ($this->analyzePostProveOwnershipFailServices as $analyzePostProveOwnershipFail) {
                $analyzePostProveOwnershipFail->analyze(
                    $card,
                    $amount,
                    $e
                );
            }

            throw $e;
        }

        $this->finishStatus->finish(
            $card
        );

        foreach ($this->analyzePostProveOwnershipSuccessServices as $analyzePostProveOwnershipSuccess) {
            $analyzePostProveOwnershipSuccess->analyze(
                $card,
                $amount
            );
        }

        return $process;
    }
}