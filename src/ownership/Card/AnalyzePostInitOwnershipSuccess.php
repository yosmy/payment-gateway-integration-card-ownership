<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

interface AnalyzePostInitOwnershipSuccess
{
    /**
     * @param Payment\Card $card
     */
    public function analyze(
        Payment\Card $card
    );
}