<?php

class BankAccount
{
    private float $balance;

    public function __construct(float $initialBalance)
    {
        $this->balance = $initialBalance;
    }

    public function deposit(float $amount): void
    {
        $this->balance += $amount;
    }

    public function withdraw(float $amount): void
    {
        $this->balance -= $amount;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }
}

class BankAccountTest
{
    public function run(): void
    {
        $account = new BankAccount(1000.0);
        echo 'Начальный баланс: ' . $account->getBalance() . PHP_EOL;

        $account->deposit(500.0);
        echo 'После пополнения на 500: ' . $account->getBalance() . PHP_EOL;

        $account->withdraw(200.0);
        echo 'После снятия 200: ' . $account->getBalance() . PHP_EOL;

        $account->deposit(-300.0);
        echo 'После пополнения на -300: ' . $account->getBalance() . PHP_EOL;

        $account->withdraw(-100.0);
        echo 'После снятия -100: ' . $account->getBalance() . PHP_EOL;

        $account->withdraw(2000.0);
        echo 'После снятия 2000: ' . $account->getBalance() . PHP_EOL;
    }
}
