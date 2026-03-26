<?php

namespace App\Tests\Controller;

use App\Infrastructure\Persistence\DoctrineFeature;

class FeatureControllerTest extends IntegrationTestCase
{

    public function testItReturns400WhenNoUserId(): void
    {
        $this->client->request('GET', '/feature/some_feature', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken,
        ]);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testItReturns404WhenFeatureNotFound(): void
    {
        $this->client->request('GET', '/feature/non_existing?userId=1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken,
        ]);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testItReturns200WithEnabledFeature(): void
    {
        $feature = new DoctrineFeature();
        $feature->setName('test_feature');
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(100);
        $this->getEm()->persist($feature);
        $this->getEm()->flush();

        $this->client->request('GET', '/feature/test_feature?userId=1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken,
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('test_feature', $data['feature']);
        $this->assertTrue($data['enabled']);
    }
}
