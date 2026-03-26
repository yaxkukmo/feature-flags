<?php

namespace App\Tests\Service\FeatureAdminService;

use App\Domain\Feature\Feature;
use App\Domain\Feature\FeatureRepositoryInterface;
use App\Domain\Feature\RuleRepositoryInterface;
use App\Dto\FeatureQuery;
use App\Application\Feature\FeatureAdminService;
use PHPUnit\Framework\TestCase;

class ListFeaturesTest extends TestCase
{
    public function testItReturnsEmptyArrayWhenNoFeatures(): void
    {
        $featureQuery = new FeatureQuery;
        $repoMock = $this->createMock(FeatureRepositoryInterface::class);
        $repoMock->expects($this->once())
            ->method('findPaginated')
            ->with($featureQuery)
            ->willReturn([]);

        $service = new FeatureAdminService(
            $repoMock,
            $this->createMock(RuleRepositoryInterface::class)
        );
        $actual = $service->list($featureQuery);
        $this->assertEmpty($actual);
    }

    public function testItReturnsFeaturesWhenTheyExist(): void
    {
        $featureQueryMock = $this->createMock(FeatureQuery::class);
        $repoMock = $this->createMock(FeatureRepositoryInterface::class);
        $repoMock->expects($this->once())
            ->method('findPaginated')
            ->with($featureQueryMock)
            ->willReturn([
                new Feature(1, 'someName', true, [], 10),
                new Feature(2, 'someName2', true, [], 11),
            ]);

        $service = new FeatureAdminService(
            $repoMock,
            $this->createMock(RuleRepositoryInterface::class)
        );
        $actual = $service->list($featureQueryMock);
        $this->assertCount(2, $actual);
        $this->assertContainsOnlyInstancesOf(Feature::class, $actual);
    }
}
