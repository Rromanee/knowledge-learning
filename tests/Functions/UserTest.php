<?php

namespace App\Tests\Functional;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    // Tests that a user is created with correct email and role
    public function testUserRegistration(): void
    {
        $user = new User();
        $user->setEmail('test@test.fr');
        $user->setRoles(['ROLE_CLIENT']);
        $user->setPassword('hashed_password');

        $this->assertEquals('test@test.fr', $user->getEmail());
        $this->assertContains('ROLE_CLIENT', $user->getRoles());
        $this->assertNotNull($user->getPassword());
    }

    // Tests that email verification activates the account
    public function testEmailVerification(): void
    {
        $user = new User();
        $user->setIsVerified(false);

        $this->assertFalse($user->isVerified());

        $user->setIsVerified(true);

        $this->assertTrue($user->isVerified());
    }
}