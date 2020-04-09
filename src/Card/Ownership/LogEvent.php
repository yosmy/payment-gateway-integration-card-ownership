<?php

namespace Yosmy\Payment\Card\Ownership;

use MongoDB\BSON\UTCDateTime;
use Yosmy\Mongo\DuplicatedKeyException;
use LogicException;

/**
 * @di\service()
 */
class LogEvent
{
    /**
     * @var ManageEventCollection
     */
    private $manageCollection;

    /**
     * @param ManageEventCollection $manageCollection
     */
    public function __construct(
        ManageEventCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string $type
     * @param string $user
     * @param string $fingerprint
     * @param array $extra
     */
    public function log(
        string $type,
        string $user,
        string $fingerprint,
        array $extra
    )  {
        try {
            $this->manageCollection->insertOne([
                '_id' => uniqid(),
                'type' => $type,
                'user' => $user,
                'fingerprint' => $fingerprint,
                'extra' => $extra,
                'date' => new UTCDateTime(time() * 1000)
            ]);
        } catch (DuplicatedKeyException $e) {
            throw new LogicException(null, null, $e);
        }
    }
}
