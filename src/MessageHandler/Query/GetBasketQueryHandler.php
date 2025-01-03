<?php

declare(strict_types=1);

namespace App\MessageHandler\Query;

use App\DTO\Response\BasketItemResponseDTO;
use App\DTO\Response\BasketResponseDTO;
use App\DTO\Response\PriceResponseDTO;
use App\DTO\Response\ProductResponseDTO;
use App\Entity\Basket;
use App\Message\Query\GetBasketQuery;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetBasketQueryHandler
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(GetBasketQuery $query): BasketResponseDTO
    {
        $items = [];
        $total = 0;

        $user = $this->userRepository->findOneBy(['uuid' => $query->getUserId()->toString()]);

        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        $basket = $user->getBasket();

        if (!$basket instanceof Basket) {
            throw new NotFoundHttpException('User has no basket yet.');
        }

        foreach ($basket->getItems() as $item) {
            $prices = [];

            foreach ($item->getProduct()->getPrices() as $price) {
                $prices[] = new PriceResponseDTO($price->getType(), $price->getAmount() ?? 0);
                $total += $price->getAmount();
            }

            $items[] = new BasketItemResponseDTO(
                $item->getUuid()->toString(),
                new ProductResponseDTO(
                    $item->getProduct()->getUuid()->toString(),
                    $item->getProduct()->getTitle() ?? '',
                    $prices,
                ),
                $item->getAddedAt() ?? new \DateTimeImmutable()
            );
        }

        return new BasketResponseDTO(
            $basket->getUuid()->toString(),
            $basket->getUser()->getUuid()->toString(),
            $items,
            \count($items),
            $total,
        );
    }
}
