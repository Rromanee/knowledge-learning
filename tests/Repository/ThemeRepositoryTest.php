<?php

namespace App\Tests\Repository;

use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ThemeRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    // Tests that a theme can be persisted and retrieved
    public function testFindThemeByTitle(): void
    {
        $title = 'Theme Repository Test ' . uniqid();

        $theme = new Theme();
        $theme->setTitle($title);

        $this->em->persist($theme);
        $this->em->flush();

        $found = $this->em
            ->getRepository(Theme::class)
            ->findOneBy(['title' => $title]);

        $this->assertNotNull($found);
        $this->assertEquals($title, $found->getTitle());

        // Cleanup
        $this->em->remove($found);
        $this->em->flush();
    }

    // Tests that a non-existent theme returns null
    public function testThemeNotFoundReturnsNull(): void
    {
        $found = $this->em
            ->getRepository(Theme::class)
            ->findOneBy([
                'title' => 'Theme That Does Not Exist ' . uniqid(),
            ]);

        $this->assertNull($found);
    }
}