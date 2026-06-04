<?php

namespace App\Tests\Functions;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthTest extends WebTestCase
{
    // Tests that a verified user can log in successfully
    public function testVerifiedUserCanLogin(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);
        
        // Cleanup before — in case a previous test left data
        $existing = $em->getRepository(User::class)->findOneBy(['email' => 'auth_test@test.fr']);
        if ($existing) {
            $em->remove($existing);
            $em->flush();
        }

        // Create a verified test user
        $user = new User();
        $user->setEmail('auth_test@test.fr');
        $user->setRoles(['ROLE_CLIENT']);
        $user->setPassword($hasher->hashPassword($user, 'Password1!'));
        $user->setIsVerified(true);

        $em->persist($user);
        $em->flush();

        // Submit login form
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email'    => 'auth_test@test.fr',
            'password' => 'Password1!',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

    }
}