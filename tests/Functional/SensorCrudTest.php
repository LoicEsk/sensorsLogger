<?php

namespace App\Tests\Functional;

use App\Entity\Sensor;
use App\Entity\SensorData;

class SensorCrudTest extends FunctionalTestCase
{
    public function testSensorListShowsRegisteredSensors(): void
    {
        $this->logIn();
        $this->createSensor('AAA', true, 'Salon', '°C');

        $crawler = $this->client->request('GET', '/sensor/');

        $this->assertResponseIsSuccessful();
        $row = $crawler->filter('table tbody tr')->first()->text();
        $this->assertStringContainsString('Salon', $row);
        $this->assertStringContainsString('AAA', $row);
        $this->assertStringContainsString('Yes', $row);
    }

    public function testSensorCanBeCreatedThroughTheForm(): void
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/sensor/new');
        $form = $crawler->selectButton('Save')->form([
            'sensor[name]' => 'Jardin',
            'sensor[keyCode]' => 'GARDEN-01',
            'sensor[unit]' => '%',
        ]);
        $form['sensor[actif]']->tick();
        $this->client->submit($form);

        $this->assertResponseRedirects('/sensor/', 303);
        $sensor = $this->em()->getRepository(Sensor::class)->findOneBy(['keyCode' => 'GARDEN-01']);
        $this->assertNotNull($sensor);
        $this->assertSame('Jardin', $sensor->getName());
        $this->assertSame('%', $sensor->getUnit());
        $this->assertTrue($sensor->getActif());
    }

    public function testSensorPageListsItsLatestData(): void
    {
        $this->logIn();
        $sensor = $this->createSensor('AAA', true, 'Salon', '°C');
        $this->createSensorData($sensor, 19.25, '2024-01-10 08:15:00');

        $this->client->request('GET', '/sensor/'.$sensor->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Sonde Salon');
        $this->assertSelectorTextContains('table:last-of-type tbody', '10/01/2024 08:15:00');
        $this->assertSelectorTextContains('table:last-of-type tbody', '19.25°C');
    }

    public function testDeletingASensorAlsoDeletesItsData(): void
    {
        $this->logIn();
        $sensor = $this->createSensor('AAA');
        $kept = $this->createSensor('BBB');
        $this->createSensorData($sensor, 1.0, '2024-01-10 08:00:00');
        $this->createSensorData($sensor, 2.0, '2024-01-10 09:00:00');
        $this->createSensorData($kept, 3.0, '2024-01-10 10:00:00');

        $crawler = $this->client->request('GET', '/sensor/'.$sensor->getId());
        $this->client->submit($crawler->selectButton('Delete')->form());

        $this->assertResponseRedirects('/sensor/', 303);
        $em = $this->em();
        $this->assertSame(['BBB'], array_map(
            static fn (Sensor $s): ?string => $s->getKeyCode(),
            $em->getRepository(Sensor::class)->findAll()
        ));
        $this->assertSame([3.0], array_map(
            static fn (SensorData $d): ?float => $d->getValue(),
            $em->getRepository(SensorData::class)->findAll()
        ));
    }

    public function testDeletionIsRefusedWithAnInvalidCsrfToken(): void
    {
        $this->logIn();
        $sensor = $this->createSensor('AAA');

        $this->client->request('POST', '/sensor/'.$sensor->getId(), ['_token' => 'forged-token']);

        $this->assertResponseRedirects('/sensor/', 303);
        $this->assertSame(1, $this->em()->getRepository(Sensor::class)->count([]));
    }
}
