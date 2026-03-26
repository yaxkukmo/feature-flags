<?php

namespace App\Tests\Service\FeatureAdminService;

use App\Application\Feature\FeatureAdminService;
use App\Domain\Feature\Exception\FeatureNotFoundException;
use App\Domain\Feature\Feature;
use App\Domain\Feature\FeatureRepositoryInterface;
use App\Domain\Feature\Rule;
use App\Domain\Feature\RuleOperator;
use App\Domain\Feature\RuleRepositoryInterface;
use App\Dto\CreateRule;
use PHPUnit\Framework\TestCase;

class AddRuleTest extends TestCase
{
    public function testItAddsRule(): void
    {
        $createRule = new CreateRule(
            type: 'userId',
            value: 99,
            operator: RuleOperator::EQUALS
        );
        $ruleRepoMock = $this->createMock(RuleRepositoryInterface::class);
        $ruleRepoMock->expects($this->once())
            ->method('save')
            ->with(
                new Rule(
                    $createRule->type,
                    $createRule->value,
                    $createRule->operator
                ),
                'someName'
            );
        $feature = new Feature(
            id: 1,
            enabled: true,
            name: 'someName',
            rolloutPercentage: 80,
            rules: [
                new Rule('userId', 99, RuleOperator::EQUALS, 1)
            ]
        );

        $featureRepoMock = $this->createMock(FeatureRepositoryInterface::class);
        $featureRepoMock->expects($this->once())
            ->method('findByNameWithRules')
            ->with('someName')
            ->willReturn($feature);

        $service = new FeatureAdminService(
            $featureRepoMock,
            $ruleRepoMock
        );
        $actual = $service->addRule('someName', $createRule);
        $this->assertCount(1, $actual->rules());
        $this->assertEquals('userId', $actual->rules()[0]->type());
        $this->assertEquals(RuleOperator::EQUALS, $actual->rules()[0]->operator());
        $this->assertEquals(99, $actual->rules()[0]->value());
    }

    public function testItThrowsException(): void
    {
        $featureRepoMock = $this->createMock(FeatureRepositoryInterface::class);
        $featureRepoMock->expects($this->once())
            ->method('findByNameWithRules')
            ->willReturn(null);
        $service = new FeatureAdminService(
            $featureRepoMock,
            $this->createMock(RuleRepositoryInterface::class)
        );
        $this->expectException(FeatureNotFoundException::class);

        $createRule = new CreateRule(
            type: 'userId',
            value: 99,
            operator: RuleOperator::EQUALS
        );
        $actual = $service->addRule('someName', $createRule);
    }
}
