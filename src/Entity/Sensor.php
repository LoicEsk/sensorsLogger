<?php

namespace App\Entity;

use App\Repository\SensorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SensorRepository::class)
 */
class Sensor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $keyCode;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $unit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif = false;
    

    public function __construct( $keyCode )
    {
        $this->setKeyCode( $keyCode );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getKeyCode(): ?string
    {
        return $this->keyCode;
    }

    public function setKeyCode(string $keyCode): self
    {
        $this->keyCode = $keyCode;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }
}
