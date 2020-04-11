<?php

namespace Yosmy\Payment\Card\Ownership\Process\Amount;

use JsonSerializable;

class Foreign implements JsonSerializable
{
    /**
     * @var string
     */
    private $currency;

    /**
     * @var int
     */
    private $from;

    /**
     * @var int
     */
    private $to;

    /**
     * @param string $currency
     * @param int    $from
     * @param int    $to
     */
    public function __construct(
        string $currency,
        int $from,
        int $to
    ) {
        $this->currency = $currency;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return int
     */
    public function getFrom(): int
    {
        return $this->from;
    }

    /**
     * @return int
     */
    public function getTo(): int
    {
        return $this->to;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'currency' => $this->getCurrency(),
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
        ];
    }
}
