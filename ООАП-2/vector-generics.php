<?php

//Дженериков и перегузки операторов в PHP нет, поэтому имеется костыль в виде интерфейса Addable
abstract class General
{
    public function print(): string { return print_r($this, true); }
}
class Any extends General {}

/** Сложение однотипных объектов. Возвращает новый объект или null. */
interface Addable
{
    public function add(Addable $other): ?Addable;
}

final class IntNum extends Any implements Addable
{
    public function __construct(public readonly int $value) {}

    public function add(Addable $other): ?Addable
    {
        return $other instanceof self ? new self($this->value + $other->value) : null;
    }

    public function print(): string { return (string) $this->value; }
}

/**
 * @template T of Addable&General
 */
class Vector extends Any implements Addable
{
    /** @var list<T> */
    private readonly array $items;

    /**
     * @param class-string<T> $type
     * @param list<T>         $items
     */
    public function __construct(public readonly string $type, array $items = [])
    {
        if (!is_subclass_of($type, General::class) || !is_subclass_of($type, Addable::class)) {
            throw new InvalidArgumentException("$type должен наследовать General и реализовывать Addable");
        }

        $this->items = array_values($items);
    }

    public function length(): int { return count($this->items); }

    public function add(Addable $other): ?Addable
    {
        if (!$other instanceof self
            || $other->type !== $this->type
            || $other->length() !== $this->length()) {
            return null;
        }
        $sum = [];
        foreach ($this->items as $i => $item) {
            $s = $item->add($other->items[$i]);
            if ($s === null) {
                return null;
            }
            $sum[] = $s;
        }
        return new static($this->type, $sum);
    }

    public function print(): string
    {
        return '[' . implode(', ', array_map(fn(General $x) => $x->print(), $this->items)) . ']';
    }
}