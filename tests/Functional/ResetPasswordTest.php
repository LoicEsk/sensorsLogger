<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordTest extends FunctionalTestCase
{
    public function testRequestPageIsPublic(): void
    {
        $this->client->request('GET', '/reset-password');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Reset your password');
    }

    public function testRequestForKnownEmailSendsTheResetLink(): void
    {
        $this->createUser();

        $this->requestReset(self::USER_EMAIL);

        $this->assertResponseRedirects('/reset-password/check-email');
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailAddressContains($email, 'to', self::USER_EMAIL);
        $this->assertEmailAddressContains($email, 'from', 'meteo@eskell.net');
        $this->assertEmailHeaderSame($email, 'subject', 'Your password reset request');
        $this->assertEmailHtmlBodyContains($email, 'http://localhost/reset-password/reset/');
    }

    public function testRequestForUnknownEmailSendsNothingButRevealsNothing(): void
    {
        $this->createUser();

        $this->requestReset('unknown@example.test');

        $this->assertResponseRedirects('/reset-password/check-email');
        $this->assertEmailCount(0);
    }

    public function testUserCanChooseANewPasswordFromTheEmailedLink(): void
    {
        $this->createUser();
        $this->requestReset(self::USER_EMAIL);

        $token = $this->extractResetTokenFromEmail();

        // Le jeton est retiré de l'URL et stocké en session
        $this->client->request('GET', '/reset-password/reset/'.$token);
        $this->assertResponseRedirects('/reset-password/reset');

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Reset password')->form([
            'change_password_form[plainPassword][first]' => 'n3w-passw0rd',
            'change_password_form[plainPassword][second]' => 'n3w-passw0rd',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/');
        $user = $this->em()->getRepository(User::class)->findOneBy(['email' => self::USER_EMAIL]);
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue($hasher->isPasswordValid($user, 'n3w-passw0rd'));
        $this->assertFalse($hasher->isPasswordValid($user, self::USER_PASSWORD));
    }

    public function testInvalidTokenRedirectsToRequestPageWithAnError(): void
    {
        $this->client->request('GET', '/reset-password/reset/invalid-token');
        $this->client->followRedirect();

        $this->assertResponseRedirects('/reset-password');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', 'There was a problem validating your reset request');
    }

    private function extractResetTokenFromEmail(): string
    {
        $email = $this->getMailerMessage();
        $this->assertInstanceOf(TemplatedEmail::class, $email);
        $this->assertSame(1, preg_match('#/reset-password/reset/([A-Za-z0-9]+)#', (string) $email->getHtmlBody(), $matches));

        return $matches[1];
    }

    private function requestReset(string $email): void
    {
        $crawler = $this->client->request('GET', '/reset-password');
        $form = $crawler->selectButton('Send password reset email')->form([
            'reset_password_request_form[email]' => $email,
        ]);
        $this->client->submit($form);
    }
}
