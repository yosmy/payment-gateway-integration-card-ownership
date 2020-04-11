<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

interface AnalyzePostInitOwnershipFail
{
    /**
     * @param Payment\Card      $card
     * @param Payment\Exception $exception
     *
     * @throws Payment\Exception
     */
    public function analyze(
        Payment\Card $card,
        Payment\Exception $exception
    );
}