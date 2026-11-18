<?php

use PHPUnit\Framework\TestCase;

final class AverageCalculator
{
    /**
     * @param int[] $numbers
     */
    public function calculateAverage(array $numbers): float
    {
        return array_sum($numbers) / count($numbers);
    }
}

final class AverageCalculatorTest extends TestCase
{
    private AverageCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new AverageCalculator();
    }

    public function testAverageOfSeveralNumbers(): void
    {
        $this->assertSame(3.0, $this->calculator->calculateAverage([1, 2, 3, 4, 5]));
    }

    public function testAverageOfSingleNumber(): void
    {
        $this->assertSame(7.0, $this->calculator->calculateAverage([7]));
    }

    public function testAverageWithNegativeNumbers(): void
    {
        $this->assertSame(0.0, $this->calculator->calculateAverage([-2, 2]));
    }

    public function testAverageIsFractional(): void
    {
        $this->assertEqualsWithDelta(2.5, $this->calculator->calculateAverage([1, 2, 3, 4]), 0.0001);
    }

    public function testEmptyArrayExposesHiddenBug(): void
    {
        $this->expectException(DivisionByZeroError::class);

        $this->calculator->calculateAverage([]); //тест проходит, покрытие есть, баг так же есть
    }
}