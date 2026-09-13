<?php


//множественного наследования в PHP нет
//поэтому у нас две иерархии плетжный метод и направление
//которые объединятся в транзакцию
//в итоге транзакция характеризуется двум криетариеми, ни один из которых нельзя выделить как главный
//критерии комбинируются

abstract class PaymentMethod
{
    abstract public function feeRate(): float;

    abstract public function depositLimit(): int;

    abstract public function withdrawalDays(): int;

    public function supportsWithdrawal(): bool
    {
        return true;
    }

    public function name(): string
    {
        return static::class;
    }
}

final class Card extends PaymentMethod
{
    public function feeRate(): float
    {
        return 0.025;
    }

    public function depositLimit(): int
    {
        return 300000;
    }

    public function withdrawalDays(): int
    {
        return 3;
    }
}

final class Sbp extends PaymentMethod
{
    public function feeRate(): float
    {
        return 0.007;
    }

    public function depositLimit(): int
    {
        return 100000;
    }

    public function withdrawalDays(): int
    {
        return 0;
    }
}

final class Crypto extends PaymentMethod
{
    public function feeRate(): float
    {
        return 0.01;
    }

    public function depositLimit(): int
    {
        return 1_000_000;
    }

    public function withdrawalDays(): int
    {
        return 1;
    }

    public function supportsWithdrawal(): bool
    {
        return false;
    }
}

abstract class Direction
{
    abstract public function validate(PaymentMethod $m, int $amount): ?string;

    abstract public function fee(PaymentMethod $m, int $amount): int;

    abstract public function balanceDelta(int $amount): int;

    abstract public function etaDays(PaymentMethod $m): int;
}

final class Deposit extends Direction
{
    public function validate(PaymentMethod $m, int $amount): ?string
    {
        return $amount > $m->depositLimit() ? "over limit {$m->depositLimit()}" : null;
    }

    public function fee(PaymentMethod $m, int $amount): int
    {
        return 0;
    }

    public function balanceDelta(int $amount): int
    {
        return +$amount;
    }

    public function etaDays(PaymentMethod $m): int
    {
        return 0;
    }
}

final class Withdrawal extends Direction
{
    public function validate(PaymentMethod $m, int $amount): ?string
    {
        return $m->supportsWithdrawal() ? null : "{$m->name()} does not support withdrawal";
    }

    public function fee(PaymentMethod $m, int $amount): int
    {
        return (int)ceil($amount * $m->feeRate());
    }

    public function balanceDelta(int $amount): int
    {
        return -$amount;
    }

    public function etaDays(PaymentMethod $m): int
    {
        return $m->withdrawalDays();
    }
}

final class Transaction
{
    public function __construct(
        private readonly Direction $direction,
        private readonly PaymentMethod $method,
        private readonly int $amount,
    ) {
    }

    public function process(): string
    {
        if ($err = $this->direction->validate($this->method, $this->amount)) {
            return "rejected: $err";
        }

        return sprintf(
            'OK: balance %+d, fee %d, eta %d day(s)',
            $this->direction->balanceDelta($this->amount),
            $this->direction->fee($this->method, $this->amount),
            $this->direction->etaDays($this->method),
        );
    }
}
