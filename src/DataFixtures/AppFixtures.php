<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Theme;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Loads initial data: users, themes, courses and lessons.
 */
class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /*
         * ============================================================
         * USERS
         * ============================================================
         */

        // Admin
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_CLIENT']);
        $admin->setIsVerified(true);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admintest')
        );

        $manager->persist($admin);

        // Client 1
        $user = new User();
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_CLIENT']);
        $user->setIsVerified(true);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'useruser')
        );

        $manager->persist($user);

        // Client 2
        $user2 = new User();
        $user2->setEmail('useruser@user.com');
        $user2->setRoles(['ROLE_CLIENT']);
        $user2->setIsVerified(true);
        $user2->setPassword(
            $this->passwordHasher->hashPassword($user2, 'User-2l')
        );

        $manager->persist($user2);

        /*
         * ============================================================
         * MUSIQUE
         * ============================================================
         */

        $music = new Theme();
        $music->setTitle('Musique');

        $manager->persist($music);

        // Guitare
        $guitar = new Course();
        $guitar->setTitle("Cursus d'initiation à la guitare");
        $guitar->setPrice(50);
        $guitar->setTheme($music);

        $manager->persist($guitar);

        $lesson = new Lesson();
        $lesson->setTitle("Découverte de l'instrument");
        $lesson->setPrice(26);
        $lesson->setCourse($guitar);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        $lesson = new Lesson();
        $lesson->setTitle("Les accords et les gammes");
        $lesson->setPrice(26);
        $lesson->setCourse($guitar);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        // Piano
        $piano = new Course();
        $piano->setTitle("Cursus d'initiation au piano");
        $piano->setPrice(50);
        $piano->setTheme($music);

        $manager->persist($piano);

        $lesson = new Lesson();
        $lesson->setTitle("Découverte de l'instrument");
        $lesson->setPrice(26);
        $lesson->setCourse($piano);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        $lesson = new Lesson();
        $lesson->setTitle("Les accords et les gammes");
        $lesson->setPrice(26);
        $lesson->setCourse($piano);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        /*
         * ============================================================
         * INFORMATIQUE
         * ============================================================
         */

        $it = new Theme();
        $it->setTitle('Informatique');

        $manager->persist($it);

        // Développement web
        $web = new Course();
        $web->setTitle("Cursus d’initiation au développement web");
        $web->setPrice(60);
        $web->setTheme($it);

        $manager->persist($web);

        $lesson = new Lesson();
        $lesson->setTitle("Les langages Html et CSS");
        $lesson->setPrice(32);
        $lesson->setCourse($web);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        $lesson = new Lesson();
        $lesson->setTitle("Dynamiser votre site avec Javascript");
        $lesson->setPrice(32);
        $lesson->setCourse($web);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        /*
         * ============================================================
         * JARDINAGE
         * ============================================================
         */

        $gardening = new Theme();
        $gardening->setTitle('Jardinage');

        $manager->persist($gardening);

        $garden = new Course();
        $garden->setTitle("Cursus d’initiation au jardinage");
        $garden->setPrice(30);
        $garden->setTheme($gardening);

        $manager->persist($garden);

        $lesson = new Lesson();
        $lesson->setTitle("Les outils du jardinier");
        $lesson->setPrice(16);
        $lesson->setCourse($garden);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        $lesson = new Lesson();
        $lesson->setTitle("Jardiner avec la lune");
        $lesson->setPrice(16);
        $lesson->setCourse($garden);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        /*
         * ============================================================
         * CUISINE
         * ============================================================
         */

        $cooking = new Theme();
        $cooking->setTitle('Cuisine');

        $manager->persist($cooking);

        // Cuisine
        $cook = new Course();
        $cook->setTitle("Cursus d’initiation à la cuisine");
        $cook->setPrice(44);
        $cook->setTheme($cooking);

        $manager->persist($cook);

        $lesson = new Lesson();
        $lesson->setTitle("Les modes de cuisson");
        $lesson->setPrice(23);
        $lesson->setCourse($cook);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        $lesson = new Lesson();
        $lesson->setTitle("Les saveurs");
        $lesson->setPrice(23);
        $lesson->setCourse($cook);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        // Art du dressage culinaire
        $presentation = new Course();
        $presentation->setTitle(
            "Cursus d’initiation à l’art du dressage culinaire"
        );
        $presentation->setPrice(48);
        $presentation->setTheme($cooking);

        $manager->persist($presentation);

        $lesson = new Lesson();
        $lesson->setTitle("Mettre en œuvre le style dans l’assiette");
        $lesson->setPrice(26);
        $lesson->setCourse($presentation);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        $lesson = new Lesson();
        $lesson->setTitle("Harmoniser un repas à quatre plats");
        $lesson->setPrice(26);
        $lesson->setCourse($presentation);
        $lesson->setContent('Lorem ipsum...');
        $lesson->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');

        $manager->persist($lesson);

        /*
         * ============================================================
         * SAVE
         * ============================================================
         */

        $manager->flush();
    }
}