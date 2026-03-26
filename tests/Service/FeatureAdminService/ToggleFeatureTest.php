<?php

namespace App\Tests\Service\FeatureAdminService;

use App\Domain\Feature\Feature;
use App\Domain\Feature\FeatureRepositoryInterface;
use App\Domain\Feature\RuleRepositoryInterface;
use App\Application\Feature\FeatureAdminService;
use PHPUnit\Framework\TestCase;

class ToggleFeatureTest extends TestCase
{
    public function testItTogglesEnabled(): void
    {
        $feature = new Feature(1, 'someName', true, [], 10);
        $repoMock = $this->createMock(FeatureRepositoryInterface::class);
        $repoMock->expects($this->once())
            ->method('toggle')
            ->with('someName')
            ->willReturn($feature);

        $service = new FeatureAdminService(
            $repoMock,
            $this->createMock(RuleRepositoryInterface::class)
        );

        $actual = $service->toggleFeature('someName');
        $this->assertInstanceOf(Feature::class, $actual);
        $this->assertTrue($actual->isEnabled());
    }
}
