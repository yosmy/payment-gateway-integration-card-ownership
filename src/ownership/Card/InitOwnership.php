<?php

namespace Yosmy\Payment\Card;

use Yosmy;
use Yosmy\Payment;

/**
 * @di\service()
 */
class InitOwnership
{
    /**
     * @var Payment\Card\Ownership\PickStatus
     */
    private $pickStatus;

    /**
     * @var Payment\Card\Ownership\StartProcess
     */
    private $startProcess;

    /**
     * @var Payment\Card\Ownership\StartStatus
     */
    private $startStatus;
    
    /**
     * @var AnalyzePostInitOwnershipSuccess[]
     */
    private $analyzePostInitOwnershipSuccessServices;

    /**
     * @var AnalyzePostInitOwnershipFail[]
     */
    private $analyzePostInitOwnershipFailServices;

    /**
     * @di\arguments({
     *     analyzePostInitOwnershipSuccessServices: '#yosmy.payment.card.post_init_ownership_success',
     *     analyzePostInitOwnershipFailServices:    '#yosmy.payment.card.post_init_ownership_fail'
     * })
     *
     * @param Payment\Card\Ownership\PickStatus   $pickStatus
     * @param Payment\Card\Ownership\StartProcess $startProcess
     * @param Payment\Card\Ownership\StartStatus  $startStatus
     * @param AnalyzePostInitOwnershipSuccess[]   $analyzePostInitOwnershipSuccessServices
     * @param AnalyzePostInitOwnershipFail[]      $analyzePostInitOwnershipFailServices
     */
    public function __construct(
        Payment\Card\Ownership\PickStatus $pickStatus,
        Payment\Card\Ownership\StartProcess $startProcess,
        Payment\Card\Ownership\StartStatus $startStatus,
        array $analyzePostInitOwnershipSuccessServices,
        array $analyzePostInitOwnershipFailServices
    ) {
        $this->pickStatus = $pickStatus;
        $this->startProcess = $startProcess;
        $this->startStatus = $startStatus;
        $this->analyzePostInitOwnershipSuccessServices = $analyzePostInitOwnershipSuccessServices;
        $this->analyzePostInitOwnershipFailServices = $analyzePostInitOwnershipFailServices;
    }

    /**
     * @param Payment\Card $card
     * @param string       $description
     * @param string       $statement
     *
     * @throws Payment\Exception
     */
    public function init(
        Payment\Card $card,
        string $description,
        string $statement
    ) {
        try {
            $this->pickStatus->pick($card);

            return;
        } catch (Ownership\NonexistentStatusException $e) {
        }

        try {
            $this->startProcess->start(
                $card,
                $description,
                $statement
            );
        } catch (Payment\Exception $e) {
            foreach ($this->analyzePostInitOwnershipFailServices as $analyzePostInitOwnershipFail) {
                $analyzePostInitOwnershipFail->analyze(
                    $card,
                    $e
                );
            }

            throw $e;
        }

        $this->startStatus->start($card);

        foreach ($this->analyzePostInitOwnershipSuccessServices as $analyzePostInitOwnershipSuccess) {
            $analyzePostInitOwnershipSuccess->analyze(
                $card
            );
        }
    }
}