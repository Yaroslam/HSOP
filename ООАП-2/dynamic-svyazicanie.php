<?php

abstract class ProgrammingLanguage
{
    abstract public function helloWorld(): void;
}

class PHP extends ProgrammingLanguage
{
    public function helloWorld(): void
    {
        var_dump("var_dump(\"Hello world\")");
    }
}


class Python extends ProgrammingLanguage
{
    public function helloWorld(): void
    {
        var_dump("print(\"Hello world\")");
    }
}

function helloWorld(ProgrammingLanguage $language)
{
    //какой именно класс вызывает метод определяется в рантайме
    $language->helloWorld();
}

$python = new Python();
$php = new PHP();

helloWorld($python);
helloWorld($php);