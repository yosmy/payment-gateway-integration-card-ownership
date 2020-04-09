<?php

namespace Yosmy\Payment\Card\Ownership;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use Yosmy\Log;

class Event extends BSONDocument implements Log\Event
{
    const TYPE_SUCCESSED_START = 'successed-start';
    const TYPE_FAILED_START = 'failed-start';
    const TYPE_EXPIRED_START = 'expired-start';
    const TYPE_SUCCESSED_COMPLETED = 'successed-completed';
    const TYPE_FAILED_COMPLETED = 'failed-completed';
    const TYPE_BANNED_COMPLETED = 'banned-completed';

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
     * @return array
     */
    public function getExtra(): array
    {
        return $this->offsetGet('extra');
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
