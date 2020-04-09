<?php

namespace Yosmy\Payment\Card\Ownership;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;

class Process extends BSONDocument
{
    /**
     * @param string $id
     * @param string $user
     * @param string $fingerprint
     * @param int    $attempts
     * @param int    $amount1
     * @param int    $amount2
     * @param string $charge1
     * @param string $charge2
     * @param int    $date
     */
    public function __construct(
        string $id,
        string $user,
        string $fingerprint,
        int $attempts,
        int $amount1,
        int $amount2,
        string $charge1,
        string $charge2,
        int $date
    ) {
        parent::__construct([
            'id' => $id,
            'user' => $user,
            'fingerprint' => $fingerprint,
            'attempts' => $attempts,
            'amount1' => $amount1,
            'amount2' => $amount2,
            'charge1' => $charge1,
            'charge2' => $charge2,
            'date' => $date,
        ]);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->offsetGet('id');
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->offsetGet('user');
    }

    /**
     * @return string
     */
    public function getFingerprint(): string
    {
        return $this->offsetGet('fingerprint');
    }

    /**
     * @return int
     */
    public function getAttempts(): int
    {
        return $this->offsetGet('attempts');
    }

    /**
     * @return string
     */
    public function getAmount1(): string
    {
        return $this->offsetGet('amount1');
    }

    /**
     * @return string
     */
    public function getAmount2(): string
    {
        return $this->offsetGet('amount2');
    }

    /**
     * @return string
     */
    public function getCharge1(): string
    {
        return $this->offsetGet('charge1');
    }

    /**
     * @return string
     */
    public function getCharge2(): string
    {
        return $this->offsetGet('charge2');
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->offsetGet('date');
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        $date = new UTCDateTime($this->date * 1000);

        $data = $this->getArrayCopy();

        $data['_id'] = $data['id'];

        unset($data['id']);

        $data['date'] = $date;

        return (object) $data;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $data['id'] = $data['_id'];
        unset($data['_id']);

        /** @var UTCDateTime $date */
        $date = $data['date'];
        $data['date'] = $date->toDateTime()->getTimestamp();

        parent::bsonUnserialize($data);
    }
}
