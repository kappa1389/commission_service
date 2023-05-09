<?php

namespace Test\Builders;

use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\ValueObject\Currency;
use App\Entity\ValueObject\TransactionType;
use App\Entity\ValueObject\UserType;
use Carbon\Carbon;
use Test\Utils\ID;

class TransactionBuilder
{
    protected string $date;
    protected string $type;
    protected float $amount;
    protected string $currency;
    protected int $id;
    protected User $user;

    public function __construct()
    {
        $this->refresh();
    }

    public function refresh(): self
    {
        $this->date = '2023-05-01';
        $this->type = 'deposit';
        $this->amount = 100;
        $this->currency = 'EUR';
        unset($this->id);
        unset($this->user);

        return $this;
    }

    public function withDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function withType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function withCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function withAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function withId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function build(): Transaction
    {
        return new Transaction(
            Carbon::parse($this->date),
            $this->user ?? new User(UserType::PRIVATE, ID::generate()),
            TransactionType::from($this->type),
            $this->amount,
            Currency::from($this->currency),
            $this->id ?? ID::generate()
        );
    }
}
