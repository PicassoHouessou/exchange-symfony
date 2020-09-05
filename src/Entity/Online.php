<?php

namespace App\Entity;

use App\Repository\OnlineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OnlineRepository::class)
 */
class Online
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=210)
     */
    private $ipUser;

    /**
     * @ORM\Column(type="datetime")
     */
    private $insertedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpUser(): ?string
    {
        return $this->ipUser;
    }

    public function setIpUser(string $ipUser): self
    {
        $this->ipUser = $ipUser;

        return $this;
    }

    public function getInsertedAt(): ?\DateTimeInterface
    {
        return $this->insertedAt;
    }

    public function setInsertedAt(\DateTimeInterface $insertedAt): self
    {
        $this->insertedAt = $insertedAt;

        return $this;
    }
}
