<?php

namespace App\Test\Controller;

use App\Domain\Feature\RuleOperator;
use App\Infrastructure\Persistence\DoctrineRule;
use App\Tests\Controller\IntegrationTestCase;
use App\Infrastructure\Persistence\DoctrineFeature;

class FeatureAdminControllerTest extends IntegrationTestCase
{
    public function testItAddsFeature(): void
    {

        $this->client->request('POST', '/feature?userId=1', [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ],json_encode([
                'name' => 'new_feature',
                'enabled' => true,
                'rolloutPercentage' => 50
        ]));

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($data['name'], 'new_feature');
        $added = $this->getEm()->getRepository(DoctrineFeature::class)->findOneBy(['name' => 'new_feature']);
        $this->assertNotNull($added);
    }

    public function testItListsFeatures(): void
    {

        $feature = new DoctrineFeature();
        $feature->setName('test_feature');
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(100);
        $this->getEm()->persist($feature);

        $feature = new DoctrineFeature();
        $feature->setName('test_feature2');
        $feature->setEnabled(false);
        $feature->setRolloutPercentage(50);
        $this->getEm()->persist($feature);

        $this->getEm()->flush();

        $this->client->request('GET', '/feature?userId=1', [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(2, $data);
        $this->assertEquals('test_feature', $data['data'][0]['name']);
        $this->assertEquals('test_feature2', $data['data'][1]['name']);

    }

    public function testItUpdatesFeatures(): void
    {

        $feature = new DoctrineFeature();
        $feature->setName('new_feature');
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(100);
        $this->getEm()->persist($feature);
        $this->getEm()->flush();

        $this->client->request('PATCH', '/feature/new_feature', [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ],json_encode([
                'name' => 'changed_name',
                'enabled' => true,
                'rolloutPercentage' => 50
        ]));

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('changed_name', $data['data']['name']);
        $changed = $this->getEm()->getRepository(DoctrineFeature::class)->findOneBy(['name' => 'changed_name']);
        $this->assertNotNull($changed);
    }

    public function testItRemovesFeature(): void
    {
        $feature = new DoctrineFeature();
        $feature->setName('new_feature');
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(100);
        $this->getEm()->persist($feature);
        $this->getEm()->flush();

        $this->client->request('DELETE', '/feature/new_feature', [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
        $deleted = $this->getEm()->getRepository(DoctrineFeature::class)->findOneBy(['name' => 'new_feature']);
        $this->assertNull($deleted);
    }

    public function testItAddsRule(): void
    {
        $feature = new DoctrineFeature();
        $feature->setName('new_feature');
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(100);
        $this->getEm()->persist($feature);
        $this->getEm()->flush();

        $this->client->request('POST', '/feature/new_feature/rule', [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ],json_encode([
                'type' => 'userId',
                'value' => 50,
                'operator' => 'equals'
        ]));
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
        $this->getEm()->clear();
        $added = $this->getEm()->getRepository(DoctrineFeature::class)->findOneBy(['name' => 'new_feature']);
        $this->assertNotNull($added->getRules());
        $this->assertEquals($added->getRules()->first()->getType(), 'userId');
    }

    public function testItRemovesRule(): void
    {

        $rule = new DoctrineRule();
        $rule->setValue(40);
        $rule->setOperator(RuleOperator::EQUALS);
        $rule->setType('user_id');
        $this->getEm()->persist($rule);
        $feature = new DoctrineFeature();
        $feature->setName('new_feature');
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(100);
        $feature->addRule($rule);
        $this->getEm()->persist($feature);
        $this->getEm()->flush();

        $this->client->request('DELETE', '/feature/new_feature/rule/' . $rule->getId(), [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ]
        );
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
        $this->getEm()->clear();
        $deleted = $this->getEm()->getRepository(DoctrineFeature::class)->findOneBy(['name' => 'new_feature']);
        $this->assertEquals($deleted->getRules()->count(), 0);
    }

    public function testItTogglesEnabled(): void
    {

        $feature = new DoctrineFeature();
        $feature->setName('new_feature');
        $feature->setEnabled(true);
        $feature->setRolloutPercentage(100);
        $this->getEm()->persist($feature);
        $this->getEm()->flush();

        $this->client->request('PATCH', '/feature/new_feature/toggle', [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ]
        );
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
        $this->getEm()->clear();
        $changed = $this->getEm()->getRepository(DoctrineFeature::class)->findOneBy(['name' => 'new_feature']);
        $this->assertFalse($changed->isEnabled());
    }
}
