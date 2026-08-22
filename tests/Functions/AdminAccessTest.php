<?php

namespace App\Tests\Functions;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminAccessTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();

        $this->em = $container->get(EntityManagerInterface::class);
        $this->hasher = $container->get(UserPasswordHasherInterface::class);

        $this->removeTestUsers();
    }

    /**
     * Tests that an administrator can access the admin area.
     */
    public function testAdminCanAccessAdminArea(): void
    {
        $admin = new User();

        $admin->setEmail('admin_access_test@test.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->hasher->hashPassword($admin, 'Password1!')
        );
        $admin->setIsVerified(true);

        $this->em->persist($admin);
        $this->em->flush();

        $this->client->loginUser($admin);

        $this->client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Tests that a regular client cannot access the admin area.
     */
    public function testClientCannotAccessAdminArea(): void
    {
        $user = new User();

        $user->setEmail('client_access_test@test.fr');
        $user->setRoles(['ROLE_CLIENT']);
        $user->setPassword(
            $this->hasher->hashPassword($user, 'Password1!')
        );
        $user->setIsVerified(true);

        $this->em->persist($user);
        $this->em->flush();

        $this->client->loginUser($user);

        $this->client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Tests that an unauthenticated user cannot access the admin area.
     */
    public function testUnauthenticatedUserCannotAccessAdminArea(): void
    {
        $this->client->request('GET', '/admin');

        $response = $this->client->getResponse();

        $this->assertTrue(
            $response->isRedirection()
            || $response->getStatusCode() === 403
        );
    }

    /**
     * Removes users created by previous test executions.
     */
    private function removeTestUsers(): void
    {
        $emails = [
            'admin_access_test@test.fr',
            'client_access_test@test.fr',
        ];

        foreach ($emails as $email) {
            $user = $this->em
                ->getRepository(User::class)
                ->findOneBy(['email' => $email]);

            if ($user !== null) {
                $this->em->remove($user);
            }
        }

        $this->em->flush();
        $this->em->clear();
    }

    protected function tearDown(): void
    {
        if ($this->em->isOpen()) {
            $this->removeTestUsers();
        }

        parent::tearDown();
    }
}