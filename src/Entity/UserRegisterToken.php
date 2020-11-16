<?php

namespace App\Entity;

use App\Repository\UserRegisterTokenRepository;
use Doctrine\ORM\Mapping as ORM;
/*
/**
 * @ORM\Entity(repositoryClass=UserRegisterTokenRepository::class)
 ///*
class UserRegisterToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     ///*
    private $id;

    /**
     * @ORM\Column(type="integer")
     ///*
    private $userId;

    /**
     * @ORM\Column(type="datetime")
     ///*
    private $requestedAt;

    /**
     * @ORM\Column(type="datetime")
     ///*
    private $expireAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getRequestedAt(): ?\DateTimeInterface
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(\DateTimeInterface $requestedAt): self
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeInterface
    {
        return $this->expireAt;
    }

    public function setExpireAt(\DateTimeInterface $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }
}
*/