<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class GatherProcess
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
     */
    public function gather(
        Payment\Card $card
    ): Process {
        /** @var Process $process */
        $process = $this->manageProcessCollection->findOne([
            '_id' => $card->getId(),
        ]);

        return $process;
    }
}