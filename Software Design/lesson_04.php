<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;


final class GradeCalculator
{
    public const MIN_GRADE = 0;
    public const MAX_GRADE = 100;


    public function calculateAverage(array $grades): float
    {
        if ($grades === []) {
            throw new InvalidArgumentException('Список оценок не может быть пустым');
        }

        $sum = 0.0;

        foreach ($grades as $grade) {
            if (!is_int($grade) && !is_float($grade)) {
                throw new InvalidArgumentException(
                    sprintf('Оценка должна быть числом, получено: %s', get_debug_type($grade))
                );
            }

            // NaN не поймать сравнениями: любое сравнение с NaN даёт false
            if (is_float($grade) && !is_finite($grade)) {
                throw new InvalidArgumentException('Оценка не может быть NaN или INF');
            }

            if ($grade < self::MIN_GRADE || $grade > self::MAX_GRADE) {
                throw new InvalidArgumentException(
                    sprintf('Оценка вне диапазона [%d, %d]: %s', self::MIN_GRADE, self::MAX_GRADE, $grade)
                );
            }

            $sum += $grade;
        }

        return $sum / count($grades);
    }
}

final class GradeCalculatorTest extends TestCase
{
    private GradeCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new GradeCalculator();
    }

    // 1 Нормальный сценарий: типичные данные, дробный результат.
    public function testReturnsFractionalAverageForTypicalGrades(): void
    {
        self::assertEqualsWithDelta(85.0, $this->calculator->calculateAverage([70, 85, 90, 95]), 1e-9);
        self::assertEqualsWithDelta(7.5, $this->calculator->calculateAverage([7, 8]), 1e-9);
    }

    // 2 Минимальный вход: единственный элемент.
    public function testReturnsTheGradeItselfForSingleElement(): void
    {
        self::assertEqualsWithDelta(42.0, $this->calculator->calculateAverage([42]), 1e-9);
    }

    // 3 Граничные значения
    public function testAcceptsBoundaryGradesOfTheScale(): void
    {
        self::assertEqualsWithDelta(0.0, $this->calculator->calculateAverage([0, 0, 0]), 1e-9);
        self::assertEqualsWithDelta(100.0, $this->calculator->calculateAverage([100, 100]), 1e-9);
        self::assertEqualsWithDelta(50.0, $this->calculator->calculateAverage([0, 100]), 1e-9);
    }

    // 4. Структурно некорректный вход: пустой массив и элементы не числового типа.
    /**
     * @param array<int|string, mixed> $grades
     */
    #[DataProvider('structurallyInvalidInputProvider')]
    public function testRejectsEmptyAndNonNumericInput(array $grades): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->calculator->calculateAverage($grades);
    }

    /** @return array<string, array{array<int|string, mixed>}> */
    public static function structurallyInvalidInputProvider(): array
    {
        return [
            'пустой массив' => [[]],
            'null-элемент' => [[80, null, 90]],
            'строка вместо числа' => [[80, '90', 70]],
            'bool вместо числа' => [[80, true]],
        ];
    }

    // 5 Некорректные значения вне диапазона
    #[DataProvider('invalidValuesProvider')]
    public function testRejectsGradesOutsideValidRange(array $grades): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->calculator->calculateAverage($grades);
    }

    /** @return array<string, array{array<int, int|float>}> */
    public static function invalidValuesProvider(): array
    {
        return [
            'ниже минимума' => [[80, -1, 90]],
            'выше максимума' => [[80, 101]],
            'NaN' => [[80, NAN]],
            'INF' => [[80, INF]],
        ];
    }
}