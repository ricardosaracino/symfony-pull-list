<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @see https://symfony.com/doc/current/security/entity_provider.html
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailVerificationToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $emailVerifiedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $emailVerificationTokenExpiresAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserPurchase", mappedBy="user")
     */
    private $userPurchases;


    public function __construct()
    {
        $this->isActive = true;

        $this->userPurchases = new ArrayCollection();

        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
    }

    ## UserInterface

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


    ## Serialize

    public function serialize()
    {
        return serialize(array(
            $this->id, $this->username, $this->password, // see section on salt below
            // $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list ($this->id, $this->username, $this->password, // see section on salt below
            // $this->salt
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

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

    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    public function setEmailVerificationToken(?string $emailVerificationToken): self
    {
        $this->emailVerificationToken = $emailVerificationToken;

        return $this;
    }

    public function getEmailVerifiedAt(): ?\DateTimeInterface
    {
        return $this->emailVerifiedAt;
    }

    public function setEmailVerifiedAt(?\DateTimeInterface $emailVerifiedAt): self
    {
        $this->emailVerifiedAt = $emailVerifiedAt;

        return $this;
    }

    public function getEmailVerificationTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->emailVerificationTokenExpiresAt;
    }

    public function setEmailVerificationTokenExpiresAt(?\DateTimeInterface $emailVerificationTokenExpiresAt): self
    {
        $this->emailVerificationTokenExpiresAt = $emailVerificationTokenExpiresAt;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|UserPurchase[]
     */
    public function getUserPurchases(): Collection
    {
        return $this->userPurchases;
    }

    public function addUserPurchase(UserPurchase $userPurchase): self
    {
        if (!$this->userPurchases->contains($userPurchase)) {
            $this->userPurchases[] = $userPurchase;
            $userPurchase->setUsers($this);
        }

        return $this;
    }

    public function removeUserPurchase(UserPurchase $userPurchase): self
    {
        if ($this->userPurchases->contains($userPurchase)) {
            $this->userPurchases->removeElement($userPurchase);
            // set the owning side to null (unless already changed)
            if ($userPurchase->getUsers() === $this) {
                $userPurchase->setUsers(null);
            }
        }

        return $this;
    }
}
