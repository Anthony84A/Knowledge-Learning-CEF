<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Functional test for user registration and login flow.
 *
 * This test covers:
 * - User registration with CSRF and terms agreement
 * - Login attempt before email verification
 * - Email verification simulation
 * - Successful login after verification
 * - Verification of page content after login
 */
class UserAuthenticationTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    /**
     * @var EntityManagerInterface|null
     */
    private ?EntityManagerInterface $entityManager = null;

    /**
     * Set up the test environment.
     *
     * Initializes the HTTP client and the entity manager.
     */
    protected function setUp(): void
    {
        $this->client = static::createClient([], [
            'HTTP_HOST' => 'localhost',
        ]);

        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Test user registration and login process.
     *
     * Steps:
     * 1. Register a new user with CSRF token and agreeTerms checked.
     * 2. Attempt login before verification (should remain on login page).
     * 3. Simulate email verification by setting isVerified to true.
     * 4. Attempt login after verification (should redirect to home page).
     * 5. Verify the home page contains expected content.
     */
    public function testRegisterLoginAndVerify(): void
    {
        $email = 'testuser_final4@example.com';
        $password = 'TestPassword123';

        // 1️⃣ Registration
        $crawler = $this->client->request('GET', '/register');
        $this->assertSelectorExists('form.auth-form');

        $form = $crawler->selectButton('Créer un compte')->form([
            'registration_form[email]' => $email,
            'registration_form[plainPassword]' => $password,
            'registration_form[agreeTerms]' => true,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects();

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        $this->assertNotNull($user, 'User should exist in database.');
        $this->assertFalse($user->isVerified(), 'User should not be verified initially.');

        // 2️⃣ Login attempt before activation
        $crawler = $this->client->request('GET', '/login');
        $this->assertSelectorExists('form');

        $form = $crawler->selectButton('Se connecter')->form([
            'email' => $email,
            'password' => $password,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/login'); // User should remain on login page

        // 3️⃣ Simulate email verification
        $user->setIsVerified(true);
        $this->entityManager->flush();

        // 4️⃣ Login after activation
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => $email,
            'password' => $password,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/'); // User redirected to home page

        // 5️⃣ Verify home page content
        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('h1.page-title', 'Thèmes de formation');
    }

    /**
     * Tear down the test environment.
     *
     * Removes the test user from the database and closes the entity manager.
     */
    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $user = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => 'testuser_final4@example.com']);

            if ($user) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();
            }

            $this->entityManager->close();
            $this->entityManager = null;
        }

        parent::tearDown();
    }
}
