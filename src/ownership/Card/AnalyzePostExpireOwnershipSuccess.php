<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

interface AnalyzePostExpireOwnershipSuccess
{
    /**
     * @param Payment\Card $card
     */
    public function analyze(
        Payment\Card $card
    );
}