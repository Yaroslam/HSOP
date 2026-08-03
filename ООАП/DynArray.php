<?php

/**
 * Постусловие: создан новый пустой массив ёмкостью MIN_CAPACITY
 *
 * @template T
 */
class DynArray
{
    public const int INSERT_NIL = 0; //insert() еще не вызывался
    public const int INSERT_OK = 1; //последний вызов insert() завершился успехом
    public const int INSERT_ERR = 2; //последний вызов insert() завершился неудачно

    public const int REMOVE_NIL = 0; //remove() не вызывался
    public const int REMOVE_OK = 1; //последний вызов remove() завершился успехом
    public const int REMOVE_ERR = 2; //последний вызов remove() завершился неудачно

    public const int GET_NIL = 0; //get() не вызывался
    public const int GET_OK = 1; //последний вызов get() завершился успехом
    public const int GET_ERR = 2; //последний вызов get() завершился неудачно

    private const int MIN_CAPACITY = 16; //минимальная ёмкость массива
    private const float GROW_FACTOR = 2.0; //увеличение емкости при переполнении
    private const float SHRINK_FACTOR = 1.5; //уменьшение емекости при ее избытке
    private const float SHRINK_THRESHOLD = 0.5; //коэффициент наполненности, ниже которого ёмкость считается избыточной

    /** @var array<int, T|null> */
    private array $array = [];

    /** @var int<0, max> */
    private int $count = 0;

    /** @var positive-int */
    private int $capacity = self::MIN_CAPACITY;

    /** @var self::INSERT_NIL|self::INSERT_OK|self::INSERT_ERR */
    private int $statusInsert = self::INSERT_NIL;

    /** @var self::REMOVE_NIL|self::REMOVE_OK|self::REMOVE_ERR */
    private int $statusRemove = self::REMOVE_NIL;

    /** @var self::GET_NIL|self::GET_OK|self::GET_ERR */
    private int $statusGet = self::GET_NIL;

    public function __construct()
    {
        $this->makeArray(self::MIN_CAPACITY);
    }

    //команды

    /**
     * Вставляет значение в позицию $index, сдвигая хвост вправо
     * Предусловие: индекс находится в допустимых границах от 0 до size
     * Постусловие: под индексом $index находится значение $value, размер массива увеличен на 1, индексы массива пересчитаны
     * @param T $value
     */
    public function insert(int $index, mixed $value): void
    {
        if ($index < 0 || $index > $this->count) {
            $this->statusInsert = self::INSERT_ERR;
            return;
        }

        if ($this->count === $this->capacity) {
            $this->makeArray((int)($this->capacity * self::GROW_FACTOR));
        }

        for ($i = $this->count; $i > $index; $i--) {
            $this->array[$i] = $this->array[$i - 1];
        }
        $this->array[$index] = $value;
        $this->count++;
        $this->statusInsert = self::INSERT_OK;
    }

    /**
     * Добавляет значение в конец массива
     * Постусловие: в конце массива добавлен новый элемент, размер массива увеличен на 1
     * @param T $value
     */
    public function append(mixed $value): void
    {
        if ($this->count === $this->capacity) {
            $this->makeArray((int)($this->capacity * self::GROW_FACTOR));
        }

        $this->array[$this->count] = $value;
        $this->count++;
    }

    /**
     * Удаляет элемент под индексом $index, сдвигая хвост влево
     * Предусловие: индекс находится в допустимых границах от 0 до size
     * Постусловие: элемент под индексом $index удалён, размер массива уменьшен на 1, индексы массива пересчитаны
     */
    public function remove(int $index): void
    {
        if ($index < 0 || $index >= $this->count) {
            $this->statusRemove = self::REMOVE_ERR;
            return;
        }

        for ($i = $index; $i < $this->count - 1; $i++) {
            $this->array[$i] = $this->array[$i + 1];
        }
        $this->array[$this->count - 1] = null;
        $this->count--;
        $this->statusRemove = self::REMOVE_OK;

        if ($this->capacity > self::MIN_CAPACITY && $this->count < $this->capacity * self::SHRINK_THRESHOLD) {
            $this->makeArray(max(self::MIN_CAPACITY, (int)($this->capacity / self::SHRINK_FACTOR)));
        }
    }

    /**
     * Очищает массив
     * Постусловие: массив пуст, ёмкость сброшена до минимальной, статусы операций обнулены
     */
    public function clear(): void
    {
        $this->count = 0;
        $this->array = [];
        $this->makeArray(self::MIN_CAPACITY);
        $this->statusInsert = self::INSERT_NIL;
        $this->statusRemove = self::REMOVE_NIL;
        $this->statusGet = self::GET_NIL;
    }

    //запросы

    /**
     * Возвращает значение элемента под индексом $index
     * Предусловие: индекс находится в допустимых границах (0 <= $index < size())
     * @return T|null значение элемента, null если индекс недопустим
     */
    public function get(int $index): mixed
    {
        if ($index < 0 || $index >= $this->count) {
            $this->statusGet = self::GET_ERR;
            return null;
        }

        $this->statusGet = self::GET_OK;
        return $this->array[$index];
    }

    /**
     * Возвращает текущий размер массива
     *
     * @return int<0, max>
     */
    public function size(): int
    {
        return $this->count;
    }

    /**
     * Возвращает текущую ёмкость
     *
     * @return positive-int
     */
    public function capacity(): int
    {
        return $this->capacity;
    }

    //запросы статусов

    /**
     * @return self::INSERT_NIL|self::INSERT_OK|self::INSERT_ERR
     * статус последнего вызова insert()
     */
    public function insertStatus(): int
    {
        return $this->statusInsert;
    }

    /**
     * @return self::REMOVE_NIL|self::REMOVE_OK|self::REMOVE_ERR
     * статус последнего вызова remove()
     */
    public function removeStatus(): int
    {
        return $this->statusRemove;
    }

    /**
     * @return self::GET_NIL|self::GET_OK|self::GET_ERR
     * статус последнего вызова get()
     */
    public function getStatus(): int
    {
        return $this->statusGet;
    }

    /**
     * Меняет ёмкость буфера, сохраняя первые $count значений
     * Предусловие: $capacity >= size()
     * Постусловие: буфер содержит ровно $capacity ячеек
     *
     * @param positive-int $capacity ёмкость буфера
     */
    private function makeArray(int $capacity): void
    {
        $new = array_fill(0, $capacity, null);
        for ($i = 0; $i < $this->count; $i++) {
            $new[$i] = $this->array[$i];
        }

        $this->array = $new;
        $this->capacity = $capacity;
    }
}
