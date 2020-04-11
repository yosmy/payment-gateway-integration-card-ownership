<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;
use Traversable;

/**
 * @di\service()
 */
class AuditExtraProcesses
{
    /**
     * @var ManageProcessCollection
     */
    private $manageProcessCollection;

    /**
     * @var Payment\ManageCardCollection
     */
    private $manageCardCollection;

    /**
     * @param ManageProcessCollection      $manageProcessCollection
     * @param Payment\ManageCardCollection $manageCardCollection
     */
    public function __construct(
        ManageProcessCollection $manageProcessCollection,
        Payment\ManageCardCollection $manageCardCollection
    ) {
        $this->manageProcessCollection = $manageProcessCollection;
        $this->manageCardCollection = $manageCardCollection;
    }

    /**
     * @return Traversable
     */
    public function audit(): Traversable
    {
        return $this->manageProcessCollection->aggregate(
            [
                [
                    '$lookup' => [
                        'localField' => '_id',
                        'from' => $this->manageCardCollection->getName(),
                        'as' => 'cards',
                        'foreignField' => '_id',
                    ]
                ],
                [
                    '$match' => [
                        'cards._id' => [
                            '$exists' => false
                        ]
                    ],
                ]
            ]
        );
    }
}