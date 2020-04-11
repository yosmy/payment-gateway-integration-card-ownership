<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class CancelOwnership
{
    /**
     * @var Payment\Card\Ownership\DeleteStatus
     */
    private $deleteStatus;

    /**
     * @var Payment\Card\Ownership\CloseProcess
     */
    private $closeProcess;

    /**
     * @param Ownership\DeleteStatus $deleteStatus
     * @param Ownership\CloseProcess $closeProcess
     */
    public function __construct(
        Ownership\DeleteStatus $deleteStatus,
        Ownership\CloseProcess $closeProcess
    ) {
        $this->deleteStatus = $deleteStatus;
        $this->closeProcess = $closeProcess;
    }

    /**
     * @param Payment\Card $card
     */
    public function cancel(
        Payment\Card $card
    ) {
        $this->deleteStatus->delete(
            $card
        );

        $this->closeProcess->close(
            $card
        );
    }
}