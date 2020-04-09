<?php

namespace Yosmy\Payment\Card;

use MongoDB\BSON\UTCDateTime;
use Yosmy;

/**
 * @di\service()
 */
class VoidExpiredOwnerships
{
    /**
     * @var Ownership\ManageProcessCollection
     */
    private $manageProcessCollection;

    /**
     * @var Ownership\ManageStatusCollection
     */
    private $manageStatusCollection;

    /**
     * @var Yosmy\Payment\VoidCharge
     */
    private $voidCharge;

    /**
     * @var Ownership\LogEvent
     */
    private $logEvent;

    /**
     * @param Ownership\ManageProcessCollection $manageProcessCollection
     * @param Ownership\ManageStatusCollection  $manageStatusCollection
     * @param Yosmy\Payment\VoidCharge        $voidCharge
     * @param Ownership\LogEvent                $logEvent
     */
    public function __construct(
        Ownership\ManageProcessCollection $manageProcessCollection,
        Ownership\ManageStatusCollection $manageStatusCollection,
        Yosmy\Payment\VoidCharge $voidCharge,
        Ownership\LogEvent $logEvent
    ) {
        $this->manageProcessCollection = $manageProcessCollection;
        $this->manageStatusCollection = $manageStatusCollection;
        $this->voidCharge = $voidCharge;
        $this->logEvent = $logEvent;
    }

    /**
     */
    public function void()
    {
        // 40 hours ago
        $expired = new UTCDateTime((time() - 3600 * 40) * 1000);

        /** @var Ownership\Process[] $processes */
        $processes = $this->manageProcessCollection->find([
            'date' => [
                '$lte' => $expired
            ],
        ]);

        foreach ($processes as $process) {
            $this->voidCharge->void(
                $process->getCharge1()
            );

            $this->voidCharge->void(
                $process->getCharge2()
            );

            $this->manageProcessCollection->deleteOne(
                [
                    'user' => $process->getUser(),
                    'fingerprint' => $process->getFingerprint(),
                ]
            );

            $this->manageStatusCollection->deleteOne(
                [
                    'user' => $process->getUser(),
                    'fingerprint' => $process->getFingerprint(),
                ]
            );

            $this->logEvent->log(
                Ownership\Event::TYPE_EXPIRED_START,
                $process->getUser(),
                $process->getFingerprint(),
                [
                    'amount1' => $process->getAmount1(),
                    'amount2' => $process->getAmount2(),
                    'charge1' => $process->getCharge1(),
                    'charge2' => $process->getCharge2(),
                ]
            );
        }
    }
}