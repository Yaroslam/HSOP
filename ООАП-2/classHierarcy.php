<?php
declare(strict_types=1);

abstract class General
{
    final public function copyTo(General $target, bool $deep = true): void
    {
        if ($target::class !== static::class) {
            throw new InvalidArgumentException(sprintf(
                'Нельзя скопировать %s в %s', static::class, $target::class,
            ));
        }
        foreach (self::properties($this) as $prop) {
            if (!$prop->isInitialized($this)) {
                continue;
            }
            $value = $prop->getValue($this);
            $prop->setValue($target, $deep ? self::deepCopyValue($value) : $value);
        }
    }

    final public function deepClone(): static
    {
        $copy = clone $this;
        $this->copyTo($copy);
        return $copy;
    }

    final public function equals(mixed $other, bool $deep = true): bool
    {
        if ($other::class !== static::class) {
            return false;
        }
        if (!$deep) {
            foreach (self::properties($this) as $prop) {
                if ($prop->isInitialized($this) !== $prop->isInitialized($other)) {
                    return false;
                }
                if ($prop->isInitialized($this)
                    && $prop->getValue($this) !== $prop->getValue($other)) {
                    return false;
                }
            }
            return true;
        }
        $seen = [];
        return self::deepEqualsValue($this, $other, $seen);
    }

    final public function serialize(): string
    {
        return serialize($this);
    }

    final public static function deserialize(string $data): static
    {
        $obj = unserialize($data, ['allowed_classes' => true]);
        if (!$obj instanceof static) {
            throw new UnexpectedValueException(sprintf(
                'Ожидался %s, в строке — %s', static::class, get_debug_type($obj),
            ));
        }
        return $obj;
    }

    final public function restoreFrom(string $data): void
    {
        static::deserialize($data)->copyTo($this, deep: false);
    }

    final public function print(): string
    {
        return print_r($this, true);
    }

    public function __toString(): string
    {
        return $this->print();
    }

    final public function isType(string $type): bool
    {
        return is_a($this, $type);
    }

    final public function realType(): string
    {
        return static::class; // эквивалентно get_class($this)
    }

    //хелперы
    private static function properties(object $obj): array
    {
        $result = [];
        for ($class = new ReflectionClass($obj); $class; $class = $class->getParentClass()) {
            foreach ($class->getProperties() as $prop) {
                if ($prop->isStatic() || isset($result[$prop->getName()])) {
                    continue;
                }
                $result[$prop->getName()] = $prop;
            }
        }
        return $result;
    }

    private static function deepCopyValue(mixed $value, array &$seen = []): mixed
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = self::deepCopyValue($v, $seen);
            }
            return $value;
        }
        if (!is_object($value) || $value instanceof Closure) {
            return $value;
        }
        $id = spl_object_id($value);
        if (isset($seen[$id])) {
            return $seen[$id];
        }
        $copy = clone $value;
        $seen[$id] = $copy;
        foreach (self::properties($copy) as $prop) {
            if (!$prop->isInitialized($copy) || $prop->isReadOnly()) {
                continue;
            }
            $prop->setValue($copy, self::deepCopyValue($prop->getValue($copy), $seen));
        }
        return $copy;
    }

    private static function deepEqualsValue(mixed $a, mixed $b, array &$seen): bool
    {
        if (is_array($a) && is_array($b)) {
            if (array_keys($a) !== array_keys($b)) {
                return false;
            }
            foreach ($a as $k => $v) {
                if (!self::deepEqualsValue($v, $b[$k], $seen)) {
                    return false;
                }
            }
            return true;
        }
        if (is_object($a) && is_object($b)) {
            if ($a === $b) {
                return true;
            }
            if ($a::class !== $b::class) {
                return false;
            }
            $key = spl_object_id($a) . ':' . spl_object_id($b);
            if (isset($seen[$key])) {        // уже сравниваем эту пару — цикл
                return true;
            }
            $seen[$key] = true;
            foreach (self::properties($a) as $prop) {
                if ($prop->isInitialized($a) !== $prop->isInitialized($b)) {
                    return false;
                }
                if ($prop->isInitialized($a)
                    && !self::deepEqualsValue($prop->getValue($a), $prop->getValue($b), $seen)) {
                    return false;
                }
            }
            return true;
        }
        return $a === $b;
    }
}


class Any extends General
{
}