<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Mongo;

/**
 * @di\service({
 *     private: true
 * })
 */
class CollectProcesses
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
     * @param int $ago
     *
     * @return Processes
     */
    public function collect(
        int $ago
    ): Processes {
        // 40 hours ago
        $expired = new Mongo\DateTime((time() - $ago) * 1000);

        $cursor = $this->manageProcessCollection->find([
            'date' => [
                '$lte' => $expired
            ],
        ]);

        return new Processes($cursor);
    }
}