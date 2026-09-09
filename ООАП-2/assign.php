<?php
abstract class General
{

    public const ASSIGN_OK   = 1; // типы совместимы
    public const ASSIGN_VOID = 2; // несовместимы

    protected static function voidValue(): null
    {
        return null;
    }

    final public static function assignmentAttempt(mixed &$target, mixed $source): int
    {
        if ($source instanceof static) {
            $target = $source;
            return self::ASSIGN_OK;
        }
        $target = static::voidValue();
        return self::ASSIGN_VOID;
    }
}

class Any extends General
{
}