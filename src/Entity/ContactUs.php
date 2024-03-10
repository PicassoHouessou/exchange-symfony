<?php

namespace App\Entity;

use App\Repository\ContactUsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactUsRepository::class)]
class ContactUs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 210)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(max: '210')]
    private $subject;

    #[ORM\Column(type: 'text')]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(max: '5000')]
    private $message;

    #[ORM\Column(type: 'string', length: 210)]
    #[Assert\Email]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(max: '210')]
    private $email;

    /**
     * @var \DateTimeInterface|null
     */
    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 210)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(max: '210')]
    private $sender;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt ;

        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }
}
