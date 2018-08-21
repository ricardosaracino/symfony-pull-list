<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    ## todo series ManyToOne? , by ManyToMany

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $details;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $UPC;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageUrl;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     */
    private $vendorPrice;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     */
    private $customerPrice;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $customerDiscount;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     */
    private $customerDiscountPrice;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $preOrderDeadlineAt;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $releasedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductType", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $productType;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Genre", inversedBy="products")
     */
    private $genres;

    /**
     * @Groups({"get_product"})
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Creator", inversedBy="products")
     */
    private $creators;


    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->creators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getUPC(): ?string
    {
        return $this->UPC;
    }

    public function setUPC(string $UPC): self
    {
        $this->UPC = $UPC;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getVendorPrice(): ?float
    {
        return $this->vendorPrice;
    }

    public function setVendorPrice(?string $vendorPrice): self
    {
        $this->vendorPrice = $vendorPrice;

        return $this;
    }

    public function getCustomerPrice(): ?float
    {
        return $this->customerPrice;
    }

    public function setCustomerPrice(?string $customerPrice): self
    {
        $this->customerPrice = $customerPrice;

        return $this;
    }

    public function getCustomerDiscountPrice(): ?float
    {
        return $this->customerDiscountPrice;
    }

    public function setCustomerDiscountPrice(?string $customerDiscountPrice): self
    {
        $this->customerDiscountPrice = $customerDiscountPrice;

        return $this;
    }

    public function getCustomerDiscount(): ?string
    {
        return $this->customerDiscount;
    }

    public function setCustomerDiscount(?string $customerDiscount): self
    {
        $this->customerDiscount = $customerDiscount;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeInterface
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTimeInterface $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    public function getPreOrderDeadlineAt(): ?\DateTimeInterface
    {
        return $this->preOrderDeadlineAt;
    }

    public function setPreOrderDeadlineAt(?\DateTimeInterface $preOrderDeadlineAt): self
    {
        $this->preOrderDeadlineAt = $preOrderDeadlineAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    public function getProductType(): ?ProductType
    {
        return $this->productType;
    }

    public function setProductType(?ProductType $productType): self
    {
        $this->productType = $productType;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
        }

        return $this;
    }

    /**
     * @return Collection|Creator[]
     */
    public function getCreators(): Collection
    {
        return $this->creators;
    }

    public function addCreator(Creator $creator): self
    {
        if (!$this->creators->contains($creator)) {
            $this->creators[] = $creator;
        }

        return $this;
    }

    public function removeCreator(Creator $creator): self
    {
        if ($this->creators->contains($creator)) {
            $this->creators->removeElement($creator);
        }

        return $this;
    }
}
