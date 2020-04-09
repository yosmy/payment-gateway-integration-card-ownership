<?php

namespace Yosmy\Payment\Card\Ownership\Defer;

use MongoDB\Model\BSONDocument;

class User extends BSONDocument
{
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
    public function getPeriod(): string
    {
        return $this->offsetGet('period');
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->offsetGet('amount');
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $data['id'] = $data['_id'];
        unset($data['_id']);

        parent::bsonUnserialize($data);
    }
}
