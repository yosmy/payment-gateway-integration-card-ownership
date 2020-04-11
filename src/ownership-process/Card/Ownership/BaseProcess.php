<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Mongo;
use Yosmy\Payment\Card\Ownership\Process\Amount\Foreign;

class BaseProcess extends Mongo\Document implements Process
{
    /**
     * @param string $card
     * @param int    $amount
     * @param string $charge
     * @param int    $date
     */
    public function __construct(
        string $card,
        int $amount,
        string $charge,
        int $date
    ) {
        parent::__construct([
            '_id' => $card,
            'amount' => $amount,
            'charge' => $charge,
            'date' => $date,
        ]);
    }

    /**
     * @return string
     */
    public function getCard(): string
    {
        return $this->offsetGet('_id');
    }

    /**
     * @return Process\Amount
     */
    public function getAmount(): Process\Amount
    {
        return $this->offsetGet('amount');
    }

    /**
     * @return string
     */
    public function getCharge(): string
    {
        return $this->offsetGet('charge');
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->offsetGet('date');
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $data['amount'] = new Process\Amount(
            $data['amount']->usd,
            $data['amount']->foreign
                ? new Foreign(
                $data['amount']->foreign->currency,
                $data['amount']->foreign->from,
                $data['amount']->foreign->to
            )
                : null
        );

        /** @var Mongo\DateTime $date */
        $date = $data['date'];
        $data['date'] = $date->toDateTime()->getTimestamp();

        parent::bsonUnserialize($data);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): object
    {
        $data = parent::jsonSerialize();

        $data->card = $data->_id;

        unset($data->_id);

        return $data;
    }
}
