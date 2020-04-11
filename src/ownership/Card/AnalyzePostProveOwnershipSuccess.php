<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

interface AnalyzePostProveOwnershipSuccess
{
    /**
     * @param Payment\Card $card
     * @param int          $amount
     */
    public function analyze(
        Payment\Card $card,
        int $amount
    );
}