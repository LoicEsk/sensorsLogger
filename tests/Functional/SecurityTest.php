<?php

namespace App\Tests\Functional;

class SecurityTest extends FunctionalTestCase
{
    public function testAnonymousVisitorIsRedirectedToLogin(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseRedirects('http://localhost/login');
    }

    public function testLoginPageDisplaysTheForm(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('input[name="_username"]'));
        $this->assertCount(1, $crawler->filter('input[name="_password"]'));
        $this->assertNotEmpty($crawler->filter('input[name="_csrf_token"]')->attr('value'));
    }

    public function testUserCanLogInAndAccessTheDashboard(): void
    {
        $this->createUser();

        $this->submitLoginForm(self::USER_EMAIL, self::USER_PASSWORD);

        $this->assertResponseRedirects('http://localhost/');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#appJs');
    }

    public function testRememberMeCookieIsIssuedOnLogin(): void
    {
        $this->createUser();

        $this->submitLoginForm(self::USER_EMAIL, self::USER_PASSWORD);

        $this->assertResponseHasCookie('REMEMBERME');
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        $this->createUser();

        $this->submitLoginForm(self::USER_EMAIL, 'wrong-password');

        $this->assertResponseRedirects('http://localhost/login');
        $this->assertRememberMeCookieIsNotUsable();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', 'Invalid credentials.');
        $this->assertInputValueSame('_username', self::USER_EMAIL);
    }

    public function testLoginIsRejectedWithoutValidCsrfToken(): void
    {
        $this->createUser();

        $this->client->request('POST', '/login', [
            '_username' => self::USER_EMAIL,
            '_password' => self::USER_PASSWORD,
            '_csrf_token' => 'forged-token',
        ]);

        $this->assertResponseRedirects('http://localhost/login');
        $this->assertRememberMeCookieIsNotUsable();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', 'Invalid CSRF token.');
    }

    public function testAuthenticatedUserCanBrowseProtectedPages(): void
    {
        $this->logIn();

        $this->client->request('GET', '/sensor/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Sensor index');
    }

    /**
     * Après un échec, Symfony peut renvoyer un cookie REMEMBERME, mais uniquement pour l'effacer.
     */
    private function assertRememberMeCookieIsNotUsable(): void
    {
        foreach ($this->client->getResponse()->headers->getCookies() as $cookie) {
            if ('REMEMBERME' === $cookie->getName()) {
                $this->assertTrue($cookie->isCleared(), 'Le cookie REMEMBERME ne doit pas être valide après un échec.');
            }
        }
    }

    private function submitLoginForm(string $email, string $password): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('login')->form([
            '_username' => $email,
            '_password' => $password,
        ]);
        $this->client->submit($form);
    }
}
