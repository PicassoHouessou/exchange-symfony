<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CurrencyRepository::class)
 * @UniqueEntity("code")
 */
class Currency
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     */
    private $reserve;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\Sequentially(
     *     @Assert\NotBlank(),
     *     @Assert\Length(max="150")
     * )
     */
    private $code;

    /**
     * @ORM\Column(type="float")
     * @Assert\Sequentially(
     *     @Assert\NotBlank()
     * )
     */
    private $rate;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\Sequentially(
     *     @Assert\NotBlank()
     * )
     */
    private $label;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReserve(): ?float
    {
        return $this->reserve;
    }

    public function setReserve(?float $reserve): self
    {
        $this->reserve = $reserve;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setcode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(?float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }
}
