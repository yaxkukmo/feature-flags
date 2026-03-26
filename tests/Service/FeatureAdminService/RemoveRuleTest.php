<?php

namespace App\Tests\Service\FeatureAdminService;

use App\Application\Feature\FeatureAdminService;
use App\Domain\Feature\Exception\RuleNotFoundException;
use App\Domain\Feature\FeatureRepositoryInterface;
use App\Domain\Feature\RuleRepositoryInterface;
use PHPUnit\Framework\TestCase;

class RemoveRuleTest extends TestCase
{
    public function testItRemovesRule(): void
    {
        $ruleRepoMock = $this->createMock(RuleRepositoryInterface::class);
        $ruleRepoMock->expects($this->once())
            ->method('delete')
            ->with(1);
        $service = new FeatureAdminService(
            $this->createMock(FeatureRepositoryInterface::class),
            $ruleRepoMock
        );
        $service->removeRule(1);
    }

    public function testItThrowsException(): void
    {

        $ruleRepoMock = $this->createMock(RuleRepositoryInterface::class);
        $ruleRepoMock->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willThrowException(new RuleNotFoundException(1));
        $service = new FeatureAdminService(
            $this->createMock(FeatureRepositoryInterface::class),
            $ruleRepoMock
        );
        $this->expectException(RuleNotFoundException::class);
        $service->removeRule(1);
    }
}
