<?php

namespace App\Entity;

use App\Entity\ValueObject\Currency;
use App\Entity\ValueObject\TransactionType;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class Transaction extends Entity
{
    public function __construct(
        protected CarbonInterface $date,
        protected User $user,
        protected TransactionType $type,
        protected float $amount,
        protected Currency $currency,
        protected ?int $id = null
    ) {
    }

    public function getDate(): CarbonInterface
    {
        return $this->date;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public static function of(
        string $date,
        User $user,
        string $type,
        float $amount,
        string $currencyCode
    ): self {
        return new self(
            Carbon::parse($date),
            $user,
            TransactionType::from($type),
            $amount,
            Currency::from($currencyCode)
        );
    }
}
