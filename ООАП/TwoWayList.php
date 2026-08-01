<?php
/**
 * Узел двусвязного списка.
 *
 * @template Value
 */
final class Node
{
    /** @var Value|null */
    private mixed $value;

    /** @var Node<Value>|null */
    private ?Node $next = null;

    /** @var Node<Value>|null */
    private ?Node $previous = null;

    /** @param Value|null $value */
    public function __construct(mixed $value = null)
    {
        $this->value = $value;
    }

    /** @return Value|null */
    public function value(): mixed
    {
        return $this->value;
    }

    /** @param Value $value */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    /** @return Node<Value>|null */
    public function nextNode(): ?Node
    {
        return $this->next;
    }

    /** @return Node<Value>|null */
    public function previousNode(): ?Node
    {
        return $this->previous;
    }

    /** @param Node<Value>|null $nextNode */
    public function setNextNode(?Node $nextNode): void
    {
        $this->next = $nextNode;
    }

    /** @param Node<Value>|null $previousNode */
    public function setPreviousNode(?Node $previousNode): void
    {
        $this->previous = $previousNode;
    }
}

/**
 * Базовый список: хранит реализацию всех общих команд и запросов.
 *
 * @template T
 */
abstract class ParentList
{
    // ** статусы **
    public const HEAD_NIL = 0; // head() не вызывался
    public const HEAD_OK = 1; // head() отработал корректно
    public const HEAD_ERR = 2; //  head() отработал некорректно

    public const TAIL_NIL = 0; // tail() не вызывался
    public const TAIL_OK = 1; //  tail() отработал корректно
    public const TAIL_ERR = 2; // tail() отработал некорректно

    public const RIGHT_NIL = 0; // right() не вызывался
    public const RIGHT_OK = 1; //  right() отработал корректно
    public const RIGHT_ERR = 2; //  right() отработал некорректно

    public const PUT_RIGHT_NIL = 0; // put_right() не вызывался
    public const PUT_RIGHT_OK = 1; //  put_right() отработал корректно
    public const PUT_RIGHT_ERR = 2; // put_right() отработал некорректно

    public const PUT_LEFT_NIL = 0; // put_left() не вызывался
    public const PUT_LEFT_OK = 1; //  put_left() отработал корректно
    public const PUT_LEFT_ERR = 2; // put_left() отработал некорректно

    public const REMOVE_NIL = 0; // remove() не вызывался
    public const REMOVE_OK = 1; //  remove() отработал корректно
    public const REMOVE_ERR = 2; // remove() отработал некорректно

    public const REPLACE_NIL = 0; // replace() не вызывался
    public const REPLACE_OK = 1; //  replace() отработал корректно
    public const REPLACE_ERR = 2; // replace() отработал некорректно

    public const FIND_NIL = 0; // find() не вызывался
    public const FIND_OK = 1; //  find() отработал корректно
    public const FIND_ERR = 2; // find() отработал некорректно или не нашел узел

    public const REMOVE_ALL_NIL = 0; // remove_all() не вызывался
    public const REMOVE_ALL_OK = 1; //  remove_all() отработал корректно
    public const REMOVE_ALL_ERR = 2; // remove_all() отработал некорректно

    public const GET_NIL = 0; // get() не вызывался
    public const GET_OK = 1; //  get() отработал корректно
    public const GET_ERR = 2; // get() отработал некорректно

    /** @var Node<T> */
    protected Node $head;

    /** @var Node<T> */
    protected Node $tail;

    /** @var Node<T>|null */
    protected ?Node $current = null;

    private int $size = 0;

    private int $headStatus = self::HEAD_NIL;
    private int $tailStatus = self::TAIL_NIL;
    private int $rightStatus = self::RIGHT_NIL;
    private int $putRightStatus = self::PUT_RIGHT_NIL;
    private int $putLeftStatus = self::PUT_LEFT_NIL;
    private int $removeStatus = self::REMOVE_NIL;
    private int $replaceStatus = self::REPLACE_NIL;
    private int $fingStatus = self::FIND_NIL;
    private int $removeAllStatus = self::REMOVE_ALL_NIL;
    private int $getStatus = self::GET_NIL;


    // постусловие: создан новый пустой список
    public function __construct()
    {
        $this->head = new Node(null);
        $this->tail = new Node(null);
        $this->head->setNextNode($this->tail);
        $this->tail->setPreviousNode($this->head);
    }

    //команды

