<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserPurchaseRepository")
 */
class UserPurchase
{
    use \App\Entity\Traits\Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"api:products:output"})
     *
     * @ORM\Column(type="datetime")
     */
    private $purchasedAt;

    /**
     * @Groups({"api:products:output"})
     *
     * @ORM\Column(type="float")
     */
    private $purchasePrice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userPurchases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;


    public function getId()
    {
        return $this->id;
    }

    public function getPurchasedAt(): ?\DateTimeInterface
    {
        return $this->purchasedAt;
    }

    public function setPurchasedAt(\DateTimeInterface $PurchasedAt): self
    {
        $this->purchasedAt = $PurchasedAt;

        return $this;
    }

    public function getPurchasePrice(): ?float
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(float $PurchasePrice): self
    {
        $this->purchasePrice = $PurchasePrice;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
