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
        $lessonTitle = 'Lesson Repository Test ' . uniqid();

        $theme = new Theme();
        $theme->setTitle('Theme Lesson Test ' . uniqid());

        $course = new Course();
        $course->setTitle('Course Lesson Test ' . uniqid());
        $course->setPrice(50.0);
        $course->setTheme($theme);

        $lesson = new Lesson();
        $lesson->setTitle($lessonTitle);
        $lesson->setPrice(26.0);
        $lesson->setContent('Test lesson content');
        $lesson->setVideoUrl('https://youtube.com/test');
        $lesson->setCourse($course);

        $this->em->persist($theme);
        $this->em->persist($course);
        $this->em->persist($lesson);
        $this->em->flush();

        $found = $this->em
            ->getRepository(Lesson::class)
            ->findOneBy(['title' => $lessonTitle]);

        $this->assertNotNull($found);
        $this->assertEquals(26.0, $found->getPrice());
        $this->assertEquals('Test lesson content', $found->getContent());

        // Cleanup
        $this->em->remove($lesson);
        $this->em->remove($course);
        $this->em->remove($theme);
        $this->em->flush();
    }

    // Tests that a non-existent lesson returns null
    public function testLessonNotFoundReturnsNull(): void
    {
        $found = $this->em
            ->getRepository(Lesson::class)
            ->findOneBy([
                'title' => 'Lesson That Does Not Exist ' . uniqid(),
            ]);

        $this->assertNull($found);
    }
}