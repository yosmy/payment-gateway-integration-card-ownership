<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;
use Yosmy\Mongo;

/**
 * @di\service({
 *     private: true
 * })
 */
class StartProcess
{
    /**
     * @var Process\ResolveAmount
     */
    private $resolveAmount;

    /**
     * @var Payment\ExecuteCharge
     */
    private $executeCharge;

    /**
     * @var ManageProcessCollection
     */
    private $manageProcessCollection;

    /**
     * @param Process\ResolveAmount   $resolveAmount
     * @param Payment\ExecuteCharge   $executeCharge
     * @param ManageProcessCollection $manageProcessCollection
     */
    public function __construct(
        Process\ResolveAmount $resolveAmount,
        Payment\ExecuteCharge $executeCharge,
        ManageProcessCollection $manageProcessCollection
    ) {
        $this->resolveAmount = $resolveAmount;
        $this->executeCharge = $executeCharge;
        $this->manageProcessCollection = $manageProcessCollection;
    }

    /**
     * @param Payment\Card $card
     * @param string       $description
     * @param string       $statement
     *
     * @throws Payment\Exception
     */
    public function start(
        Payment\Card $card,
        string $description,
        string $statement
    ) {
        $amount = $this->resolveAmount->resolve(
            $card
        );

        try {
            $charge = $this->executeCharge->execute(
                $card,
                $amount->getUsd(),
                $description,
                $statement
            );
        } catch (Payment\Exception $e) {
            throw $e;
        }

        $this->manageProcessCollection->insertOne([
            '_id' => $card->getId(),
            'amount' => [
                'usd' => $amount->getUsd(),
                'foreign' => $amount->getForeign()
                    ? [
                        'currency' => $amount->getForeign()->getCurrency(),
                        'from' => $amount->getForeign()->getFrom(),
                        'to' => $amount->getForeign()->getTo(),
                    ]
                    : null
            ],
            'charge' => $charge->getId(),
            'date' => new Mongo\DateTime($charge->getDate() * 1000)
        ]);
    }
}