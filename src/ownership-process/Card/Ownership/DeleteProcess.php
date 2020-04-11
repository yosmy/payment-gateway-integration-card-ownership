<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class DeleteProcess
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
     */
    public function delete(
        Payment\Card $card
    ) {
        $this->manageProcessCollection->deleteOne([
            '_id' => $card->getId(),
        ]);
    }
}