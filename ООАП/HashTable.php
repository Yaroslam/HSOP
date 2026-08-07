<?php

/**
 * @template T
 */
class HashTable
{
    public const int PUT_STATUS_NIL = 0; //put() ещё не вызывался
    public const int PUT_STATUS_OK = 1; //put()  завершился успешно
    public const int PUT_STATUS_ERR = 2; //put() завершился неуспешно
    public const int REMOVE_STATUS_NIL = 0; // remove() ещё не вызывался
    public const int REMOVE_STATUS_OK = 1; // remove() завершился успешно
    public const int REMOVE_STATUS_ERR = 2; //remove() завершился неуспешно
    public const int CONTAINS_STATUS_NIL = 0; //contains() ещё не вызывался
    public const int CONTAINS_STATUS_OK = 1; //contains() завершился успешно
    public const int CONTAINS_STATUS_ERR = 2; ///contains() завершился неуспешно

    private const int DEFAULT_SIZE = 32; //максимальный размер хэш таблицы по умолчанию

    /** @var array<int, list<T>|null> */
    private array $slots;

    /** @var positive-int */
    private int $maxSize;

    private int $size;
    private int $putStatus;
    private int $removeStatus;
    private int $containsStatus;

    /**
     * постусловие: создана пустая хэш таблица на maxSize элементов
     *
     * @param int|null $maxSize
     */
    public function __construct(?int $maxSize = null)
    {
        $this->maxSize = (is_null($maxSize) || $maxSize <= 0) ? self::DEFAULT_SIZE : $maxSize;
        $this->slots = array_fill(0, $this->maxSize, null);
        $this->size = 0;
        $this->putStatus = self::PUT_STATUS_NIL;
        $this->removeStatus = self::REMOVE_STATUS_NIL;
        $this->containsStatus = self::CONTAINS_STATUS_NIL;
    }

    // команды

    /**
     * предусловие: хэш таблица не заполнена
     * постусловие: в хэш таблицу добавлено новое значение
     *
     * @param T $value
     */
    public function put(mixed $value): void
    {
        $index = $this->hashFunc($value);
        if ($this->slots[$index] !== null && in_array($value, $this->slots[$index], true)) {
            $this->putStatus = self::PUT_STATUS_OK;
            return;
        }
        if ($this->size === $this->maxSize) {
            $this->putStatus = self::PUT_STATUS_ERR;
            return;
        }
        if ($this->slots[$index] === null) {
            $this->slots[$index] = [];
        }
        $this->slots[$index][] = $value;
        $this->size++;
        $this->putStatus = self::PUT_STATUS_OK;
    }

    /**
     * предусловие: в хэш таблице присутсвует значение value
     * постусловие: из хэш таблицы удалено значение value
     *
     * @param T $value
     */
    public function remove(mixed $value): void
    {
        $index = $this->hashFunc($value);
        if ($this->slots[$index] === null) {
            $this->removeStatus = self::REMOVE_STATUS_ERR;
            return;
        }
        $position = array_search($value, $this->slots[$index], true);
        if ($position === false) {
            $this->removeStatus = self::REMOVE_STATUS_ERR;
            return;
        }
        array_splice($this->slots[$index], $position, 1);
        $this->size--;
        $this->removeStatus = self::REMOVE_STATUS_OK;
        if ($this->slots[$index] === []) {
            $this->slots[$index] = null;
        }
    }

    // запросы

    /**
     * содержится ли значение value в хэш таблице
     *
     * @param T $value
     */
    public function contains(mixed $value): bool
    {
        $index = $this->hashFunc($value);
        if ($this->slots[$index] === null || !in_array($value, $this->slots[$index], true)) {
            $this->containsStatus = self::CONTAINS_STATUS_ERR;
            return false;
        }
        $this->containsStatus = self::CONTAINS_STATUS_OK;
        return true;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function maxSize(): int
    {
        return $this->maxSize;
    }

    public function putStatus(): int
    {
        return $this->putStatus;
    }

    public function removeStatus(): int
    {
        return $this->removeStatus;
    }

    public function containsStatus(): int
    {
        return $this->containsStatus;
    }

    /**
     * @param T $value
     */
    private function hashFunc(mixed $value): int
    {
        $asString = (string) $value;
        $hash = 5381;
        for ($i = 0; $i < strlen($asString); $i++) {
            $hash = (($hash * 33) + ord($asString[$i])) & 0x7FFFFFFF;
        }
        return $hash % $this->maxSize;
    }
}
