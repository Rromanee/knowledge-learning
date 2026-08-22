<?php

namespace App\Tests\Functions;

use App\Entity\Certification;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\LessonValidation;
use App\Entity\Theme;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LessonValidationTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private KernelBrowser $client;

    private ?int $userId = null;
    private ?int $themeId = null;
    private ?int $courseId = null;

    /**
     * @var int[]
     */
    private array $lessonIds = [];

    protected function setUp(): void
    {
        /*
         * Important:
         * createClient() must be called before accessing the container.
         * This avoids the "kernel should only be booted once" error.
         */
        $this->client = static::createClient();

        $container = static::getContainer();

        $this->em = $container->get(EntityManagerInterface::class);

        /*
         * Clean any previous test data that may remain
         * from an interrupted PHPUnit run.
         */
        $this->cleanupPreviousTestData();

        /*
         * Create Theme.
         */
        $theme = new Theme();
        $theme->setTitle('Thème de test');

        /*
         * Create Course.
         */
        $course = new Course();
        $course->setTitle('Cursus de test');
        $course->setPrice(50.0);
        $course->setTheme($theme);

        /*
         * Create first lesson.
         */
        $lesson1 = new Lesson();
        $lesson1->setTitle('Leçon de test 1');
        $lesson1->setPrice(20.0);
        $lesson1->setContent('Contenu de la première leçon.');
        $lesson1->setVideoUrl('https://youtube.com/test1');
        $lesson1->setCourse($course);

        /*
         * Create second lesson.
         */
        $lesson2 = new Lesson();
        $lesson2->setTitle('Leçon de test 2');
        $lesson2->setPrice(20.0);
        $lesson2->setContent('Contenu de la deuxième leçon.');
        $lesson2->setVideoUrl('https://youtube.com/test2');
        $lesson2->setCourse($course);

        /*
         * Create test user.
         */
        $user = new User();
        $user->setEmail('lesson_validation_test@test.fr');
        $user->setRoles(['ROLE_CLIENT']);
        $user->setPassword('dummy_password');
        $user->setIsVerified(true);

        /*
         * Persist all test entities.
         */
        $this->em->persist($theme);
        $this->em->persist($course);
        $this->em->persist($lesson1);
        $this->em->persist($lesson2);
        $this->em->persist($user);

        $this->em->flush();

        /*
         * Store IDs instead of keeping entity references.
         * This prevents detached-entity problems during tearDown().
         */
        $this->userId = $user->getId();
        $this->themeId = $theme->getId();
        $this->courseId = $course->getId();

        $this->lessonIds = [
            $lesson1->getId(),
            $lesson2->getId(),
        ];
    }

    /**
     * Tests that a lesson can be validated by the authenticated user.
     */
    public function testLessonCanBeValidated(): void
    {
        $user = $this->em->getRepository(User::class)->find($this->userId);
        $lesson = $this->em->getRepository(Lesson::class)->find($this->lessonIds[0]);

        $this->client->loginUser($user);

        $this->client->request(
            'POST',
            '/lesson/' . $lesson->getId() . '/validate'
        );

        $this->assertResponseRedirects(
            '/lesson/' . $lesson->getId()
        );

        /*
         * Reload the validation from the database.
         */
        $this->em->clear();

        $user = $this->em->getRepository(User::class)->find($this->userId);
        $lesson = $this->em->getRepository(Lesson::class)->find($this->lessonIds[0]);

        $validation = $this->em
            ->getRepository(LessonValidation::class)
            ->findOneBy([
                'user' => $user,
                'lesson' => $lesson,
            ]);

        $this->assertNotNull($validation);
        $this->assertNotNull($validation->getValidatedAt());
    }

    /**
     * Tests that a lesson cannot be validated twice
     * for the same user.
     */
    public function testLessonCannotBeValidatedTwice(): void
    {
        $user = $this->em->getRepository(User::class)->find($this->userId);
        $lesson = $this->em->getRepository(Lesson::class)->find($this->lessonIds[0]);

        $this->client->loginUser($user);

        $this->client->request(
            'POST',
            '/lesson/' . $lesson->getId() . '/validate'
        );

        $this->client->request(
            'POST',
            '/lesson/' . $lesson->getId() . '/validate'
        );

        /*
         * Reload entities from the database.
         */
        $this->em->clear();

        $user = $this->em->getRepository(User::class)->find($this->userId);
        $lesson = $this->em->getRepository(Lesson::class)->find($this->lessonIds[0]);

        $validations = $this->em
            ->getRepository(LessonValidation::class)
            ->findBy([
                'user' => $user,
                'lesson' => $lesson,
            ]);

        $this->assertCount(1, $validations);
    }

    /**
     * Tests that no certification is created
     * when not all lessons of the theme are validated.
     */
    public function testCertificationIsNotCreatedBeforeAllLessonsAreValidated(): void
    {
        $user = $this->em->getRepository(User::class)->find($this->userId);
        $lesson = $this->em->getRepository(Lesson::class)->find($this->lessonIds[0]);
        $theme = $this->em->getRepository(Theme::class)->find($this->themeId);

        $this->client->loginUser($user);

        $this->client->request(
            'POST',
            '/lesson/' . $lesson->getId() . '/validate'
        );

        /*
         * Reload the EntityManager after the request.
         */
        $this->em->clear();

        $user = $this->em->getRepository(User::class)->find($this->userId);
        $theme = $this->em->getRepository(Theme::class)->find($this->themeId);

        $certification = $this->em
            ->getRepository(Certification::class)
            ->findOneBy([
                'user' => $user,
                'theme' => $theme,
            ]);

        $this->assertNull($certification);
    }

    /**
     * Tests that a certification is automatically created
     * when all lessons of the theme are validated.
     */
    public function testCertificationIsAutomaticallyCreatedWhenAllLessonsAreValidated(): void
    {
        $user = $this->em->getRepository(User::class)->find($this->userId);

        $this->client->loginUser($user);

        foreach ($this->lessonIds as $lessonId) {
            $this->client->request(
                'POST',
                '/lesson/' . $lessonId . '/validate'
            );
        }

        /*
         * Reload everything from the database.
         */
        $this->em->clear();

        $user = $this->em->getRepository(User::class)->find($this->userId);
        $theme = $this->em->getRepository(Theme::class)->find($this->themeId);

        $certification = $this->em
            ->getRepository(Certification::class)
            ->findOneBy([
                'user' => $user,
                'theme' => $theme,
            ]);

        $this->assertNotNull($certification);
        $this->assertNotNull($certification->getObtainedAt());

        $this->assertSame(
            $theme->getId(),
            $certification->getTheme()->getId()
        );
    }

    /**
     * Tests that only one certification is created
     * for a completed theme.
     */
    public function testOnlyOneCertificationIsCreatedForTheSameTheme(): void
    {
        $user = $this->em->getRepository(User::class)->find($this->userId);

        $this->client->loginUser($user);

        /*
         * Validate every lesson.
         */
        foreach ($this->lessonIds as $lessonId) {
            $this->client->request(
                'POST',
                '/lesson/' . $lessonId . '/validate'
            );
        }

        /*
         * Try to validate the first lesson again.
         */
        $this->client->request(
            'POST',
            '/lesson/' . $this->lessonIds[0] . '/validate'
        );

        /*
         * Reload entities.
         */
        $this->em->clear();

        $user = $this->em->getRepository(User::class)->find($this->userId);
        $theme = $this->em->getRepository(Theme::class)->find($this->themeId);

        $certifications = $this->em
            ->getRepository(Certification::class)
            ->findBy([
                'user' => $user,
                'theme' => $theme,
            ]);

        $this->assertCount(1, $certifications);
    }

    /**
     * Removes test data from previous interrupted PHPUnit runs.
     */
    private function cleanupPreviousTestData(): void
    {
        /*
         * Remove previous certifications.
         */
        $certifications = $this->em
            ->getRepository(Certification::class)
            ->findBy([
                'user' => $this->em
                    ->getRepository(User::class)
                    ->findOneBy([
                        'email' => 'lesson_validation_test@test.fr',
                    ]),
            ]);

        foreach ($certifications as $certification) {
            $this->em->remove($certification);
        }

        /*
         * Remove previous validations.
         */
        $existingUser = $this->em
            ->getRepository(User::class)
            ->findOneBy([
                'email' => 'lesson_validation_test@test.fr',
            ]);

        if ($existingUser !== null) {
            $validations = $this->em
                ->getRepository(LessonValidation::class)
                ->findBy([
                    'user' => $existingUser,
                ]);

            foreach ($validations as $validation) {
                $this->em->remove($validation);
            }
        }

        /*
         * Remove previous lessons.
         */
        $oldLessons = $this->em
            ->getRepository(Lesson::class)
            ->findBy([
                'title' => [
                    'Leçon de test 1',
                    'Leçon de test 2',
                ],
            ]);

        foreach ($oldLessons as $lesson) {
            $this->em->remove($lesson);
        }

        /*
         * Remove previous course.
         */
        $oldCourse = $this->em
            ->getRepository(Course::class)
            ->findOneBy([
                'title' => 'Cursus de test',
            ]);

        if ($oldCourse !== null) {
            $this->em->remove($oldCourse);
        }

        /*
         * Remove previous theme.
         */
        $oldTheme = $this->em
            ->getRepository(Theme::class)
            ->findOneBy([
                'title' => 'Thème de test',
            ]);

        if ($oldTheme !== null) {
            $this->em->remove($oldTheme);
        }

        /*
         * Remove previous user.
         */
        if ($existingUser !== null) {
            $this->em->remove($existingUser);
        }

        $this->em->flush();
        $this->em->clear();
    }

    protected function tearDown(): void
    {
        /*
         * Always reload entities from the current EntityManager.
         * This avoids "Detached entity cannot be removed" errors.
         */
        if ($this->userId !== null) {
            $user = $this->em
                ->getRepository(User::class)
                ->find($this->userId);

            if ($user !== null) {
                $certifications = $this->em
                    ->getRepository(Certification::class)
                    ->findBy([
                        'user' => $user,
                    ]);

                foreach ($certifications as $certification) {
                    $this->em->remove($certification);
                }

                $validations = $this->em
                    ->getRepository(LessonValidation::class)
                    ->findBy([
                        'user' => $user,
                    ]);

                foreach ($validations as $validation) {
                    $this->em->remove($validation);
                }
            }
        }

        /*
         * Remove lessons by reloading them first.
         */
        foreach ($this->lessonIds as $lessonId) {
            $lesson = $this->em
                ->getRepository(Lesson::class)
                ->find($lessonId);

            if ($lesson !== null) {
                $this->em->remove($lesson);
            }
        }

        /*
         * Remove course.
         */
        if ($this->courseId !== null) {
            $course = $this->em
                ->getRepository(Course::class)
                ->find($this->courseId);

            if ($course !== null) {
                $this->em->remove($course);
            }
        }

        /*
         * Remove theme.
         */
        if ($this->themeId !== null) {
            $theme = $this->em
                ->getRepository(Theme::class)
                ->find($this->themeId);

            if ($theme !== null) {
                $this->em->remove($theme);
            }
        }

        /*
         * Remove user.
         */
        if ($this->userId !== null) {
            $user = $this->em
                ->getRepository(User::class)
                ->find($this->userId);

            if ($user !== null) {
                $this->em->remove($user);
            }
        }

        $this->em->flush();
        $this->em->clear();

        parent::tearDown();
    }
}