<?php

namespace App\Tests\Functional\Api;

use App\Entity\Widget;
use App\Tests\Functional\FunctionalTestCase;

class WidgetApiTest extends FunctionalTestCase
{
    public function testWidgetListRequiresAuthentication(): void
    {
        $this->client->request('GET', '/api/widget/');

        $this->assertResponseRedirects('http://localhost/login');
    }

    public function testWidgetsAreListedByPositionWithTheirSensors(): void
    {
        $this->logIn();
        $sensor = $this->createSensor('AAA', true, 'Salon');

        $this->createWidget('Second', 2);
        $first = $this->createWidget('Premier', 1);
        $first->addSensor($sensor);
        $this->em()->flush();

        $this->client->request('GET', '/api/widget/');

        $this->assertResponseIsSuccessful();
        $json = $this->jsonResponse();
        $this->assertSame(['Premier', 'Second'], array_column($json, 'name'));
        $this->assertSame(['Salon'], array_column($json[0]['sensors'], 'name'));
        $this->assertSame([], $json[1]['sensors']);
        $this->assertSame(7, $json[0]['periodeValue']);
        $this->assertSame('days', $json[0]['periodeUnit']);
    }

    private function createWidget(string $name, int $position): Widget
    {
        $widget = (new Widget())
            ->setName($name)
            ->setPosition($position)
            ->setSize(1)
            ->setPeriodeValue(7)
            ->setPeriodeUnit('days');

        $em = $this->em();
        $em->persist($widget);
        $em->flush();

        return $widget;
    }
}
