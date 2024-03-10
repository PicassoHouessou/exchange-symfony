<?php

namespace App\Entity;

use App\Repository\ConversionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConversionRepository::class)]
class Conversion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @Assert\Sequentially(
     *     @Assert\NotBlank(),
     *     @Assert\Length(max="30")
     * )
     */
    #[ORM\Column(type: 'string', length: 100)]
    private $number;

    /**
     * @Assert\Sequentially(
     *     @Assert\NotNull(),
     *     @Assert\Length(max="149"),
     *     @Assert\Email()
     * )
     */
    #[ORM\Column(type: 'string', length: 150)]
    private $email;

    /**
     * @Assert\Sequentially(
     *     @Assert\NotBlank(),
     *     @Assert\Length(max="149")
     * )
     */
    #[ORM\Column(type: 'string', length: 150)]
    private $fullName;

    #[ORM\Column(type: 'datetime')]
    #[Assert\DateTime]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 150)]
    private $currencyFrom;

    #[ORM\Column(type: 'string', length: 150)]
    private $currencyTo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCurrencyFrom(): ?string
    {
        return $this->currencyFrom;
    }

    public function setCurrencyFrom(string $currencyFrom): self
    {
        $this->currencyFrom = $currencyFrom;

        return $this;
    }

    public function getCurrencyTo(): ?string
    {
        return $this->currencyTo;
    }

    public function setCurrencyTo(string $currencyTo): self
    {
        $this->currencyTo = $currencyTo;

        return $this;
    }
}
