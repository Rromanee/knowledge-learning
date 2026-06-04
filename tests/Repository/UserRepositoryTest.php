<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Entity\Course;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        // Cleanup before each test
        $existing = $this->em->getRepository(User::class)->findOneBy(['email' => 'repo_test@test.fr']);
        if ($existing) {
            $this->em->remove($existing);
            $this->em->flush();
        }
    }

    // Tests that a user can be found by email
    public function testFindUserByEmail(): void
    {
        $user = new User();
        $user->setEmail('repo_test@test.fr');
        $user->setRoles(['ROLE_CLIENT']);
        $user->setPassword('dummy');

        $this->em->persist($user);
        $this->em->flush();

        $found = $this->em->getRepository(User::class)
            ->findOneBy(['email' => 'repo_test@test.fr']);

        $this->assertNotNull($found);
        $this->assertEquals('repo_test@test.fr', $found->getEmail());
    }

    // Tests that a non-existent user returns null
    public function testUserNotFoundReturnsNull(): void
    {
        $found = $this->em->getRepository(User::class)
            ->findOneBy(['email' => 'nobody@test.fr']);

        $this->assertNull($found);
    }

    protected function tearDown(): void
    {
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => 'repo_test@test.fr']);

        if ($user) {
            $this->em->remove($user);
            $this->em->flush();
        }

        parent::tearDown();
    }
}