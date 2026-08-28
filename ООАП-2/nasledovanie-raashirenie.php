<?php


class Apple
{
    //родительский класс, обычное яблоко
    protected int $growLimit = 10;
    protected int $grow = 0;

    public function grow()
    {
        if ($this->grow < $this->growLimit) {
            $this->grow++;
            var_dump('apple grow');
        } else {
            var_dump('apple already grown');
        }
    }

    //одинаковый для обоих классов метод, которые не расширяется в наследнике
    public function getInfo()
    {
        var_dump("grow limit is {$this->growLimit}, current grow is {$this->grow}");
    }
}


class GMOApple extends Apple
{
    //наследник, яблоко, котороые можео генномодифицировать
    private bool $isGMOentered = false;

    //Расширение, метода у родителя нет
    public function GMOenter()
    {
        $this->growLimit++;
        $this->isGMOentered = true;
    }

    //Специализациф метод существует у родителя,
    //но его поведение уточняется, если в яблоке есть гмо, то оно растет дважды
    public function grow()
    {
        parent::grow();
        if ($this->isGMOentered) {
            parent::grow();
        }
    }
}