    /**
     * Предусловие: список не пустой
     *  Постусловие: курсор установлен на первый узел списка
     */
    public function head(): void
    {
        if ($this->current === null) {
            $this->headStatus = self::HEAD_ERR;
        } else {
            $this->current = $this->head->nextNode();
            $this->headStatus = self::HEAD_OK;
        }
    }

    /**
     * Предусловие: список не пустой
     * Постусловие: курсор установлен на последний узел списка
     */
    public function tail(): void
    {
        if ($this->current === null) {
            $this->tailStatus = self::TAIL_ERR;
        } else {
            $this->current = $this->tail->previousNode();
            $this->tailStatus = self::TAIL_OK;
        }
    }

    /**
     * Предусловие: справа от курсора есть элемент
     * Постусловие: курсор сдвинут вправо
     */
    public function right(): void
    {
        if ($this->current === null) {
            $this->rightStatus = self::RIGHT_ERR;
        } else {
            $this->current = $this->isTail() ? $this->current : $this->current->nextNode();
            $this->rightStatus = self::RIGHT_OK;
        }
    }

    /**
     * Предусловие: список не пустой
     * Постусловие: справа от текущего узла вставлен узел со значением $value
     * @param T $value
     */
    public function putRight(mixed $value): void
    {
        if ($this->current === null) {
            $this->putRightStatus = self::PUT_RIGHT_ERR;
        } else {
            /** @var Node<T> $newNode */
            $newNode = new Node($value);
            $newNode->setNextNode($this->current->nextNode());
            $newNode->setPreviousNode($this->current);
            $this->current->nextNode()->setPreviousNode($newNode);
            $this->current->setNextNode($newNode);
            $this->size++;
            $this->putRightStatus = self::PUT_RIGHT_OK;
        }
    }

    /**
     * Предусловие: список не пустой
     * Постусловие: слева от текущего узла вставлен узел со значением $value
     * @param T $value
     */
    public function putLeft(mixed $value): void
    {
        if ($this->current === null) {
            $this->putLeftStatus = self::PUT_LEFT_ERR;
        } else {
            /** @var Node<T> $newNode */
            $newNode = new Node($value);
            $newNode->setNextNode($this->current);
            $newNode->setPreviousNode($this->current->previousNode());
            $this->current->previousNode()->setNextNode($newNode);
            $this->current->setPreviousNode($newNode);
            $this->size++;
            $this->putLeftStatus = self::PUT_LEFT_OK;
        }
    }

    /**
     * Предусловие: список не пустой
     * Постусловие: текущий узел удалён, курсор смещён врпаво, если там есть узел,
     * иначе вмещен влево, если там есть узел
     */
    public function remove(): void
    {
        if ($this->current === null) {
            $this->removeStatus = self::REMOVE_ERR;
            return;
        }

        $next = $this->current->nextNode();
        $previous = $this->current->previousNode();
        $previous->setNextNode($next);
        $next->setPreviousNode($previous);
        $this->size--;

        if ($next !== $this->tail) {
            $this->current = $next;
        } elseif ($previous !== $this->head) {
            $this->current = $previous;
        } else {
            $this->current = null; //список пуст
        }

        $this->removeStatus = self::REMOVE_OK;
    }

    /**
     * Постусловие: пустой список
     */
    public function clear(): void
    {
        $this->size = 0;
        $this->current = null;
        $this->head->setNextNode($this->tail);
        $this->tail->setPreviousNode($this->head);
    }

    /**
     * Постусловие: новый узел добавлен в хвост
     * @param T $value
     */
    public function addTail(mixed $value): void
    {
        /** @var Node<T> $newNode */
        $newNode = new Node($value);
        $last = $this->tail->previousNode();
        $last->setNextNode($newNode);
        $newNode->setPreviousNode($last);
        $newNode->setNextNode($this->tail);
        $this->tail->setPreviousNode($newNode);
        $this->size++;

        if ($this->current === null) {
            $this->current = $newNode;
        }
    }

    /**
     * Постусловие: в списке удалены все узлы с заданным значением
     * @param T $value
     */
    public function removeAll(mixed $value): void
    {
        if ($this->current === null) {
            $this->removeAllStatus = self::REMOVE_ALL_ERR;
            return;
        }

        $currentRemoved = false;
        $node = $this->head->nextNode();
        while ($node !== $this->tail) {
            $next = $node->nextNode();
            if ($node->value() === $value) {
                $node->previousNode()->setNextNode($next);
                $next->setPreviousNode($node->previousNode());
                $this->size--;
                if ($node === $this->current) {
                    $currentRemoved = true;
                }
            }
            $node = $next;
        }

        if ($currentRemoved) {
            $this->current = $this->size > 0 ? $this->head->nextNode() : null;
        }

        $this->removeAllStatus = self::REMOVE_ALL_OK;
    }

