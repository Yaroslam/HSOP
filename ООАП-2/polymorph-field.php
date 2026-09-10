<?php

abstract class Engine
{
    abstract public function horsePower(): int;
    abstract public function maxSpeed(): int;
}

class V8 extends Engine
{
    public function horsePower(): int
    {
        return 100;
    }

    public function maxSpeed(): int
    {
        return 220;
    }
}


class V4 extends Engine
{
    public function horsePower(): int
    {
        return 50;
    }

    public function maxSpeed(): int
    {
        return 100;
    }
}


class Car
{
    private int $speed = 0 {
        get {
            return $this->speed;
        }
    }

    public function __construct(private readonly Engine $engine)
    {}

    public function tapGas(): void
    {
        $this->speed = $this->speed + $this->engine->maxSpeed() / 10 >= $this->engine->maxSpeed() ?
        $this->engine->maxSpeed() / 10 : $this->engine->maxSpeed();
    }
}