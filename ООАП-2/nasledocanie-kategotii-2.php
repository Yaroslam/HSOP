<?php

//наследование вариаций

class Apatments
{
    public function getPrice(): int
    {
        return 100;
    }
}

class MoscowApartments extends Apatments
{
    private const int MOSCOW_MODIFIER = 10;

    public function getPrice(): int
    {
        return 100 * self::MOSCOW_MODIFIER;
    }
}


//Наследование конкретизацией
abstract class PaymentSystem
{
    abstract public function checkout(): mixed;
    abstract public function processing(): bool;
}

class Cryptomus extends PaymentSystem
{
    public function checkout(): mixed
    {
        return "https//crypotomus.com/pay";
    }

    public function processing(): bool
    {
        return true;
    }
}


//стуктурное наследование

class Jsonable implements JsonSerializable
{
    final public function jsonSerialize(): mixed
    {
        return $this;
    }
}

class SomeClass extends Jsonable
{
    //здесь какая-то логика
}