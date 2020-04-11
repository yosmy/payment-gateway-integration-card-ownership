<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

interface AnalyzePostApproveOwnershipSuccess
{
    /**
     * @param Payment\Card $card
     * @param string       $operator
     * @param string       $reason
     */
    public function analyze(
        Payment\Card $card,
        string $operator,
        string $reason
    );
}