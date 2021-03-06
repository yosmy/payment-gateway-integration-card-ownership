<?php

namespace Yosmy\Payment\Card\Ownership\Process;

use Yosmy;

/**
 * @di\service({
 *     private: true
 * })
 */
class GenerateAmount
{
    /**
     * @param int   $from
     * @param int[] $denied
     * @param int   $to
     *
     * @return int
     */
    public function generate(
        int $from,
        array $denied,
        int $to
    ): int {
        do {
            $amount = mt_rand($from, $to);
        } while (in_array($amount, $denied));

        return $amount;
    }
}