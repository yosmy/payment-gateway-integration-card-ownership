<?php

namespace Yosmy\Payment\Card\Ownership\Process;

use Yosmy;
use LogicException;

/**
 * @di\service({
 *     private: true
 * })
 */
class ResolveAmount
{
    /**
     * @var Yosmy\Payment\Card\ResolveLookup
     */
    private $resolveLookup;

    /**
     * @var GenerateAmount
     */
    private $generateAmount;

    /**
     * @var Yosmy\Payment\GatherCustomer
     */
    private $gatherCustomer;

    /**
     * @var Yosmy\Country\ResolveCurrencies
     */
    private $resolveCurrencies;

    /**
     * @var Yosmy\Currency\Oer\ConvertAmount
     */
    private $convertAmount;

    /**
     * @param Yosmy\Payment\Card\ResolveLookup $resolveLookup
     * @param GenerateAmount                   $generateAmount
     * @param Yosmy\Payment\GatherCustomer     $gatherCustomer
     * @param Yosmy\Country\ResolveCurrencies  $resolveCurrencies
     * @param Yosmy\Currency\Oer\ConvertAmount $convertAmount
     */
    public function __construct(
        Yosmy\Payment\Card\ResolveLookup $resolveLookup,
        GenerateAmount $generateAmount,
        Yosmy\Payment\GatherCustomer $gatherCustomer,
        Yosmy\Country\ResolveCurrencies $resolveCurrencies,
        Yosmy\Currency\Oer\ConvertAmount $convertAmount
    ) {
        $this->resolveLookup = $resolveLookup;
        $this->generateAmount = $generateAmount;
        $this->gatherCustomer = $gatherCustomer;
        $this->resolveCurrencies = $resolveCurrencies;
        $this->convertAmount = $convertAmount;
    }

    /**
     * @param Yosmy\Payment\Card $card
     *
     * @return Amount
     */
    public function resolve(
        Yosmy\Payment\Card $card
    ): Amount {
        $amount = $this->generateAmount->generate(
            301,
            [
                400, 500, 600, 700, 800, 900,
                350, 450, 550, 650, 750, 850, 950,
                333, 444, 555, 666, 777, 888, 999,
                345, 456, 567, 678, 789,
            ],
            999
        );

        $digits = substr($card->getRaw()['number'], 0, 6);

        try {
            $lookup = $this->resolveLookup->resolve($digits);
        } catch (Yosmy\Payment\Card\UnresolvableLookupException $e) {
            // Then take user country

            $user = $this->gatherCustomer->gather($card->getUser());

            $lookup = new Yosmy\Payment\Card\Lookup(
                $user->getCountry()
            );
        }

        if ($lookup->getCountry() == 'US') {
            return new Amount(
                $amount,
                null
            );
        }

        try {
            $currency = $this->resolveCurrencies->resolve(
                $lookup->getCountry()
            )[0];
        } catch (Yosmy\Country\NotFoundException $e) {
            throw new LogicException(null, null, $e);
        }

        /* Some countries like SV will return USD as the currency */

        if ($currency->getCode() == 'USD') {
            return new Amount(
                $amount,
                null
            );
        }

        $from = (int) ($this->convertAmount->convert(
            $currency->getCode(),
            $amount / 100,
            2
        ) * 100);

        // Extends interval 50 cents more to include bank fee

        $to = (int) ($this->convertAmount->convert(
            $currency->getCode(),
            ($amount + 50) / 100,
            2
        ) * 100);

        return new Amount(
            $amount,
            new Amount\Foreign(
                $currency->getCode(),
                $from,
                $to
            )
        );
    }
}