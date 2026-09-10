<?php

//полиморфизмый вызов
abstract class Animal
{
    abstract public function voice(): string;
}
class Dog extends Animal {
    public function voice(): string
    {
        return 'Woof';
    }
}
class Cat extends Animal
{
    public function voice(): string
    {
        return 'Meow';
    }
}


//ковариантного вызова метода нет, сужать можно только возвращаемое значение, а передаваемое нет
