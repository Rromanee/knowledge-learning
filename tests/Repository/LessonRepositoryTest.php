<?php

namespace App\Tests\Repository;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LessonRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    // Tests that a lesson can be persisted and retrieved
    public function testFindLessonByTitle(): void
    {
        $theme = new Theme();
        $theme->setTitle('Musique test');

        $course = new Course();
        $course->setTitle('Guitare test');
        $course->setPrice(50.0);
        $course->setTheme($theme);

        $lesson = new Lesson();
        $lesson->setTitle('Les accords');
        $lesson->setPrice(26.0);
        $lesson->setContent('Contenu de la leçon');
        $lesson->setVideoUrl('https://youtube.com/test');
        $lesson->setCourse($course);

        $this->em->persist($theme);
        $this->em->persist($course);
        $this->em->persist($lesson);
        $this->em->flush();

        $found = $this->em->getRepository(Lesson::class)
            ->findOneBy(['title' => 'Les accords']);

        $this->assertNotNull($found);
        $this->assertEquals(26.0, $found->getPrice());

        // Cleanup
        $this->em->remove($lesson);
        $this->em->remove($course);
        $this->em->remove($theme);
        $this->em->flush();
    }

    // Tests that a non-existent lesson returns null
    public function testLessonNotFoundReturnsNull(): void
    {
        $found = $this->em->getRepository(Lesson::class)
            ->findOneBy(['title' => 'Leçon inexistante']);

        $this->assertNull($found);
    }
}