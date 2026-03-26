<?php

namespace App\Tests\Service\FeatureAdminService;

use App\Domain\Feature\Exception\FeatureNotFoundException;
use App\Domain\Feature\FeatureRepositoryInterface;
use App\Domain\Feature\RuleRepositoryInterface;
use App\Application\Feature\FeatureAdminService;
use PHPUnit\Framework\TestCase;


class RemoveFeatureTest extends TestCase
{
    public function testItRemoveFeature(): void
    {
        $repoMock = $this->createMock(FeatureRepositoryInterface::class);
        $repoMock->expects($this->once())
            ->method('delete')
            ->with('someName');

        $service = new FeatureAdminService(
            $repoMock,
            $this->createMock(RuleRepositoryInterface::class)
        );
        $service->remove('someName');
    }

    public function testItThrowsException(): void
    {
        $repoMock = $this->createMock(FeatureRepositoryInterface::class);
        $repoMock->expects($this->once())
            ->method('delete')
            ->willThrowException(new FeatureNotFoundException('someName'));

        $service = new FeatureAdminService(
            $repoMock,
            $this->createMock(RuleRepositoryInterface::class)
        );
        $this->expectException(FeatureNotFoundException::class);
        $service->remove('someName');
    }
}
