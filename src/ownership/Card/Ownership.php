<?php

namespace Yosmy\Payment\Card;

use JsonSerializable;

class Ownership implements JsonSerializable
{
    /**
     * @var string
     */
    private $card;

    /**
     * @var bool
     */
    private $proved;

    /**
     * @param string $card
     * @param bool   $proved
     */
    public function __construct(
        string $card,
        bool $proved
    ) {
        $this->card = $card;
        $this->proved = $proved;
    }

    /**
     * @return string
     */
    public function getCard(): string
    {
        return $this->card;
    }

    /**
     * @return bool
     */
    public function isProved(): bool
    {
        return $this->proved;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'card' => $this->card,
            'proved' => $this->proved,
        ];
    }
}
