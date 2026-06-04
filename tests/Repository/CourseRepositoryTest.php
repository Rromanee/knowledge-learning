<?php

namespace App\Tests\Repository;

use App\Entity\Course;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CourseRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    // Tests that a course can be persisted and retrieved
    public function testFindCourseByTitle(): void
    {
        $theme = new Theme();
        $theme->setTitle('Musique');

        $course = new Course();
        $course->setTitle('Initiation à la guitare');
        $course->setPrice(50.0);
        $course->setTheme($theme);

        $this->em->persist($theme);
        $this->em->persist($course);
        $this->em->flush();

        $found = $this->em->getRepository(Course::class)
            ->findOneBy(['title' => 'Initiation à la guitare']);

        $this->assertNotNull($found);
        $this->assertEquals(50.0, $found->getPrice());

        // Cleanup
        $this->em->remove($course);
        $this->em->remove($theme);
        $this->em->flush();
    }

    // Tests that a non-existent course returns null
    public function testCourseNotFoundReturnsNull(): void
    {
        $found = $this->em->getRepository(Course::class)
            ->findOneBy(['title' => 'Cours inexistant']);

        $this->assertNull($found);
    }
}