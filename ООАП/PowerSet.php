<?php

/**
 * @template T
 *
 * @extends HashTable<T>
 */
class PowerSet extends HashTable
{
    /**
     * @param int $size
     */
    public function __construct(int $size)
    {
        parent::__construct($size);
    }

    /**
     * Возвращает пересечение текущего множества с переданным множеством.
     *
     * @param PowerSet<T> $other
     *
     * @return PowerSet<T>
     */
    public function intersection(PowerSet $other): PowerSet
    {
        $newSet = new PowerSet($this->maxSize);

        foreach ($this->slots as $slot) {
            if ($slot !== null && $other->contains($slot)) {
                $newSet->put($slot);
            }
        }

        return $newSet;
    }

    /**
     * Возвращает объединение текущего множества с переданным множеством.
     *
     * @param PowerSet<T> $other
     *
     * @return PowerSet<T>
     */
    public function union(PowerSet $other): PowerSet
    {
        $newSet = new PowerSet(
            $this->maxSize + $other->maxSize
        );

        foreach ($this->slots as $slot) {
            if ($slot !== null && !$other->contains($slot)) {
                $newSet->put($slot);
            }
        }

        foreach ($other->slots as $slot) {
            if ($slot !== null && !$this->contains($slot)) {
                $newSet->put($slot);
            }
        }

        return $newSet;
    }

    /**
     * Возвращает разницу между текущим множеством и переданным множеством
     *
     * @param PowerSet<T> $other
     *
     * @return PowerSet<T>
     */
    public function difference(PowerSet $other): PowerSet
    {
        $newSet = new PowerSet($this->maxSize);

        foreach ($this->slots as $slot) {
            if ($slot !== null && !$other->contains($slot)) {
                $newSet->put($slot);
            }
        }

        return $newSet;
    }

    /**
     * Проверяет, является ли другое множество подмножеством текущего.
     *
     * @param PowerSet<T> $other
     */
    public function isSubset(PowerSet $other): bool
    {
        return array_all($other->slots, fn($slot) => $slot === null || $this->contains($slot));

    }
}
