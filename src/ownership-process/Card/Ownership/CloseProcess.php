<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class CloseProcess
{
    /**
     * @var PickProcess
     */
    private $pickProcess;

    /**
     * @var Payment\GatherCharge
     */
    private $gatherCharge;

    /**
     * @var Payment\RefundCharge
     */
    private $refundCharge;

    /**
     * @var DeleteProcess
     */
    private $deleteProcess;

    /**
     * @param PickProcess        $pickProcess
     * @param Payment\GatherCharge $gatherCharge
     * @param Payment\RefundCharge $refundCharge
     * @param DeleteProcess        $deleteProcess
     */
    public function __construct(
        PickProcess $pickProcess,
        Payment\GatherCharge $gatherCharge,
        Payment\RefundCharge $refundCharge,
        DeleteProcess $deleteProcess
    ) {
        $this->pickProcess = $pickProcess;
        $this->gatherCharge = $gatherCharge;
        $this->refundCharge = $refundCharge;
        $this->deleteProcess = $deleteProcess;
    }

    /**
     * @param Payment\Card $card
     *
     * @throws NonexistentProcessException
     */
    public function close(
        Payment\Card $card
    ) {
        try {
            $process = $this->pickProcess->pick($card);
        } catch (NonexistentProcessException $e) {
            throw $e;
        }

        $charge = $this->gatherCharge->gather(
            $process->getCharge(),
            null
        );

        $this->refundCharge->refund(
            $charge,
            null
        );

        $this->deleteProcess->delete($card);
    }
}