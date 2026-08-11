<?php

/**
 * @template T
 */
class BloomFilter
{
    /**
     * Битовый массив фильтра.
     *
     * @var int
     */
    private int $bitArray = 0;

    /**
     * Размер битового массива.
     *
     * @var int
     */
    private int $size;

    /**
     * @param int $size Размер фильтра
     */
    public function __construct(int $size = 32)
    {
        $this->size = $size;
    }

    /**
     * @param T $value
     */
    public function firstHashFunction(mixed $value): int
    {
        $string = (string) $value;
        $hash = 0;

        for ($i = 0, $length = strlen($string); $i < $length; $i++) {
            $hash = ($hash * 31 + ord($string[$i])) % $this->size;
        }

        return $hash;
    }

    /**
     * @param T $value
     */
    public function secondHashFunction(mixed $value): int
    {
        $string = (string) $value;
        $hash = 0;

        for ($i = 0, $length = strlen($string); $i < $length; $i++) {
            $hash = ($hash * 53 + ord($string[$i])) % $this->size;
        }

        return $hash;
    }

    /**
     * Добавляет значение в фильтр.
     *
     * @param T $value
     */
    public function put(mixed $value): void
    {
        $firstIndex = $this->firstHashFunction($value);
        $secondIndex = $this->secondHashFunction($value);

        $this->bitArray |= (1 << $firstIndex);
        $this->bitArray |= (1 << $secondIndex);
    }

    /**
     * Проверяет наличие значения в фильтре
     *
     * @param T $value
     */
    public function isValue(mixed $value): bool
    {
        $firstIndex = $this->firstHashFunction($value);
        $secondIndex = $this->secondHashFunction($value);

        $firstMask = 1 << $firstIndex;
        $secondMask = 1 << $secondIndex;

        return ($this->bitArray & $firstMask) !== 0
            && ($this->bitArray & $secondMask) !== 0;
    }
}