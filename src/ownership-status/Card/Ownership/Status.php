<?php

namespace Yosmy\Payment\Card\Ownership;

interface Status
{
    /**
     * @return string
     */
    public function getCard(): string;

    /**
     * @return bool
     */
    public function isProved(): bool;
}
