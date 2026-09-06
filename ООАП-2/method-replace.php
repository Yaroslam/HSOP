<?php


class A
{
    final public function cantUseInB()
    {
        var_dump('OK');
    }

    public function canUseInB()
    {
        var_dump('OK A');
    }
}


class B extends A
{
//    public function cantUseInB()
//    {
//        var_dump('error not compile');
//    }

    public function canUseInB()
    {
        var_dump('ok B');
    }
}