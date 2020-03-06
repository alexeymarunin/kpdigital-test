<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkRepository")
 */
class Link
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $pretty;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $original;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPretty(): ?string
    {
        return $this->pretty;
    }

    public function setPretty(string $pretty): self
    {
        $this->pretty = $pretty;

        return $this;
    }

    public function getOriginal(): ?string
    {
        return $this->original;
    }

    public function setOriginal(string $original): self
    {
        $this->original = $original;

        return $this;
    }
}
