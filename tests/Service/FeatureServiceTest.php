<?php

namespace App\Tests\Service;

use App\Dto\RuleContext;
use App\Entity\Feature;
use App\Entity\Rule;
use App\Enum\RuleOperator;
use App\Repository\FeatureRepository;
use App\Service\FeatureService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FeatureServiceTest extends TestCase
{
    #[Test]
    public function returnsFalseWhenFeatureNotEnabled() {
        $feature = new Feature;
        $feature->setEnabled(false);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 1)));
    }

    #[Test]
    public function returnsFalseWhenNoRulesAndRolloutPercentageIsNull() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(null);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 1)));
    }

    #[Test]
    public function returnsFalseWhenNoRulesAndRolloutPercentageIsZero() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(0);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 1)));
    }

    #[Test]
    public function returnsTrueWhenNoRulesAndRolloutPercentageIs100() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(100);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertTrue($service->isEnabled('someName', new RuleContext(userId: 1)));
    }

    #[Test]
    public function returnsTrueWhenNoRulesAndUserIdIsInRolloutPercentageRange() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(10);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertTrue($service->isEnabled('someName', new RuleContext(userId: 109)));
    }

    #[Test]
    public function returnsFalseWhenNoRulesAndUserIdIsNotInRolloutPercentageRange() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(10);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 119)));
    }

    #[Test]
    public function returnsFalseWhenRulesNotMetEqualCondition() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(10);
        $rule = new Rule();
        $rule->setType('user_id');
        $rule->setValue(99);
        $rule->setOperator(RuleOperator::EQUALS);
        $feature->addRule($rule);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 119)));
    }

    #[Test]
    public function returnsTrueWhenRulesMetEqualCondition() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(10);
        $rule = new Rule();
        $rule->setType('user_id');
        $rule->setValue(99);
        $rule->setOperator(RuleOperator::EQUALS);
        $feature->addRule($rule);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertTrue($service->isEnabled('someName', new RuleContext(userId: 99)));
    }

    #[Test]
    public function returnsFalseWhenRulesNotMetGreaterThanCondition() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(10);
        $rule = new Rule();
        $rule->setType('user_id');
        $rule->setValue(99);
        $rule->setOperator(RuleOperator::GREATER_THAN);
        $feature->addRule($rule);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 99)));
    }

    #[Test]
    public function returnsTrueWhenRulesMetGreaterThanCondition() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(10);
        $rule = new Rule();
        $rule->setType('user_id');
        $rule->setValue(99);
        $rule->setOperator(RuleOperator::GREATER_THAN);
        $feature->addRule($rule);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertTrue($service->isEnabled('someName', new RuleContext(userId: 100)));
    }

    #[Test]
    public function returnsFalseWhenRulesNotMetLessThanCondition() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(10);
        $rule = new Rule();
        $rule->setType('user_id');
        $rule->setValue(99);
        $rule->setOperator(RuleOperator::LESS_THAN);
        $feature->addRule($rule);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertFalse($service->isEnabled('someName', new RuleContext(userId: 100)));
    }

    #[Test]
    public function returnsTrueWhenRulesMetLessThanCondition() {
        $feature = new Feature;
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(10);
        $rule = new Rule();
        $rule->setType('user_id');
        $rule->setValue(99);
        $rule->setOperator(RuleOperator::LESS_THAN);
        $feature->addRule($rule);
        $repoMock = $this->createMock(FeatureRepository::class);
        $repoMock->expects($this->once())->method('findByNameWithRules')
            ->willReturn($feature);
        $service = new FeatureService($repoMock);
        $this->assertTrue($service->isEnabled('someName', new RuleContext(userId: 98)));
    }
}
