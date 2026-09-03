<?php

//ковариантность
class Animal {}

class Dog extends Animal {}

interface AnimalCage
{
    public function release(): Animal;
}

class DogCage implements AnimalCage
{
    public function __construct(
        public Dog $animal
    ) {}

    public function release(): Dog
    {
        return $this->animal;
    }
}

function releaseAnimal(AnimalCage $cage): Animal
{
    return $cage->release();
}

releaseAnimal(new DogCage(new Dog()));

//контравариантность

class Cat extends Animal {}

class DogFood
{
    function feed(Dog $dog): void
    {
        return;
    }
}

class AnimalFood extends DogFood
{
    function feed(Animal $animal): void //Animal шире чем Dog, ошибок не будет
    {
        return;
    }
}


class CatFood extends DogFood
{
    function feed(Cat $cat): void //на этой строчке интерпритатор упадет с ошибкой, так как Cat уже чем Animal
    {

    }
}