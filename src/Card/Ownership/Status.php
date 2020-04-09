<?php

namespace Yosmy\Payment\Card\Ownership;

use MongoDB\Model\BSONDocument;

class Status extends BSONDocument
{
    /**
     * @param string $id
     * @param string $user
     * @param string $fingerprint
     * @param bool   $completed
     */
    public function __construct(
        string $id,
        string $user,
        string $fingerprint,
        bool $completed
    ) {
        parent::__construct([
            'id' => $id,
            'user' => $user,
            'fingerprint' => $fingerprint,
            'completed' => $completed
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
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->offsetGet('completed');
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        $data = $this->getArrayCopy();

        $data['_id'] = $data['id'];

        unset($data['id']);

        return (object) $data;
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
