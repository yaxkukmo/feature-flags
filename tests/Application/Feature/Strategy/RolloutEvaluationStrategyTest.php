<?php

namespace App\Tests\Application\Feature\Strategy;

use App\Domain\Feature\Feature;
use App\Dto\RuleContext;
use App\Application\Feature\Strategy\RolloutEvaluationStrategy;
use PHPUnit\Framework\TestCase;

class RolloutEvaluationStrategyTest extends TestCase
{
    public function testItReturnsFlaseWhenNoRolloutPercentage(): void
    {
        $context = new RuleContext(1, 'pl', 'plan');
        $strategy = new RolloutEvaluationStrategy(
            $context,
            new Feature(id: 1, name: 'someName', rolloutPercentage: null)
        );
        $actual = $strategy->evaluate();
        $this->assertFalse($actual);
    }

    public function testItReturnsFalseWhenRolloutPercentageIsZero(): void
    {
        $context = new RuleContext(1, 'pl', 'plan');
        $strategy = new RolloutEvaluationStrategy(
            $context,
            new Feature(id: 1, name: 'someName', rolloutPercentage: 0)
        );
        $actual = $strategy->evaluate();
        $this->assertFalse($actual);
    }

    public function testItReturnsTrueWhenPercentageIsHundred(): void
    {
        $context = new RuleContext(1, 'pl', 'plan');
        $strategy = new RolloutEvaluationStrategy(
            $context,
            new Feature(id: 1, name: 'someName', rolloutPercentage: 100)
        );
        $actual = $strategy->evaluate();
        $this->assertTrue($actual);
    }

    public function testItReturnsTrueWhenUserIdIsInRange(): void
    {
        $context = new RuleContext(80, 'pl', 'plan');
        $strategy = new RolloutEvaluationStrategy(
            $context,
            new Feature(id: 1, name: 'someName', rolloutPercentage: 81)
        );
        $actual = $strategy->evaluate();
        $this->assertTrue($actual);
    }

    public function testItReturnsFalseWhenUserIdIsOutOfRange(): void
    {
        $context = new RuleContext(83, 'pl', 'plan');
        $strategy = new RolloutEvaluationStrategy(
            $context,
            new Feature(id: 1, name: 'someName', rolloutPercentage: 81)
        );
        $actual = $strategy->evaluate();
        $this->assertFalse($actual);
    }
}
