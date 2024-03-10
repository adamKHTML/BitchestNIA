<?php

namespace App\Entity;

use App\Repository\CryptoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryptoRepository::class)]

class Crypto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2)]
    private ?string $actual_value = null;

    #[ORM\ManyToMany(targetEntity: Wallet::class, inversedBy: 'cryptos')]
    private Collection $wallet;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2)]
    private ?string $price = null;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2)]
    private ?string $priceBTC = null;

    

   

    public function __construct()
    {
        $this->wallet = new ArrayCollection();

       
        if (empty($this->price) || empty($this->priceBTC) || empty($this->actual_value)) {
            $this->generateRandomValues();
        }
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

    public function getActualValue(): ?string
    {
        return $this->actual_value;
    }

    public function setActualValue(string $actual_value): static
    {
        $this->actual_value = $actual_value;

        return $this;
    }

    /**
     * @return Collection<int, Wallet>
     */
    public function getWallet(): Collection
    {
        return $this->wallet;
    }

    public function addWallet(Wallet $wallet): static
    {
        if (!$this->wallet->contains($wallet)) {
            $this->wallet->add($wallet);
        }

        return $this;
    }

    public function removeWallet(Wallet $wallet): static
    {
        $this->wallet->removeElement($wallet);

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceBTC(): ?string
    {
        return $this->priceBTC;
    }

    public function setPriceBTC(string $priceBTC): static
    {
        $this->priceBTC = $priceBTC;

        return $this;
    }
   

    private function generateRandomValues(): void
    {
        $this->price = $this->generateRandomPrice();
        $this->priceBTC = $this->generateRandomPriceBTC();
        $this->actual_value = $this->generateRandomActualValue();
    }

    private function generateRandomPrice(): string
    {
       
        return number_format(mt_rand(500, 5000) / 100, 2);
    }

    private function generateRandomPriceBTC(): string
    {
      
        return number_format(mt_rand(1, 10) / 100, 2);
    }

    private function generateRandomActualValue(): string
    {
       
        return number_format(mt_rand(50, 200) / 100, 2);
    }

    
   

  
}
