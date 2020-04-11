<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class FinishProcess
{
    /**
     * @var GatherProcess
     */
    private $gatherProcess;

    /**
     * @var CloseProcess
     */
    private $closeProcess;

    /**
     * @param GatherProcess $gatherProcess
     * @param CloseProcess  $closeProcess
     */
    public function __construct(
        GatherProcess $gatherProcess,
        CloseProcess $closeProcess
    ) {
        $this->gatherProcess = $gatherProcess;
        $this->closeProcess = $closeProcess;
    }

    /**
     * @param Payment\Card $card
     * @param int          $amount
     *
     * @return bool
     */
    public function finish(
        Payment\Card $card,
        int $amount
    ): bool {
        $process = $this->gatherProcess->gather($card);

        /* Usd */

        if ($amount == $process->getAmount()->getUsd()) {
            $this->closeProcess->close($card);

            return true;
        }

        // Patch for keyboards with not dot
        // Instead of 9.90, user will input 990, and system will receive 99000
        if ($amount / 100 == $process->getAmount()->getUsd()) {
            $this->closeProcess->close($card);

            return true;
        }

        /* Foreign */

        $foreign = $process->getAmount()->getForeign();

        if (!$foreign) {
            return false;
        }

        if (
            $amount >= $foreign->getFrom()
            && $amount <= $foreign->getTo()
        ) {
            $this->closeProcess->close($card);

            return true;
        }

        // Patch for keyboards with not dot
        // Instead of 9.90, user will input 990, and system will receive 99000
        if (
            $amount / 100 >= $foreign->getFrom()
            && $amount / 100 <= $foreign->getTo()
        ) {
            $this->closeProcess->close($card);

            return true;
        }

        return false;
    }
}