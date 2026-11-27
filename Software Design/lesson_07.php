<?php

$dateString = "2024-05-13 14:30:00";

try {
    $tz = new DateTimeZone('Europe/Moscow');
    $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dateString, $tz);

    if ($date === false) {
        throw new Exception("Не удалось разобрать дату: " . $dateString);
    }

    echo "Date: " . $date->format('D M d H:i:s T Y') . PHP_EOL;

} catch (Exception $e) {
    logException($e);
    throw new Exception("Проищошка ошибка " . $e->getMessage());

}