<?php
//Наследование реализации

class ArrayList
{
    /** @var list<mixed> */
    protected array $items = [];

    public function append(mixed $x): void
    {
        $this->items[] = $x;
    }
    public function removeLast(): mixed
    {
        if ($this->items === []) throw new Exception('empty');
        return array_pop($this->items);
    }
    public function last(): mixed
    {
        return $this->items[array_key_last($this->items)] ?? null;
    }

    public function size(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }
}

class Stack extends ArrayList
{
    public function push(mixed $x): void { $this->append($x); }
    public function pop(): mixed { return $this->removeLast(); }
    public function peek(): mixed { return $this->last(); }
}


//Льготное наследование

class AppException extends RuntimeException
{
    public const CODE_VALIDATION = 400;
    public const CODE_NOT_FOUND  = 404;

    protected array $context = [];

    protected const CODE = 500;

    public function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message ?: static::defaultMessage(), static::CODE, $previous);
    }

    protected static function defaultMessage(): string
    {
        return 'application error';
    }

    public function context(): array
    {
        return $this->context;
    }


}