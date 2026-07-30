<?php

/**
 * @template T
 */
class BoundedStack
{
    public const int POP_NIL = 0; //pop() не вызывался
    public const int POP_OK = 1; //последний вызов pop() завершился успешно
    public const int POP_ERR = 2; //последний вызов pop() завершился неудачно

    public const int PEEK_NIL = 0; //peek() не вызывался
    public const int PEEK_OK = 1; //последний вызов peek() завершился успешно
    public const int PEEK_ERR = 2; //последний вызов peek() завершился неудачно

    public const int PUSH_NIL = 0; //push() не вызывался
    public const int PUSH_OK = 1; //последний вызов push() завершился успешно
    public const int PUSH_ERR = 2; //последний вызов push() завершился неудачно

    private const int DEFAULT_SIZE = 32; //размер стека по умолчанию

    /** @var list<T> хранилище стека */
    private array $stack = [];

    /** @var self::POP_NIL|self::POP_OK|self::POP_ERR статус команды pop() */
    private int $statusPop = self::POP_NIL;

    /** @var self::PEEK_NIL|self::PEEK_OK|self::PEEK_ERR статус запроса peek() */
    private int $statusPeek = self::PEEK_NIL;

    /** @var self::PUSH_NIL|self::PUSH_OK|self::PUSH_ERR статус команды push() */
    private int $statusPush = self::PUSH_NIL;

    /** @var positive-int максимальный размер стека */
    private int $size;

    /**
     * @param int|null $size максимальный размер стека null или неположительное значение - размер по умолчанию
     */
    public function __construct(?int $size = null)
    {
        $this->size = (is_null($size) || $size <= 0) ? self::DEFAULT_SIZE : $size;
    }

    //команды
    /**
     * Добавляет элемент в стек
     * Предусловие: стек не переполнен
     * Постусловие: в стек добавлено новое значение
     * @param T $value
     */
    public function push(mixed $value): void
    {
        if ($this->size() < $this->size) {
            $this->stack[] = $value;
            $this->statusPush = self::PUSH_OK;
        } else {
            $this->statusPush = self::PUSH_ERR;
        }
    }

    /**
     * Удаляет верхний элемент стека
     * Предусловие: стек не пуст
     * Постусловие: из стека удален верхний элемент
     */
    public function pop(): void
    {
        if ($this->size() > 0) {
            array_pop($this->stack);
            $this->statusPop = self::POP_OK;
        } else {
            $this->statusPop = self::POP_ERR;
        }
    }

    /**
     * Очищает стек
     * Постусловие: удаляет из стека все значения, обнуляет статусы операций
     */
    public function clear(): void
    {
        $this->stack = [];
        $this->statusPop = self::POP_NIL;
        $this->statusPeek = self::PEEK_NIL;
        $this->statusPush = self::PUSH_NIL;
    }

    //запросы

    /**
     * Возвращает текущую длину стека
     *
     * @return int<0, max>
     */
    public function size(): int
    {
        return count($this->stack);
    }

    /**
     * Возвращает верхний элемент стека
     * Предусловие: стек не пуст
     * @return T|null верхний элемент, null если стек пуст
     */
    public function peek(): mixed
    {
        if ($this->size() > 0) {
            $this->statusPeek = self::PEEK_OK;
            return $this->stack[count($this->stack) - 1];
        } else {
            $this->statusPeek = self::PEEK_ERR;
            return null;
        }
    }

    /**
     * @return self::PEEK_NIL|self::PEEK_OK|self::PEEK_ERR статус последнего вызова peek()
     */
    public function peekStatus(): int
    {
        return $this->statusPeek;
    }

    /**
     * @return self::POP_NIL|self::POP_OK|self::POP_ERR статус последнего вызова pop()
     */
    public function popStatus(): int
    {
        return $this->statusPop;
    }

    /**
     * @return self::PUSH_NIL|self::PUSH_OK|self::PUSH_ERR статус последнего вызова push()
     */
    public function pushStatus(): int
    {
        return $this->statusPush;
    }
}
