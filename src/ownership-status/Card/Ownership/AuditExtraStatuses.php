<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Payment;
use Traversable;

/**
 * @di\service()
 */
class AuditExtraStatuses
{
    /**
     * @var ManageStatusCollection
     */
    private $manageStatusCollection;

    /**
     * @var Payment\ManageCardCollection
     */
    private $manageCardCollection;

    /**
     * @param ManageStatusCollection $manageStatusCollection
     * @param Payment\ManageCardCollection $manageCardCollection
     */
    public function __construct(
        ManageStatusCollection $manageStatusCollection,
        Payment\ManageCardCollection $manageCardCollection
    ) {
        $this->manageStatusCollection = $manageStatusCollection;
        $this->manageCardCollection = $manageCardCollection;
    }

    /**
     * @return Traversable
     */
    public function audit(): Traversable
    {
        return $this->manageStatusCollection->aggregate(
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