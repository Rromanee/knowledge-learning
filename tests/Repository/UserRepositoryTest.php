<?php

namespace App\Tests\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    // Tests that a user can be found by email
    public function testFindUserByEmail(): void
    {
        $email = 'repository-test-' . uniqid() . '@test.fr';

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_CLIENT']);
        $user->setPassword('dummy');
        $user->setIsVerified(true);

        $this->em->persist($user);
        $this->em->flush();

        $found = $this->em
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        $this->assertNotNull($found);
        $this->assertEquals($email, $found->getEmail());
        $this->assertContains('ROLE_CLIENT', $found->getRoles());

        // Cleanup
        $this->em->remove($found);
        $this->em->flush();
    }

    // Tests that a non-existent user returns null
    public function testUserNotFoundReturnsNull(): void
    {
        $found = $this->em
            ->getRepository(User::class)
            ->findOneBy([
                'email' => 'user-that-does-not-exist-' . uniqid() . '@test.fr',
            ]);

        $this->assertNull($found);
    }
}