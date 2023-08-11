<?php

namespace App\Entity;

use App\Repository\WidgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=WidgetRepository::class)
 */
class Widget
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Sensor::class)
     */
    private $sensors;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $endTime = 'PO';

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\Column(type="smallint")
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $periodeUnit;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     */
    private $periodeValue;

    /**
     * @ORM\Column(type="string", length=16)
     * @Assert\NotNull()
     */
    private $widgetType;

    public function __construct()
    {
        $this->sensors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Sensor[]
     */
    public function getSensors(): Collection
    {
        return $this->sensors;
    }

    public function addSensor(Sensor $sensor): self
    {
        if (!$this->sensors->contains($sensor)) {
            $this->sensors[] = $sensor;
        }

        return $this;
    }

    public function removeSensor(Sensor $sensor): self
    {
        $this->sensors->removeElement($sensor);

        return $this;
    }

    public function getEndTime(): ?string
    {
        return $this->endTime;
    }

    public function setEndTime(string $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPeriodeUnit(): ?string
    {
        return $this->periodeUnit;
    }

    public function setPeriodeUnit(string $periodeUnit): self
    {
        $this->periodeUnit = $periodeUnit;

        return $this;
    }

    public function getPeriodeValue(): ?int
    {
        return $this->periodeValue;
    }

    public function setPeriodeValue(int $periodeValue): self
    {
        $this->periodeValue = $periodeValue;

        return $this;
    }

    public function getWidgetType(): ?string
    {
        return $this->widgetType;
    }

    public function setWidgetType(string $widgetType): self
    {
        $this->widgetType = $widgetType;

        return $this;
    }
}
