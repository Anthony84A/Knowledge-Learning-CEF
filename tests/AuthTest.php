<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    private const TEST_EMAIL = 'testuser@example.com';
    private const TEST_PASSWORD = 'password123';
    private const REGISTRATION_EMAIL = 'newuser@example.com';
    private const REGISTRATION_PASSWORD = 'newpassword123';

    private ?User $testUser = null;
    private ?User $registrationUser = null;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Créer l'utilisateur de login s'il n'existe pas
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $existingUser = $entityManager->getRepository(User::class)
                                      ->findOneBy(['email' => self::TEST_EMAIL]);

        if (!$existingUser) {
            $user = new User();
            $user->setEmail(self::TEST_EMAIL);
            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(true);
            $user->setPassword(
                $this->client->getContainer()->get('security.password_hasher')
                     ->hashPassword($user, self::TEST_PASSWORD)
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->testUser = $user;
        } else {
            $this->testUser = $existingUser;
        }
    }

    protected function tearDown(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        // Supprimer l'utilisateur de login
        if ($this->testUser) {
            $entityManager->remove($this->testUser);
            $entityManager->flush();
            $this->testUser = null;
        }

        // Supprimer l'utilisateur créé lors de l'inscription
        if ($this->registrationUser) {
            $entityManager->remove($this->registrationUser);
            $entityManager->flush();
            $this->registrationUser = null;
        }

        parent::tearDown();
    }

    public function testRegistration(): void
    {
        $crawler = $this->client->request('GET', '/register');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Créer un compte')->form([
            'registration_form[email]' => self::REGISTRATION_EMAIL,
            'registration_form[plainPassword]' => self::REGISTRATION_PASSWORD,
            'registration_form[agreeTerms]' => true,
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();

        // Récupérer l'utilisateur créé pour pouvoir le supprimer
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->registrationUser = $entityManager->getRepository(User::class)
                                               ->findOneBy(['email' => self::REGISTRATION_EMAIL]);

        $this->assertSelectorTextContains('body', 'Déconnexion');
    }

    public function testLogin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $form = $crawler->selectButton('Se connecter')->form([
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
            '_csrf_token' => $csrfToken,
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('body', 'Déconnexion');
    }
}
