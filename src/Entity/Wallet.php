<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\Column(length: 255)]
    private ?string $name = "Current Account";

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2)]
    private ?string $solde_euro = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2)]
    private ?string $solde_cryptos = '0.00'; 

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Crypto::class, mappedBy: 'wallet')]
    private Collection $cryptos;


    public function __construct()
    {
        $this->cryptos = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSoldeEuro(): ?string
    {
        return $this->solde_euro;
    }

    public function setSoldeEuro(string $solde_euro): static
    {
        $this->solde_euro = $solde_euro;

        return $this;
    }

    public function getSoldeCryptos(): ?string
    {
        return $this->solde_cryptos;
    }

    public function setSoldeCryptos(string $solde_cryptos): static
    {
        $this->solde_cryptos = $solde_cryptos;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Crypto>
     */
    public function getCryptos(): Collection
    {
        return $this->cryptos;
    }

    public function addCrypto(Crypto $crypto): static
    {
        if (!$this->cryptos->contains($crypto)) {
            $this->cryptos->add($crypto);
            $crypto->addWallet($this);
        }

        return $this;
    }

    public function removeCrypto(Crypto $crypto): static
    {
        if ($this->cryptos->removeElement($crypto)) {
            $crypto->removeWallet($this);
        }

        return $this;
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

    
}
