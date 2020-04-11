<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

interface AnalyzePostProveOwnershipFail
{
    /**
     * @param Payment\Card      $card
     * @param int               $amount
     * @param Payment\Exception $exception
     */
    public function analyze(
        Payment\Card $card,
        int $amount,
        Payment\Exception $exception
    );
}