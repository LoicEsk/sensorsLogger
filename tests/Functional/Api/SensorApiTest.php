<?php

namespace App\Tests\Functional\Api;

use App\Entity\Sensor;
use App\Entity\SensorData;
use App\Tests\Functional\FunctionalTestCase;

class SensorApiTest extends FunctionalTestCase
{
    public function testGetEndpointAnswersSuccessWithoutAuthentication(): void
    {
        $this->client->request('GET', '/api/sensor', ['ping' => '1']);

        $this->assertResponseIsSuccessful();
        $this->assertSame(['success' => true], $this->jsonResponse());
    }

    public function testPostFromUnknownSensorRegistersItAsInactiveWithoutStoringTheValue(): void
    {
        $this->client->request('POST', '/api/sensor', ['sensor' => 'NEW-SENSOR', 'value' => '21.5']);

        $this->assertResponseIsSuccessful();
        $this->assertSame(['success' => true], $this->jsonResponse());

        $sensors = $this->em()->getRepository(Sensor::class)->findAll();
        $this->assertCount(1, $sensors);
        $this->assertSame('NEW-SENSOR', $sensors[0]->getKeyCode());
        $this->assertFalse($sensors[0]->getActif());
        $this->assertSame(0, $this->em()->getRepository(SensorData::class)->count([]));
    }

    public function testPostFromActiveSensorStoresTheValue(): void
    {
        $this->createSensor('AAA', true);

        $this->client->request('POST', '/api/sensor', ['sensor' => 'AAA', 'value' => '21.5']);

        $this->assertResponseIsSuccessful();
        $data = $this->em()->getRepository(SensorData::class)->findAll();
        $this->assertCount(1, $data);
        $this->assertSame(21.5, $data[0]->getValue());
        $this->assertSame('AAA', $data[0]->getSensor()->getKeyCode());
    }

