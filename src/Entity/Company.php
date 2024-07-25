<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Table(name: 'companies')]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/')]
    private ?string $CNPJ = null;

    #[ORM\OneToOne(targetEntity: Address::class, inversedBy: 'company', cascade: ['persist', 'remove'])]
    #[Assert\NotBlank]
    private Address $address;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Partner>
     */
    #[ORM\OneToMany(targetEntity: Partner::class, mappedBy: 'company')]
    private Collection $partners;

    public function __construct()
    {
        $this->partners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCNPJ(): ?string
    {
        return $this->CNPJ;
    }

    public function setCNPJ(?string $CNPJ): static
    {
        $this->CNPJ = $CNPJ;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Partner>
     */
    public function getPartners(): Collection
    {
        return $this->partners;
    }

    public function addPartner(Partner $partner): static
    {
        if (!$this->partners->contains($partner)) {
            $this->partners->add($partner);
            $partner->setCompany($this);
        }

        return $this;
    }

    public function removePartner(Partner $partner): static
    {
        if ($this->partners->removeElement($partner)) {
            if ($partner->getCompany() === $this) {
                $partner->setCompany(null);
            }
        }

        return $this;
    }
}
