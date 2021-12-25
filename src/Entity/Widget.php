<?php

namespace App\Entity;

use App\Repository\WidgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
    private $startTime;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $endTime;

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

    public function getStartTime(): ?string
    {
        return $this->startTime;
    }

    public function setStartTime(string $startTime): self
    {
        $this->startTime = $startTime;

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
}
