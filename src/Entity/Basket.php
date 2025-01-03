<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DefaultTrait;
use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Basket
{
    use DefaultTrait;

    /**
     * @var Collection<int, BasketItem>
     */
    #[ORM\OneToMany(targetEntity: BasketItem::class, mappedBy: 'basket', orphanRemoval: true)]
    private Collection $items;

    #[ORM\OneToOne(inversedBy: 'basket', cascade: ['persist', 'remove'])]
    private User $user;

    /**
     * @var Collection<int, RemovedItem>
     */
    #[ORM\OneToMany(targetEntity: RemovedItem::class, mappedBy: 'basket')]
    private Collection $removedItems;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->items = new ArrayCollection();
        $this->removedItems = new ArrayCollection();
    }

    /**
     * @return Collection<int, BasketItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(BasketItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setBasket($this);
        }

        return $this;
    }

    public function removeItem(BasketItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getBasket() === $this) {
                $item->setBasket(null);
            }
        }

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, RemovedItem>
     */
    public function getRemovedItems(): Collection
    {
        return $this->removedItems;
    }

    public function addRemovedItem(RemovedItem $removedItem): static
    {
        if (!$this->removedItems->contains($removedItem)) {
            $this->removedItems->add($removedItem);
            $removedItem->setBasket($this);
        }

        return $this;
    }

    public function removeRemovedItem(RemovedItem $removedItem): static
    {
        if ($this->removedItems->removeElement($removedItem)) {
            // set the owning side to null (unless already changed)
            if ($removedItem->getBasket() === $this) {
                $removedItem->setBasket(null);
            }
        }

        return $this;
    }
}
