<?php

namespace App\Tests\Service;

use App\Dto\RuleContext;
use App\Domain\Feature\Feature;
use App\Domain\Feature\Rule;
use App\Domain\Feature\RuleOperator;
use App\Infrastructure\Persistence\DoctrineFeatureRepository as FeatureRepository;
use App\Service\FeatureService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FeatureServiceTest extends TestCase
{
    #[Test]
    public function returnsFalseWhenFeatureNotEnabled() {
        $feature = new Feature(1, 'someName', false);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 1)));
    }

    #[Test]
    public function returnsFalseWhenNoRulesAndRolloutPercentageIsNull() {
        $feature = new Feature(1, 'someName', true);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 1)));
    }

    #[Test]
    public function returnsFalseWhenNoRulesAndRolloutPercentageIsZero() {
        $feature = new Feature(1, 'someName', true, [], 0);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 1)));
    }

    #[Test]
    public function returnsTrueWhenNoRulesAndRolloutPercentageIs100() {
        $feature = new Feature(1, 'someName', true, [], 100);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertTrue($service->isEnabled('someName', new RuleContext(userId: 1)));
    }

    #[Test]
    public function returnsTrueWhenNoRulesAndUserIdIsInRolloutPercentageRange() {
        $feature = new Feature(1, 'someName', true, [], 10);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertTrue($service->isEnabled('someName', new RuleContext(userId: 109)));
    }

    #[Test]
    public function returnsFalseWhenNoRulesAndUserIdIsNotInRolloutPercentageRange() {
        $feature = new Feature(1, 'someName', true, [], 10);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 119)));
    }

    #[Test]
    public function returnsFalseWhenRulesNotMetEqualCondition() {
        $rule = new Rule(1, 'user_id', 99, RuleOperator::EQUALS);
        $feature = new Feature(1, 'someName', true, [$rule], 10);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 119)));
    }

    #[Test]
    public function returnsTrueWhenRulesMetEqualCondition() {
        $rule = new Rule(1, 'user_id', 99, RuleOperator::EQUALS);
        $feature = new Feature(1, 'someName', true, [$rule], 10);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertTrue($service->isEnabled('someName', new RuleContext(userId: 99)));
    }

    #[Test]
    public function returnsFalseWhenRulesNotMetGreaterThanCondition() {
        $rule = new Rule(1, 'user_id', 99, RuleOperator::GREATER_THAN);
        $feature = new Feature(1, 'someName', true, [$rule], 10);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 99)));
    }

    #[Test]
    public function returnsTrueWhenRulesMetGreaterThanCondition() {
        $rule = new Rule(1, 'user_id', 99, RuleOperator::GREATER_THAN);
        $feature = new Feature(1, 'someName', true, [$rule], 10);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertTrue($service->isEnabled('someName', new RuleContext(userId: 100)));
    }

    #[Test]
    public function returnsFalseWhenRulesNotMetLessThanCondition() {
        $rule = new Rule(1, 'user_id', 99, RuleOperator::LESS_THAN);
        $feature = new Feature(1, 'someName', true, [$rule], 10);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 100)));
    }

    #[Test]
    public function returnsTrueWhenRulesMetLessThanCondition() {
        $rule = new Rule(1, 'user_id', 99, RuleOperator::LESS_THAN);
        $feature = new Feature(1, 'user_id', true, [$rule], 10);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertTrue($service->isEnabled('someName', new RuleContext(userId: 98)));
    }
}