    /**
     * Предусловие: список не пуст
     * Постусловие: значение текущего узла заменено на переданное
     * @param T $value
     */
    public function replace(mixed $value): void
    {
        if ($this->current === null) {
            $this->replaceStatus = self::REPLACE_ERR;
        } else {
            $this->current->setValue($value);
            $this->replaceStatus = self::REPLACE_OK;
        }
    }

    /**
     * Постусловие: курсор установлен на следующий узел с искомым значением, если такой узел существует
     * @param T $value
     */
    public function find(mixed $value): void
    {
        if ($this->current === null) {
            $this->fingStatus = self::FIND_ERR;
            return;
        }

        $node = $this->current->nextNode();
        while ($node !== $this->tail) {
            if ($node->value() === $value) {
                $this->current = $node;
                $this->fingStatus = self::FIND_OK;
                return;
            }
            $node = $node->nextNode();
        }

        $this->fingStatus = self::FIND_ERR;
    }

    //запросы

    /**
     * Возвращает значение текущего узла
     * Предусловие: список не пуст
     * @return T|null
     */
    public function get(): mixed
    {
        if ($this->current === null) {
            $this->getStatus = self::GET_ERR;
            return null;
        }

        $this->getStatus = self::GET_OK;
        return $this->current->value();
    }

    public function size(): int
    {
        return $this->size;
    }



    /**
     * Курсор установлен на первый узел списка?
     */
    public function isHead(): bool
    {
        return $this->current !== null && $this->current === $this->head->nextNode();
    }

    /**
     * Курсор установлен на последний узел списка?
     */
    public function isTail(): bool
    {
        return $this->current !== null && $this->current === $this->tail->previousNode();
    }

    /**
     * Курсор установлен на существующий узел?
     */
    public function isValue(): bool
    {
        return $this->current !== null;
    }

    //запросы статусов

    /**
     * @return int статус последнего вызова head()
     */
    public function headStatus(): int
    {
        return $this->headStatus;
    }

    /**
     * @return int статус последнего вызова tail()
     */
    public function tailStatus(): int
    {
        return $this->tailStatus;
    }

    /**
     * @return int статус последнего вызова putRight()
     */
    public function rightStatus(): int
    {
        return $this->rightStatus;
    }

    /**
     * @return int статус последнего вызова putLeft()
     */
    public function putRightStatus(): int
    {
        return $this->putRightStatus;
    }

    public function putLeftStatus(): int
    {
        return $this->putLeftStatus;
    }

    /**
     * @return int статус последнего вызова remove()
     */
    public function removeStatus(): int
    {
        return $this->removeStatus;
    }

    /**
     * @return int статус последнего вызова replace()
     */
    public function replaceStatus(): int
    {
        return $this->replaceStatus;
    }

    /**
     * @return int статус последнего вызова find()
     */
    public function findStatus(): int
    {
        return $this->fingStatus;
    }

    /**
     * @return int статус последнего вызова removeAll()
     */
    public function removeAllStatus(): int
    {
        return $this->removeAllStatus;
    }

    /**
     * @return int статус последнего вызова get()
     */
    public function getStatus(): int
    {
        return $this->getStatus;
    }
}

/**
 * Односвязный список курсор двигается только вправо.
 *
 * @template T
 * @extends ParentList<T>
 */
class LinkedList extends ParentList
{
    public function __construct()
    {
        parent::__construct();
    }
}

/**
 * Двунаправленный список c командой left().
 *
 * @template T
 * @extends ParentList<T>
 */
class TwoWayList extends ParentList
{
    public const LEFT_NIL = 0; // left() не вызывался
    public const LEFT_OK = 1; //  left() отработал нормально
    public const LEFT_ERR = 2; //  left() отработал некорректно

    private int $leftStatus = self::LEFT_NIL;

    public function __construct()
    {
        parent::__construct();
    }

    //команды

    /**
     * Предусловие: слева от курсора есть элемент
     * Постусловие: курсор сдвинут влево
     */
    public function left(): void
    {
        if ($this->current === null) {
            $this->leftStatus = self::LEFT_ERR;
        } else {
            $this->current = $this->isHead() ? $this->current : $this->current->previousNode();
            $this->leftStatus = self::LEFT_OK;
        }
    }

    /**
     * @return int статус последнего вызова left()
     */
    public function getLeftStatus(): int
    {
        return $this->leftStatus;
    }
}
