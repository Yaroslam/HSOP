<?php

/**
 * @template T
 */
class Queue
{
    public const DEQUEUE_STATUS_NIL = 0; // dequeue() ещё не вызывалось
    public const int DEQUEUE_STATUS_OK = 1; // dequeue() прошло успешно
    public const int DEQUEUE_STATUS_ERR = 2; // dequeue() прошло неуспешно
    public const int GET_STATUS_NIL = 0; // get() ещё не вызывался
    public const int GET_STATUS_OK = 1; // get() прошёл успешно
    public const int GET_STATUS_ERR = 2; // get() прошел неудачно

    /** @var list<T> */
    protected array $queue;
    protected int $size;
    protected int $dequeueStatus;
    protected int $getStatus;

    // постусловие: создана пустая очередь
    public function __construct()
    {
        $this->queue = [];
        $this->size = 0;
        $this->dequeueStatus = self::DEQUEUE_STATUS_NIL;
        $this->getStatus = self::GET_STATUS_NIL;
    }

    //команды

    /**
     * Постусловие: добавлен новый элемент в хвост очереди
     *
     * @param T $value
     */
    public function enqueue(mixed $value): void
    {
        $this->queue[] = $value;
        $this->size++;
    }

    /**
     * предусловие: очередь не пуста
     * постусловие: удалён элемент из головы очереди
     */
    public function dequeue(): void
    {
        if (empty($this->queue)) {
            $this->dequeueStatus = self::DEQUEUE_STATUS_ERR;
            return;
        }
        $this->dequeueStatus = self::DEQUEUE_STATUS_OK;
        array_shift($this->queue);
        $this->size--;
    }

    //запросы
    /**
     * предусловие: очередь не пуста
     *
     * @return T|null
     */
    public function get(): mixed
    {
        if (empty($this->queue)) {
            $this->getStatus = self::GET_STATUS_ERR;
            return null;
        }
        $this->getStatus = self::GET_STATUS_OK;
        return $this->queue[0];
    }

    public function size(): int
    {
        return $this->size;
    }

    // запросы статусов
    public function dequeueStatus(): int
    {
        return $this->dequeueStatus;
    }

    public function getStatus(): int
    {
        return $this->getStatus;
    }
}

/**
 * Двусторонняя очередь (дек)
 *
 * @template T
 * @extends Queue<T>
 */
class Dequeue extends Queue
{
    public const int REMOVE_TAIL_STATUS_NIL = 0; // removeTail ещё не вызывался
    public const int REMOVE_TAIL_STATUS_OK = 1; // removeTail прошел успешно
    public const int REMOVE_TAIL_STATUS_ERR = 2; // removeTail завершился ошибкой
    public const int GET_TAIL_STATUS_NIL = 0; // getTail ещё не вызывался
    public const int GET_TAIL_STATUS_OK = 1; // getTail прошёл успешно
    public const int GET_TAIL_STATUS_ERR = 2; //getTail завершился ошибкой

    private int $removeTailStatus;
    private int $getTailStatus;

    // постусловие: создан пустой дек
    public function __construct()
    {
        parent::__construct();
        $this->removeTailStatus = self::REMOVE_TAIL_STATUS_NIL;
        $this->getTailStatus = self::GET_TAIL_STATUS_NIL;
    }

    // команды

    /**
     * постусловие: в начало очереди добавлен новый элемент
     *
     * @param T $value
     */
    public function addFront(mixed $value): void
    {
        array_unshift($this->queue, $value);
        $this->size++;
    }

    /**
     * предусловие: очередь не пуста
     * постусловие: из конца очереди удалён элемент
     */
    public function removeTail(): void
    {
        if (empty($this->queue)) {
            $this->removeTailStatus = self::REMOVE_TAIL_STATUS_ERR;
            return;
        }
        array_pop($this->queue);
        $this->size--;
        $this->removeTailStatus = self::REMOVE_TAIL_STATUS_OK;
    }

    // запросы

    /**
     * предусловие: очередь не пуста
     *
     * @return T|null
     */
    public function getTail(): mixed
    {
        if (empty($this->queue)) {
            $this->getTailStatus = self::GET_TAIL_STATUS_ERR;
            return null;
        }
        $this->getTailStatus = self::GET_TAIL_STATUS_OK;
        return $this->queue[$this->size - 1];
    }

    // запросы статусов
    public function removeTailStatus(): int
    {
        return $this->removeTailStatus;
    }

    public function getTailStatus(): int
    {
        return $this->getTailStatus;
    }
}
