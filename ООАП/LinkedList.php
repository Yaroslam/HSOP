<?php

/**
 * Постусловие: создан новый пустой список
 *
 * @template T
 */
abstract class LinkedList
{
    //команды

    /**
     * Предусловие: список не пустой
     * Постусловие: курсор установлен на первый узел списка
     */
    abstract public function head(): void;

    /**
     * Предусловие: список не пустой
     * Постусловие: курсор установлен на последний узел списка
     */
    abstract public function tail(): void;

    /**
     * Предусловие: справа от курсора есть элемент
     * Постусловие: курсор сдвинут вправо
     */
    abstract public function right(): void;

    /**
     * Предусловие: список не пустой
     * Постусловие: справа от текущего узла вставлен узел со значением $value
     * @param T $value
     */
    abstract public function putRight(mixed $value): void;

    /**
     * Предусловие: список не пустой
     * Постусловие: слева от текущего узла вставлен узел со значением $value
     * @param T $value
     */
    abstract public function putLeft(mixed $value): void;

    /**
     * Предусловие: список не пустой
     * Постусловие: текущий узел удалён, курсор смещён врпаво, если там есть узел,
     * иначе вмещен влево, если там есть узел
     */
    abstract public function remove(): void;

    /**
     * Постусловие: пустой список
     */
    abstract public function clear(): void;

    /**
     * Постусловие: новый узел добавлен в хвост
     * @param T $value
     */
    abstract public function addTail(mixed $value): void;

    /**
     * Постусловие: в списке удалены все узлы с заданным значением
     * @param T $value
     */
    abstract public function removeAll(mixed $value): void;

    /**
     * Предусловие: список не пуст
     * Постусловие: значение текущего узла заменено на переданное
     * @param T $value
     */
    abstract public function replace(mixed $value): void;

    /**
     * Постусловие: курсор установлен на следующий узел с искомым значением, если такой узел существует
     * @param T $value
     */
    abstract public function find(mixed $value): void;

    //запросы

    /**
     * Возвращает значение текущего узла
     * Предусловие: список не пуст
     * @return T|null
     */
    abstract public function get(): mixed;

    /**
     * Курсор установлен на первый узел списка?
     */
    abstract public function isHead(): bool;

    /**
     * Курсор установлен на последний узел списка?
     */
    abstract public function isTail(): bool;

    /**
     * Курсор установлен на существующий узел?
     */
    abstract public function isValue(): bool;

    /**
     * Возвращает количество узлов в списке
     *
     * @return int<0, max>
     */
    abstract public function size(): int;

    //запросы статусов

    /**
     * @return int статус последнего вызова head()
     */
    abstract public function headStatus(): int;

    /**
     * @return int статус последнего вызова tail()
     */
    abstract public function tailStatus(): int;

    /**
     * @return int статус последнего вызова right()
     */
    abstract public function rightStatus(): int;

    /**
     * @return int статус последнего вызова putRight()
     */
    abstract public function putRightStatus(): int;

    /**
     * @return int статус последнего вызова putLeft()
     */
    abstract public function putLeftStatus(): int;

    /**
     * @return int статус последнего вызова remove()
     */
    abstract public function removeStatus(): int;

    /**
     * @return int статус последнего вызова replace()
     */
    abstract public function replaceStatus(): int;

    /**
     * @return int статус последнего вызова find()
     */
    abstract public function findStatus(): int;

    /**
     * @return int статус последнего вызова get()
     */
    abstract public function getStatus(): int;
}

/**
 * Задание 2.2. Почему операция tail не сводима к другим операциям (если исходить из эффективной реализации)?
 * Ответ: связанный список предполагает хранение внутри себя указатей на голову и хвост. Что дает O(1) на получение
 * хвоста, полчение другими операциями не даст O(1)
 */

/**
 * Задание 2.3. Операция поиска всех узлов с заданным значением, выдающая список таких узлов, уже не нужна. Почему?
 * Ответ: У нас есть find, который ищет значение относительно текущего узла и переходит к узлу с этим значением.
 * В цикле пользователь получит все узлы с необходимым значением.
 */
