<?php

declare(strict_types=1);

interface Storage
{
    public function save(string $data): void;

    public function retrieve(int $id): string;
}

class DbStorage implements Storage
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function save(string $data): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO storage (data) VALUES (:data)');
        $stmt->execute(['data' => $data]);
    }

    public function retrieve(int $id): string
    {
        $stmt = $this->pdo->prepare('SELECT data FROM storage WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $data = $stmt->fetchColumn();

        if ($data === false) {
            throw new RuntimeException("Record with id {$id} not found");
        }

        return $data;
    }
}

$storage = new DbStorage($preparedPdo);

$toSave = [
    '1 строка',
    '2 строка',
    '3 строка',
];

// Сохраняем несколько строк
foreach ($toSave as $line) {
    $storage->save($line);
}

foreach ([1,2,3] as $id) {
    var_dump($storage->retrieve($id));
}