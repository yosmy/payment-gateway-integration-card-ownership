<?php

namespace Yosmy\Payment\Card\Ownership;

interface Process
{
    /**
     * @return string
     */
    public function getCard(): string;

    /**
     * @return Process\Amount
     */
    public function getAmount(): Process\Amount;

    /**
     * @return string
     */
    public function getCharge(): string;

    /**
     * @return string
     */
    public function getDate(): string;
}
