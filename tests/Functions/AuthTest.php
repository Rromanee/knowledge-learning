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

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        /** @var UserPasswordHasherInterface $hasher */
        $hasher = $container->get(UserPasswordHasherInterface::class);

        // Cleanup before the test
        $existing = $em
            ->getRepository(User::class)
            ->findOneBy([
                'email' => 'auth_test@test.fr',
            ]);

        if ($existing) {
            $em->remove($existing);
            $em->flush();
        }

        // Create a verified test user
        $user = new User();
        $user->setEmail('auth_test@test.fr');
        $user->setRoles(['ROLE_CLIENT']);
        $user->setPassword(
            $hasher->hashPassword($user, 'Password1!')
        );
        $user->setIsVerified(true);

        $em->persist($user);
        $em->flush();

        // Open the login page
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();

        // Submit the real login form
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'auth_test@test.fr',
            'password' => 'Password1!',
        ]);

        $client->submit($form);

        // Successful authentication redirects to the home page
        $this->assertResponseRedirects('/');

        $client->followRedirect();

        $this->assertResponseIsSuccessful();
    }
}