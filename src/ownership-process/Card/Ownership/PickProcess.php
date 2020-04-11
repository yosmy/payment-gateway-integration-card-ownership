<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class PickProcess
{
    /**
     * @var ManageProcessCollection
     */
    private $manageProcessCollection;

    /**
     * @param ManageProcessCollection $manageProcessCollection
     */
    public function __construct(
        ManageProcessCollection $manageProcessCollection
    ) {
        $this->manageProcessCollection = $manageProcessCollection;
    }

    /**
     * @param Payment\Card $card
     *
     * @return Process
     *                
     * @throws NonexistentProcessException
     */
    public function pick(
        Payment\Card $card
    ): Process {
        /** @var Process $process */
        $process = $this->manageProcessCollection->findOne([
            '_id' => $card->getId(),
        ]);

        if (!$process) {
            throw new NonexistentProcessException();
        }

        return $process;
    }
}