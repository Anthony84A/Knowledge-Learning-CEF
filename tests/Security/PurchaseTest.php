<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Lesson;
use App\Entity\Cursus;
use App\Entity\Purchase;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional tests for lesson and cursus purchases.
 *
 * This test covers:
 * - Purchase of a single lesson
 * - Purchase of a cursus with multiple lessons
 * - Prevention of duplicate purchases
 */
class PurchaseTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    /**
     * @var EntityManagerInterface|null
     */
    private ?EntityManagerInterface $entityManager = null;

    /**
     * @var User|null
     */
    private ?User $user = null;

    /**
     * Set up the test environment.
     *
     * Initializes the HTTP client, entity manager, and creates a test user.
     */
    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient([], [
            'HTTP_HOST' => 'localhost',
        ]);

        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = new User();
        $this->user->setEmail('buyer@example.com');
        $this->user->setPassword(password_hash('Password123', PASSWORD_BCRYPT));
        $this->user->setIsVerified(true);

        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        $this->client->loginUser($this->user);
    }

    /**
     * Assigns a name and description to a Theme entity.
     *
     * @param Theme $theme
     * @param string $name
     * @param string $description
     */
    private function assignThemeNameAndDescription(Theme $theme, string $name, string $description = 'Test theme description'): void
    {
        foreach (['setName', 'setTitle', 'setLabel', 'setNom'] as $method) {
            if (method_exists($theme, $method)) {
                $theme->{$method}($name);
                break;
            }
        }

        if (method_exists($theme, 'setDescription')) {
            $theme->setDescription($description);
        } else {
            $ref = new \ReflectionObject($theme);
            if ($ref->hasProperty('description')) {
                $prop = $ref->getProperty('description');
                $prop->setAccessible(true);
                $prop->setValue($theme, $description);
            }
        }
    }

    /**
     * Test purchasing a single lesson.
     *
     * Steps:
     * 1. Create a theme and a cursus.
     * 2. Create a lesson linked to the cursus.
     * 3. Simulate Stripe success URL.
     * 4. Verify that a purchase record is created.
     */
    public function testBuyLesson(): void
    {
        $theme = new Theme();
        $this->assignThemeNameAndDescription($theme, 'Theme Test');
        $this->entityManager->persist($theme);

        $cursus = new Cursus();
        $cursus->setTitle('Cursus Test')
               ->setDescription('Test Cursus Description')
               ->setPrice('30.00')
               ->setTheme($theme);
        $this->entityManager->persist($cursus);

        $lesson = new Lesson();
        $lesson->setTitle('Test Lesson')
               ->setDescription('Demo Lesson')
               ->setPrice('15.00')
               ->setCursus($cursus);
        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

        $this->client->request('GET', '/success/'.$lesson->getId().'/FAKESESSION123');
        $this->assertResponseIsSuccessful();

        $purchase = $this->entityManager
            ->getRepository(Purchase::class)
            ->findOneBy(['user' => $this->user, 'lesson' => $lesson]);

        $this->assertNotNull($purchase, 'A purchase should be created for the lesson.');
        $this->assertEquals('lesson', $purchase->getType());
    }

    /**
     * Test purchasing a cursus with multiple lessons.
     *
     * Steps:
     * 1. Create a theme.
     * 2. Create a cursus and add two lessons.
     * 3. Persist the entities.
     * 4. Simulate Stripe success URL for cursus purchase.
     * 5. Verify that the cursus purchase is created.
     * 6. Verify that each lesson purchase is created.
     */
    public function testBuyCursusWithLessons(): void
    {
        $theme = new Theme();
        $this->assignThemeNameAndDescription($theme, 'Theme Test 2');
        $this->entityManager->persist($theme);

        $cursus = new Cursus();
        $cursus->setTitle('Test Cursus')
               ->setDescription('Demo Cursus')
               ->setPrice('40.00')
               ->setTheme($theme);

        $lesson1 = new Lesson();
        $lesson1->setTitle('Lesson 1')->setDescription('Desc1')->setPrice('20.00');
        $cursus->addLesson($lesson1);

        $lesson2 = new Lesson();
        $lesson2->setTitle('Lesson 2')->setDescription('Desc2')->setPrice('20.00');
        $cursus->addLesson($lesson2);

        $this->entityManager->persist($cursus);
        $this->entityManager->persist($lesson1);
        $this->entityManager->persist($lesson2);
        $this->entityManager->flush();

        $this->client->request('GET', '/success-cursus/'.$cursus->getId().'/FAKESESSION456');
        $this->assertResponseIsSuccessful();

        $cursusPurchase = $this->entityManager
            ->getRepository(Purchase::class)
            ->findOneBy(['user' => $this->user, 'cursus' => $cursus]);

        $this->assertNotNull($cursusPurchase, 'A purchase should be created for the cursus.');
        $this->assertEquals('cursus', $cursusPurchase->getType());

        $lessonPurchases1 = $this->entityManager
            ->getRepository(Purchase::class)
            ->findBy(['user' => $this->user, 'lesson' => $lesson1]);

        $lessonPurchases2 = $this->entityManager
            ->getRepository(Purchase::class)
            ->findBy(['user' => $this->user, 'lesson' => $lesson2]);

        $this->assertCount(1, $lessonPurchases1, 'First lesson purchase should be created.');
        $this->assertCount(1, $lessonPurchases2, 'Second lesson purchase should be created.');
    }

    /**
     * Test that duplicate purchases are not created for the same lesson.
     *
     * Steps:
     * 1. Create a theme, cursus, and lesson.
     * 2. Simulate two Stripe success URLs for the same lesson.
     * 3. Verify that only one purchase is created.
     */
    public function testNoDuplicatePurchase(): void
    {
        $theme = new Theme();
        $this->assignThemeNameAndDescription($theme, 'Theme Test 3');
        $this->entityManager->persist($theme);

        $cursus = new Cursus();
        $cursus->setTitle('Duplicate Cursus')
               ->setDescription('For duplicate test')
               ->setPrice('20.00')
               ->setTheme($theme);
        $this->entityManager->persist($cursus);

        $lesson = new Lesson();
        $lesson->setTitle('Duplicate Test Lesson')
               ->setDescription('Demo lesson')
               ->setPrice('10.00')
               ->setCursus($cursus);

        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

        $this->client->request('GET', '/success/'.$lesson->getId().'/SESSION1');
        $this->client->request('GET', '/success/'.$lesson->getId().'/SESSION2');

        $lessonPurchases = $this->entityManager
            ->getRepository(Purchase::class)
            ->findBy(['user' => $this->user, 'lesson' => $lesson]);

        $this->assertCount(1, $lessonPurchases, 'Duplicate purchases should not be created.');
    }

    /**
     * Tear down the test environment.
     *
     * Cleans up the database tables and closes the entity manager.
     */
    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $connection = $this->entityManager->getConnection();
            $platform = $connection->getDatabasePlatform();
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');
            foreach (['purchase', 'lesson', 'cursus', 'theme', 'user'] as $table) {
                $connection->executeStatement($platform->getTruncateTableSQL($table, true));
            }
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');

            $this->entityManager->close();
            $this->entityManager = null;
        }

        parent::tearDown();
    }
}
