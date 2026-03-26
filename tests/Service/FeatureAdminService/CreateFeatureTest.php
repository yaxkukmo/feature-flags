<?php

namespace App\Tests\Service\FeatureAdminService;

use App\Domain\Feature\FeatureRepositoryInterface;
use App\Domain\Feature\RuleRepositoryInterface;
use App\Dto\CreateFeature;
use App\Domain\Feature\Feature;
use App\Application\Feature\FeatureAdminService;
use PHPUnit\Framework\TestCase;

class CreateFeatureTest extends TestCase
{
    public function testItCreatesNewFeature() {
        $createFeature = new CreateFeature(
            name: 'someName',
            rolloutPercentage: 10,
            enabled: true
        );
        $repoMock = $this->createMock(FeatureRepositoryInterface::class);
        $repoMock->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo('someName'),
                $this->callback(
                    fn(Feature $item) =>
                    $item->name() === 'someName' && $item->isEnabled() === true
                )
            )
            ->willReturn(new Feature(1, 'someName', true, [], 10));
        $service = new FeatureAdminService(
            $repoMock,
            $this->createMock(RuleRepositoryInterface::class)
        );
        $actual = $service->create($createFeature);
        $this->assertInstanceOf(Feature::class, $actual);
        $this->assertSame('someName', $actual->name());
        $this->assertTrue($actual->isEnabled());
    }
}
