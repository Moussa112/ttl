<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DefaultTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User
{
    use DefaultTrait;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Basket $basket = null;

    public function __construct(?string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(?Basket $basket): static
    {
        // unset the owning side of the relation if necessary
        if (null === $basket && null !== $this->basket) {
            $this->basket->setUser($this);
        }

        // set the owning side of the relation if necessary
        if (null !== $basket && $basket->getUser() !== $this) {
            $basket->setUser($this);
        }

        $this->basket = $basket;

        return $this;
    }
}
