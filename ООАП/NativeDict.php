<?php

/**
 * Словарь с открытой адресацией: ключ - строка, значение - произвольного типа
 *
 * @template T
 */
class NativeDict
{
    public const int PUT_NIL = 0; //put() ещё не вызывался
    public const int PUT_OK = 1; //последний вызов put() завершился успехом
    public const int PUT_ERR = 2; //последний вызов put() завершился неудачно

    public const int GET_NIL = 0; //get() ещё не вызывался
    public const int GET_OK = 1; //последний вызов get() завершился успехом
    public const int GET_ERR = 2; //последний вызов get() завершился неудачно

    private const int DEFAULT_SIZE = 32; //максимальное количество пар по умолчанию
    private const int PROBE_STEP = 1; //шаг линейного пробирования, взаимно прост с любым размером

    /** @var array<int, string|null> */
    private array $slots;

    /** @var array<int, T|null> */
    private array $values;

    /** @var positive-int */
    private int $maxSize;

    /** @var int<0, max> */
    private int $count = 0;

    private int $statusPut = self::PUT_NIL;

    private int $statusGet = self::GET_NIL;

    /**
     * Постусловие: создан пустой словарь на maxSize пар ключ-значение
     */
    public function __construct(?int $maxSize = null)
    {
        $this->maxSize = (is_null($maxSize) || $maxSize <= 0) ? self::DEFAULT_SIZE : $maxSize;
        $this->slots = array_fill(0, $this->maxSize, null);
        $this->values = array_fill(0, $this->maxSize, null);
    }

    //команды

    /**
     * Записывает значение value по ключу key
     * Предусловие: ключ key уже есть в словаре или в словаре есть свободный слот
     * Постусловие: по ключу key хранится значение value
     *
     * @param T $value
     */
    public function put(string $key, mixed $value): void
    {
        $index = $this->findSlot($key);
        if ($index === null) {
            $this->statusPut = self::PUT_ERR;
            return;
        }

        if ($this->slots[$index] === null) {
            $this->slots[$index] = $key;
            $this->count++;
        }
        $this->values[$index] = $value;
        $this->statusPut = self::PUT_OK;
    }

    //запросы

    /**
     * Возвращает значение, хранящееся по ключу key
     * Предусловие: ключ key присутствует в словаре
     *
     * @return T|null
     */
    public function get(string $key): mixed
    {
        $index = $this->findSlot($key);
        if ($index === null || $this->slots[$index] === null) {
            $this->statusGet = self::GET_ERR;
            return null;
        }

        $this->statusGet = self::GET_OK;
        return $this->values[$index];
    }

    /**
     * Присутствует ли ключ key в словаре
     */
    public function isKey(string $key): bool
    {
        $index = $this->findSlot($key);

        return $index !== null && $this->slots[$index] !== null;
    }

    /**
     * Возвращает индекс слота, с которого начинается поиск ключа key
     *
     * @return int<0, max>
     */
    public function hashFunc(string $key): int
    {
        $hash = 5381;
        for ($i = 0; $i < strlen($key); $i++) {
            $hash = (($hash * 33) + ord($key[$i])) & 0x7FFFFFFF;
        }

        return $hash % $this->maxSize;
    }

    /**
     * Возвращает текущее количество пар ключ-значение
     *
     * @return int<0, max>
     */
    public function size(): int
    {
        return $this->count;
    }

    /**
     * Возвращает максимальное количество пар ключ-значение
     *
     * @return positive-int
     */
    public function maxSize(): int
    {
        return $this->maxSize;
    }

    //запросы статусов

    public function putStatus(): int
    {
        return $this->statusPut;
    }

    public function getStatus(): int
    {
        return $this->statusGet;
    }


    private function findSlot(string $key): ?int
    {
        $index = $this->hashFunc($key);
        for ($i = 0; $i < $this->maxSize; $i++) {
            if ($this->slots[$index] === null || $this->slots[$index] === $key) {
                return $index;
            }
            $index = ($index + self::PROBE_STEP) % $this->maxSize;
        }

        return null;
    }
}
