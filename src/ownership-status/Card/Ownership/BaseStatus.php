<?php

namespace Yosmy\Payment\Card\Ownership;

use Yosmy\Mongo;

class BaseStatus extends Mongo\Document implements Status
{
    /**
     * @param string $card
     * @param bool   $proved
     */
    public function __construct(
        string $card,
        bool $proved
    ) {
        parent::__construct([
            '_id' => $card,
            'proved' => $proved
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
     * @return bool
     */
    public function isProved(): bool
    {
        return $this->offsetGet('proved');
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
