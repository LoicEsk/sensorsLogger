<?php

namespace App\Tests\Functional;

use App\Entity\Sensor;
use App\Entity\SensorData;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Classe de base des tests fonctionnels : client HTTP + base SQLite recréée à chaque test.
 */
abstract class FunctionalTestCase extends WebTestCase
{
    protected const USER_EMAIL = 'user@example.test';
    protected const USER_PASSWORD = 'secret123';

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->resetDatabase();
    }

    /**
     * Le kernel est redémarré entre deux requêtes : on récupère toujours
     * l'EntityManager du conteneur courant, vidé de toute entité en cache.
     */
    protected function em(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('doctrine')->getManager();

        return $em;
    }

    protected function createUser(string $email = self::USER_EMAIL, string $plainPassword = self::USER_PASSWORD): User
    {
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = (new User())->setEmail($email)->setRoles(['ROLE_USER']);
        $user->setPassword($hasher->hashPassword($user, $plainPassword));

        $em = $this->em();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function logIn(): User
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        return $user;
    }

    protected function createSensor(string $keyCode, bool $actif = true, ?string $name = null, ?string $unit = null): Sensor
    {
        $sensor = (new Sensor($keyCode))
            ->setActif($actif)
            ->setName($name)
            ->setUnit($unit);

        $em = $this->em();
        $em->persist($sensor);
        $em->flush();

        return $sensor;
    }

    protected function createSensorData(Sensor $sensor, float $value, string $date): SensorData
    {
        $data = new SensorData($sensor, $value);
        $data->setDate(new \DateTime($date));

        $em = $this->em();
        $em->persist($data);
        $em->flush();

        return $data;
    }

    /**
     * @return array<mixed>
     */
    protected function jsonResponse(): array
    {
        $this->assertResponseHeaderSame('content-type', 'application/json');

        return json_decode((string) $this->client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
    }

    private function resetDatabase(): void
    {
        $em = $this->em();
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}
