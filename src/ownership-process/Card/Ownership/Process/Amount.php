<?php

namespace Yosmy\Payment\Card\Ownership\Process;

use JsonSerializable;

class Amount implements JsonSerializable
{
    /**
     * @var int
     */
    private $usd;

    /**
     * @var Amount\Foreign
     */
    private $foreign;

    /**
     * @param int                 $usd
     * @param Amount\Foreign|null $foreign
     */
    public function __construct(
        int $usd,
        ?Amount\Foreign $foreign
    ) {
        $this->usd = $usd;
        $this->foreign = $foreign;
    }

    /**
     * @return int
     */
    public function getUsd(): int
    {
        return $this->usd;
    }

    /**
     * @return Amount\Foreign
     */
    public function getForeign(): ?Amount\Foreign
    {
        return $this->foreign;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $foreign = $this->getForeign();

        if ($foreign) {
            $foreign = $foreign->jsonSerialize();
        }

        return [
            'usd' => $this->getUsd(),
            'foreign' => $foreign,
        ];
    }
}
