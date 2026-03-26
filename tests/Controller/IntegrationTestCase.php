<?php

namespace App\Tests\Controller;

use App\Infrastructure\Persistence\DoctrineFeature;
use App\Infrastructure\Persistence\DoctrineUser;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class IntegrationTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected string $userToken;
    protected string $adminToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);
        $jwtManager = $container->get(JWTTokenManagerInterface::class);

        $this->userToken = $jwtManager->create($this->ensureUser($em, $hasher, 'user@test.com', ['ROLE_USER']));
        $this->adminToken = $jwtManager->create($this->ensureUser($em, $hasher, 'admin@test.com', ['ROLE_ADMIN']));
    }

    protected function tearDown(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->createQuery('DELETE FROM App\Infrastructure\Persistence\DoctrineRule')->execute();
        $em->createQuery('DELETE FROM App\Infrastructure\Persistence\DoctrineFeature')->execute();
        parent::tearDown();
    }

    private function ensureUser(EntityManagerInterface $em, UserPasswordHasherInterface $hasher, string $email, array $roles): DoctrineUser
    {
        $existing = $em->getRepository(DoctrineUser::class)->findOneBy(['email' => $email]);
        if ($existing) {
            return $existing;
        }

        $user = new DoctrineUser();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword($hasher->hashPassword($user, 'password'));
        $em->persist($user);
        $em->flush();
        return $user;
    }

    protected function getEm(): EntityManagerInterface
    {
        return static::getContainer()->get(EntityManagerInterface::class);
    }
}
