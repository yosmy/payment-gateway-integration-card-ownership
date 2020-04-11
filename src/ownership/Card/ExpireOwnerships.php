<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * @di\service()
 */
class ExpireOwnerships
{
    /**
     * @var Ownership\CollectProcesses
     */
    private $collectProcesses;

    /**
     * @var Payment\GatherCard
     */
    private $gatherCard;

    /**
     * @var CancelOwnership
     */
    private $cancelOwnership;

    /**
     * @var AnalyzePostExpireOwnershipSuccess[]
     */
    private $analyzePostExpireOwnershipSuccessServices;
    
    /**
     * @di\arguments({
     *     analyzePostExpireOwnershipSuccessServices: '#yosmy.payment.card.post_expire_ownership_success'
     * })
     * 
     * @param Ownership\CollectProcesses               $collectProcesses
     * @param Payment\GatherCard                       $gatherCard
     * @param CancelOwnership                          $cancelOwnership
     * @param AnalyzePostExpireOwnershipSuccess[]|null $analyzePostExpireOwnershipSuccessServices
     */
    public function __construct(
        Ownership\CollectProcesses $collectProcesses,
        Payment\GatherCard $gatherCard,
        CancelOwnership $cancelOwnership,
        ?array $analyzePostExpireOwnershipSuccessServices
    ) {
        $this->collectProcesses = $collectProcesses;
        $this->gatherCard = $gatherCard;
        $this->cancelOwnership = $cancelOwnership;
        $this->analyzePostExpireOwnershipSuccessServices = $analyzePostExpireOwnershipSuccessServices;
    }

    /**
     */
    public function expire()
    {
        /** @var Payment\Card\Ownership\Process[] $processes */
        $processes = $this->collectProcesses->collect(3600 * 40);

        foreach ($processes as $process) {
            $card = $this->gatherCard->gather(
                $process->getCard(),
                null,
                null
            );

            $this->cancelOwnership->cancel(
                $card
            );

            foreach ($this->analyzePostExpireOwnershipSuccessServices as $analyzePostExpireOwnershipSuccess) {
                $analyzePostExpireOwnershipSuccess->analyze(
                    $card
                );
            }
        }
    }
}