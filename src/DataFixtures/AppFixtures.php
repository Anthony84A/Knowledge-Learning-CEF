<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Cursus;
use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Data fixtures for the Knowledge Learning platform.
 *
 * Handles:
 * - Creating initial users (admin and regular users)
 * - Creating themes, courses (cursuses), and lessons as per the project specification
 */
class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher Service to hash user passwords
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Load initial data into the database.
     *
     * @param ObjectManager $manager Doctrine object manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // --- Create initial users ---
        $admin = new User();
        $admin->setEmail('admin@site.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setIsVerified(true);
        $manager->persist($admin);

        $user1 = new User();
        $user1->setEmail('user1@site.com');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password'));
        $user1->setIsVerified(true);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@site.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'password'));
        $user2->setIsVerified(true);
        $manager->persist($user2);

        // --- Create themes, courses (cursuses), and lessons ---
        $data = [
            "Musique" => [
                [
                    "title" => "Cursus d’initiation à la guitare",
                    "price" => "50.00",
                    "lessons" => [
                        ["title" => "Découverte de l’instrument", "price" => "26.00"],
                        ["title" => "Les accords et les gammes", "price" => "26.00"],
                    ]
                ],
                [
                    "title" => "Cursus d’initiation au piano",
                    "price" => "50.00",
                    "lessons" => [
                        ["title" => "Découverte de l’instrument", "price" => "26.00"],
                        ["title" => "Les accords et les gammes", "price" => "26.00"],
                    ]
                ]
            ],
            "Informatique" => [
                [
                    "title" => "Cursus d’initiation au développement web",
                    "price" => "60.00",
                    "lessons" => [
                        ["title" => "Les langages HTML et CSS", "price" => "32.00"],
                        ["title" => "Dynamiser votre site avec JavaScript", "price" => "32.00"],
                    ]
                ]
            ],
            "Jardinage" => [
                [
                    "title" => "Cursus d’initiation au jardinage",
                    "price" => "30.00",
                    "lessons" => [
                        ["title" => "Les outils du jardinier", "price" => "16.00"],
                        ["title" => "Jardiner avec la lune", "price" => "16.00"],
                    ]
                ]
            ],
            "Cuisine" => [
                [
                    "title" => "Cursus d’initiation à la cuisine",
                    "price" => "44.00",
                    "lessons" => [
                        ["title" => "Les modes de cuisson", "price" => "23.00"],
                        ["title" => "Les saveurs", "price" => "23.00"],
                    ]
                ],
                [
                    "title" => "Cursus d’initiation à l’art du dressage culinaire",
                    "price" => "48.00",
                    "lessons" => [
                        ["title" => "Mettre en œuvre le style dans l’assiette", "price" => "26.00"],
                        ["title" => "Harmoniser un repas à quatre plats", "price" => "26.00"],
                    ]
                ]
            ],
        ];

        // Loop through the data and create entities
        foreach ($data as $themeTitle => $cursuses) {
            $theme = new Theme();
            $theme->setTitle($themeTitle);
            $theme->setDescription("Theme description: $themeTitle");
            $manager->persist($theme);

            foreach ($cursuses as $cursusData) {
                $cursus = new Cursus();
                $cursus->setTitle($cursusData["title"]);
                $cursus->setDescription("Course description: " . $cursusData["title"]);
                $cursus->setPrice($cursusData["price"]);
                $cursus->setTheme($theme);
                $manager->persist($cursus);

                foreach ($cursusData["lessons"] as $lessonData) {
                    $lesson = new Lesson();
                    $lesson->setTitle($lessonData["title"]);
                    $lesson->setDescription("Lesson content: " . $lessonData["title"]);
                    $lesson->setPrice($lessonData["price"]);
                    $lesson->setCursus($cursus);
                    $manager->persist($lesson);
                }
            }
        }

        // Persist all changes to the database
        $manager->flush();
    }
}
