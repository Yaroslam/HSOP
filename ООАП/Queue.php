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
    private array $queue;
    private int $size;
    private int $dequeueStatus;
    private int $getStatus;

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
