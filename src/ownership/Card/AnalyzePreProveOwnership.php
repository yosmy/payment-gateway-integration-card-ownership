<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

interface AnalyzePreProveOwnership
{
    /**
     * @param Payment\Card $card
     * @param int          $amount
     *
     * @throws Payment\KnownException $exception
     */
    public function analyze(
        Payment\Card $card,
        int $amount
    );
}