    public function testPostFromInactiveSensorIgnoresTheValue(): void
    {
        $this->createSensor('AAA', false);

        $this->client->request('POST', '/api/sensor', ['sensor' => 'AAA', 'value' => '21.5']);

        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $this->em()->getRepository(SensorData::class)->count([]));
        $this->assertSame(1, $this->em()->getRepository(Sensor::class)->count([]));
    }

    public function testPostWithoutSensorKeyStoresNothing(): void
    {
        $this->client->request('POST', '/api/sensor', ['value' => '21.5']);

        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $this->em()->getRepository(Sensor::class)->count([]));
        $this->assertSame(0, $this->em()->getRepository(SensorData::class)->count([]));
    }

    public function testPostWithEmptySensorKeyStoresNothing(): void
    {
        $this->client->request('POST', '/api/sensor', ['sensor' => '', 'value' => '21.5']);

        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $this->em()->getRepository(Sensor::class)->count([]));
        $this->assertSame(0, $this->em()->getRepository(SensorData::class)->count([]));
    }

    public function testPostFromActiveSensorWithKeyZeroStoresTheValue(): void
    {
        $this->createSensor('0', true);

        $this->client->request('POST', '/api/sensor', ['sensor' => '0', 'value' => '21.5']);

        $this->assertResponseIsSuccessful();
        $data = $this->em()->getRepository(SensorData::class)->findAll();
        $this->assertCount(1, $data);
        $this->assertSame(21.5, $data[0]->getValue());
        $this->assertSame('0', $data[0]->getSensor()->getKeyCode());
    }

    public function testPostFromUnknownSensorWithKeyZeroRegistersItAsInactive(): void
    {
        $this->client->request('POST', '/api/sensor', ['sensor' => '0', 'value' => '21.5']);

        $this->assertResponseIsSuccessful();
        $sensors = $this->em()->getRepository(Sensor::class)->findAll();
        $this->assertCount(1, $sensors);
        $this->assertSame('0', $sensors[0]->getKeyCode());
        $this->assertFalse($sensors[0]->getActif());
        $this->assertSame(0, $this->em()->getRepository(SensorData::class)->count([]));
    }

    public function testPostOfZeroValueFromActiveSensorStoresIt(): void
    {
        $this->createSensor('AAA', true);

        $this->client->request('POST', '/api/sensor', ['sensor' => 'AAA', 'value' => '0']);

        $this->assertResponseIsSuccessful();
        $data = $this->em()->getRepository(SensorData::class)->findAll();
        $this->assertCount(1, $data);
        $this->assertSame(0.0, $data[0]->getValue());
        $this->assertSame('AAA', $data[0]->getSensor()->getKeyCode());
    }

    public function testPostOfZeroValueFromUnknownSensorRegistersItAsInactive(): void
    {
        $this->client->request('POST', '/api/sensor', ['sensor' => 'NEW-SENSOR', 'value' => '0']);

        $this->assertResponseIsSuccessful();
        $sensors = $this->em()->getRepository(Sensor::class)->findAll();
        $this->assertCount(1, $sensors);
        $this->assertSame('NEW-SENSOR', $sensors[0]->getKeyCode());
        $this->assertFalse($sensors[0]->getActif());
        $this->assertSame(0, $this->em()->getRepository(SensorData::class)->count([]));
    }

    /**
     * Une valeur absente ou non numérique ne doit jamais être enregistrée comme un 0,
     * ni faire enregistrer un capteur inconnu.
     *
     * @dataProvider provideMissingOrNonNumericValue
     *
     * @param array<string, string> $parameters
     */
    public function testPostWithMissingOrNonNumericValueStoresNothing(array $parameters): void
    {
        $this->createSensor('AAA', true);

        $this->client->request('POST', '/api/sensor', $parameters);

        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $this->em()->getRepository(SensorData::class)->count([]));
        $this->assertSame(1, $this->em()->getRepository(Sensor::class)->count([]));
    }

    /**
     * @return iterable<string, array{array<string, string>}>
     */
    public static function provideMissingOrNonNumericValue(): iterable
    {
        yield 'capteur actif, value absente' => [['sensor' => 'AAA']];
        yield 'capteur actif, value vide' => [['sensor' => 'AAA', 'value' => '']];
        yield 'capteur actif, value non numérique' => [['sensor' => 'AAA', 'value' => 'abc']];
        yield 'capteur inconnu, value absente' => [['sensor' => 'NEW-SENSOR']];
        yield 'capteur inconnu, value non numérique' => [['sensor' => 'NEW-SENSOR', 'value' => 'abc']];
    }

    public function testSensorDetailRequiresAuthentication(): void
    {
        $sensor = $this->createSensor('AAA');

        $this->client->request('GET', '/api/sensor/'.$sensor->getId());

        $this->assertResponseRedirects('http://localhost/login');
    }

    public function testSensorDetailIsReturnedAsJsonToAuthenticatedUser(): void
    {
        $this->logIn();
        $sensor = $this->createSensor('AAA', true, 'Salon', '°C');

        $this->client->request('GET', '/api/sensor/'.$sensor->getId());

        $this->assertResponseIsSuccessful();
        $json = $this->jsonResponse();
        $this->assertSame($sensor->getId(), $json['id']);
        $this->assertSame('Salon', $json['name']);
        $this->assertSame('AAA', $json['keyCode']);
        $this->assertSame('°C', $json['unit']);
        $this->assertTrue($json['actif']);
    }

    public function testSensorDataRequiresAuthentication(): void
    {
        $sensor = $this->createSensor('AAA');

        $this->client->request('GET', '/api/sensor/'.$sensor->getId().'/data');

        $this->assertResponseRedirects('http://localhost/login');
    }

    public function testSensorDataReturnsValuesWithinRangeInChronologicalOrder(): void
    {
        $this->logIn();
        $sensor = $this->createSensor('AAA');
        $otherSensor = $this->createSensor('BBB');

        $this->createSensorData($sensor, 1.5, '2024-01-09 23:59:59'); // avant la plage
        $this->createSensorData($sensor, 4.5, '2024-01-12 08:00:00'); // borne haute incluse
        $this->createSensorData($sensor, 2.5, '2024-01-10 00:00:00'); // borne basse incluse
        $this->createSensorData($sensor, 3.5, '2024-01-11 12:30:00');
        $this->createSensorData($sensor, 5.5, '2024-01-12 08:00:01'); // après la plage
        $this->createSensorData($otherSensor, 99.5, '2024-01-11 00:00:00'); // autre capteur

        $this->client->request('GET', '/api/sensor/'.$sensor->getId().'/data', [
            'from' => '2024-01-10 00:00:00',
            'to' => '2024-01-12 08:00:00',
        ]);

        $this->assertResponseIsSuccessful();
        $json = $this->jsonResponse();
        $this->assertSame([2.5, 3.5, 4.5], array_column($json, 'value'));
        $this->assertSame(
            ['2024-01-10T00:00:00', '2024-01-11T12:30:00', '2024-01-12T08:00:00'],
            array_map(static fn (array $row): string => substr($row['date'], 0, 19), $json)
        );
    }
}
