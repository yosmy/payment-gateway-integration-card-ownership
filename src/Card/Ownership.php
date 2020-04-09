<?php

namespace Yosmy\Payment\Card;

use JsonSerializable;

class Ownership implements JsonSerializable
{
    /**
     * @var string
     */
    private $fingerprint;

    /**
     * @var bool
     */
    private $completed;

    /**
     * @param string $fingerprint
     * @param bool   $completed
     */
    public function __construct(
        string $fingerprint,
        bool $completed
    ) {
        $this->fingerprint = $fingerprint;
        $this->completed = $completed;
    }

    /**
     * @return string
     */
    public function getFingerprint(): string
    {
        return $this->fingerprint;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'fingerprint' => $this->fingerprint,
            'completed' => $this->completed,
        ];
    }
}
