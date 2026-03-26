<?php

namespace App\Tests\Service\FeatureAdminService;

use App\Domain\Feature\Feature;
use App\Domain\Feature\FeatureRepositoryInterface;
use App\Domain\Feature\RuleRepositoryInterface;
use App\Dto\UpdateFeature;
use App\Application\Feature\FeatureAdminService;
use PHPUnit\Framework\TestCase;

class UpdateFeatureTest extends TestCase
{
    public function testItUpdatesFeature(): void
    {
        $updateValues = new UpdateFeature(name: 'changedName', rolloutPercentage: 90, enabled: true);
        $repoMock = $this->createMock(FeatureRepositoryInterface::class);
        $repoMock->expects($this->once())
            ->method('save')
            ->with($this->equalTo('someName'), new Feature(
                id: null,
                name: 'changedName',
                enabled: true,
                rolloutPercentage: 90
            ))
            ->willReturn(new Feature(1, 'changedName', true, [], 90));
        $service = new FeatureAdminService(
            $repoMock,
            $this->createMock(RuleRepositoryInterface::class)
        );
        $feature = $service->update('someName', $updateValues);
        $this->assertInstanceOf(Feature::class, $feature);
        $this->assertEquals('changedName', $feature->name());
    }
}
