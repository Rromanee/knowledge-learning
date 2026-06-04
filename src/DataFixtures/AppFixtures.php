<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/** Loads initial data: themes, courses and lessons. */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Music theme and courses
        $music = new Theme();
        $music->setTitle('Musique');
        $manager->persist($music);

        $guitar = new Course();
        $guitar->setTitle("Cursus d'initiation à la guitare");
        $guitar->setPrice(50);
        $guitar->setTheme($music);
        $manager->persist($guitar);

        $lesson1 = new Lesson();
        $lesson1->setTitle("Découverte de l'instrument");
        $lesson1->setPrice(26);
        $lesson1->setCourse($guitar);
        $lesson1->setContent('Lorem ipsum...');
        $lesson1->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson1);
        
        $lesson2 = new Lesson();
        $lesson2->setTitle("Les accords et les gammes");
        $lesson2->setPrice(26);
        $lesson2->setCourse($guitar);
        $lesson2->setContent('Lorem ipsum...');
        $lesson2->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson2);

        $piano = new Course();
        $piano->setTitle("Cursus d'initiation au piano");
        $piano->setPrice(50);
        $piano->setTheme($music);
        $manager->persist($piano);

        $lesson1 = new Lesson();
        $lesson1->setTitle("Découverte de l'instrument");
        $lesson1->setPrice(26);
        $lesson1->setCourse($piano);
        $lesson1->setContent('Lorem ipsum...');
        $lesson1->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson1);
        
        $lesson2 = new Lesson();
        $lesson2->setTitle("Les accords et les gammes");
        $lesson2->setPrice(26);
        $lesson2->setCourse($piano);
        $lesson2->setContent('Lorem ipsum...');
        $lesson2->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson2);
        
        //IT theme and courses
        $it = new Theme();
        $it->setTitle('Informatique');
        $manager->persist($it);

        $web = new Course();
        $web->setTitle("Cursus d’initiation au développement web");
        $web->setPrice(60);
        $web->setTheme($it);
        $manager->persist($web);

        $lesson1 = new Lesson();
        $lesson1->setTitle("Les langages Html et CSS");
        $lesson1->setPrice(32);
        $lesson1->setCourse($web);
        $lesson1->setContent('Lorem ipsum...');
        $lesson1->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson1);

        $lesson2 = new Lesson();
        $lesson2->setTitle("Dynamiser votre site avec Javascript");
        $lesson2->setPrice(32);
        $lesson2->setCourse($web);
        $lesson2->setContent('Lorem ipsum...');
        $lesson2->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson2);
        
        //Gardening theme and courses
        $gardening = new Theme();
        $gardening->setTitle('Jardinage');
        $manager->persist($gardening);

        $garden = new Course();
        $garden->setTitle("Cursus d’initiation au jardinage");
        $garden->setPrice(30);
        $garden->setTheme($gardening);
        $manager->persist($garden);

        $lesson1 = new Lesson();
        $lesson1->setTitle("Les outils du jardinier");
        $lesson1->setPrice(16);
        $lesson1->setCourse($garden);
        $lesson1->setContent('Lorem ipsum...');
        $lesson1->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson1);
        
        $lesson2 = new Lesson();
        $lesson2->setTitle("Jardiner avec la lune");
        $lesson2->setPrice(16);
        $lesson2->setCourse($garden);
        $lesson2->setContent('Lorem ipsum...');
        $lesson2->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson2);
        
        //Cooking theme and courses
        $cooking = new Theme();
        $cooking->setTitle('Cuisine');
        $manager->persist($cooking);

        $cook = new Course();
        $cook->setTitle("Cursus d’initiation à la cuisine");
        $cook->setPrice(44);
        $cook->setTheme($cooking);
        $manager->persist($cook);

        $lesson1 = new Lesson();
        $lesson1->setTitle("Les modes de cuisson");
        $lesson1->setPrice(23);
        $lesson1->setCourse($cook);
        $lesson1->setContent('Lorem ipsum...');
        $lesson1->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson1);
        
        $lesson2 = new Lesson();
        $lesson2->setTitle("Les saveurs");
        $lesson2->setPrice(23);
        $lesson2->setCourse($cook);
        $lesson2->setContent('Lorem ipsum...');
        $lesson2->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson2);

        $presentation = new Course();
        $presentation->setTitle("Cursus d’initiation à l’art du dressage culinaire");
        $presentation->setPrice(48);
        $presentation->setTheme($cooking);
        $manager->persist($presentation);

        $lesson1 = new Lesson();
        $lesson1->setTitle("Mettre en œuvre le style dans l’assiette");
        $lesson1->setPrice(26);
        $lesson1->setCourse($presentation);
        $lesson1->setContent('Lorem ipsum...');
        $lesson1->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson1);
        
        $lesson2 = new Lesson();
        $lesson2->setTitle("Harmoniser un repas à quatre plats");
        $lesson2->setPrice(26);
        $lesson2->setCourse($presentation);
        $lesson2->setContent('Lorem ipsum...');
        $lesson2->setVideoUrl('https://youtu.be/BFzeAGvLvBw?si=_biqXykVZ4M-Cix0');
        $manager->persist($lesson2);

        $manager->flush();
    }
}
