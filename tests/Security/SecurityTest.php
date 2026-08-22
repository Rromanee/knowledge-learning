<?php

namespace App\Tests\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
    }

    // Tests that password is stored hashed, never in plain text
    public function testPasswordIsHashed(): void
    {
        $user = new User();
        $user->setEmail('security_test@test.fr');
        $user->setRoles(['ROLE_CLIENT']);
        $user->setPassword($this->hasher->hashPassword($user, 'Password1!'));

        $this->assertNotEquals('Password1!', $user->getPassword());
        $this->assertTrue($this->hasher->isPasswordValid($user, 'Password1!'));
    }

    // Tests that an unverified user cannot be considered active
    public function testUnverifiedUserIsNotActive(): void
    {
        $user = new User();
        $user->setEmail('unverified@test.fr');
        $user->setRoles(['ROLE_CLIENT']);
        $user->setPassword('dummy');
        $user->setIsVerified(false);

        $this->assertFalse($user->isVerified());
    }

    // Tests that roles are correctly assigned
    public function testUserHasCorrectRole(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_CLIENT']);

        $this->assertContains('ROLE_CLIENT', $user->getRoles());
        $this->assertNotContains('ROLE_ADMIN', $user->getRoles());
    }
}