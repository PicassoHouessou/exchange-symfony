<?php

namespace App\Entity;

use App\Repository\UserInfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserInfoRepository::class)
 * @Vich\Uploadable()
 */
class UserInfo implements \Serializable
{
    public const GENDER_MALE = 'M';
    public const GENDER_FEMALE= 'F' ;

    public const MAIN_ACTIVITY_ENTREPENOR = 'entrepenor' ;
    public const MAIN_ACTIVITY_INVESTOR = 'investor' ;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=210, nullable=true)
     * @Assert\Sequentially(
     *     @Assert\NotNull(),
     *     @Assert\Length(max="210")
     * )
     */
    private $mainActivity;

    /**
     * @ORM\Column(type="string", length=210)
     * @Assert\Sequentially({
     *     @Assert\NotNull(),
     *     @Assert\NotBlank(),
     *     @Assert\Length(max="210")
     * })
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=210)
     * @Assert\Sequentially({
     *     @Assert\NotNull(),
     *     @Assert\NotBlank(),
     *     @Assert\Length(max="210")
     * })
     */
    private $lastName;
//, groups={"registration"}
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="avatarName")
     * @Assert\File( maxSize="3M",mimeTypes={ "image/png", "image/jpeg" } )
     * @var File|null
     */
    private $avatarFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $avatarName;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="info", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Sequentially(
     *     @Assert\NotNull(),
     *     @Assert\Length(max="210")
     * )
     */
    private $phoneNumber;
    /**
     * @ORM\Column(type="datetime",nullable=true)
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=210, nullable=true)
     * @Assert\Length(max="210")
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=210, nullable=true)
     * @Assert\Length(max="210")
     */
    private $city;

    /**
     * @ORM\Column(type="datetime", length=210, nullable=true)
     * @Assert\Type("\DateTimeInterface")
     */
    private $birthday;

    /**
     * ORM\Column(type="string", length=210, nullable=true)
     * Assert\Length(max="210")

    private $profession;
    */

    /**
     * @ORM\Column(type="string", length=210, nullable=true)
     *  @Assert\Sequentially({
     *     @Assert\NotNull(),
     *     @Assert\NotBlank(),
     *     @Assert\Length(max="100")
     * })
     */
    private $gender;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * )
     */
    private $hasCompany;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     */
    private $bio;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMainActivity() : ?string
    {
        return $this->mainActivity;
    }

    public function setMainActivity(?string $mainActivity): self
    {
        $this->mainActivity = $mainActivity;

        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName() : ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName ): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatarName(?string $avatarName) : self
    {
        $this->avatarName = $avatarName;
        return $this ;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $avatarFile
     */
    public function setAvatarFile(?File $avatarFile = null) :self
    {
        $this->avatarFile = $avatarFile ;

        if (null !== $avatarFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        // set (or unset) the owning side of the relation if necessary
        $newInfo = null === $user ? null : $this;
        if ($user->getInfo() !== $newInfo) {
            $user->setInfo($newInfo);
        }

        return $this;
    }

    public function getPhoneNumber() : ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?int $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getBirthday() : ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }
/*
    public function getProfession() : ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }
    */

    /**
     * Verify that user complete your profile
     * @return bool true if user complete
     */
    public  function isComplete()
    {
        return isset(
            $this->city ,
            $this->country ,
            $this->mainActivity ,
            $this->lastName ,
            $this->firstName ,
            $this->avatarName
        )
            ;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getFullName()
    {
        return $this->getFirstName() . ' '. $this->getLastName() ;
    }

    public function getHasCompany(): ?bool
    {
        return $this->hasCompany;
    }

    public function setHasCompany(?bool $hasCompany): self
    {
        $this->hasCompany = $hasCompany;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;
        return $this;
    }

}
