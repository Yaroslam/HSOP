<?php
class BankAccount
{
    private float $balance;

    public function __construct(float $initialBalance)
    {
        if (!is_finite($initialBalance) || $initialBalance < 0) {
            throw new InvalidArgumentException(
                'Начальный баланс должен быть конечным числом не меньше нуля.'
            );
        }

        $this->balance = $initialBalance;
    }

    public function deposit(float $amount): void
    {
        $this->validateAmount($amount);

        $newBalance = $this->balance + $amount;

        if (!is_finite($newBalance)) {
            throw new OverflowException(
                'Пополнение приведёт к переполнению баланса.'
            );
        }

        $this->balance = $newBalance;
    }

    public function withdraw(float $amount): void
    {
        $this->validateAmount($amount);

        if ($amount > $this->balance) {
            throw new RuntimeException(
                'Недостаточно средств на счёте.'
            );
        }

        $this->balance -= $amount;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    private function validateAmount(float $amount): void
    {
        if (!is_finite($amount) || $amount <= 0) {
            throw new InvalidArgumentException(
                'Сумма операции должна быть конечным числом больше нуля.'
            );
        }
    }
}