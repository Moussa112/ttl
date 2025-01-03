<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Controller\AddProductToBasketController;
use App\DTO\Request\ProductRequestDTO;
use App\Entity\BasketItem;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddProductToBasketControllerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$container->get(EntityManagerInterface::class);
    }

    public function testAddProductToBasketStoresDataInDatabase(): void
    {
        $user = new User('moussa@ttl.com');
        $this->entityManager->persist($user);

        $product = new Product(1, 'Awesome comic', 'description', 'thumbnail');
        $price = new Price('printPrice', 1.99, $product);
        $product->addPrice($price);
        $this->entityManager->persist($price);
        $this->entityManager->persist($product);

        $this->entityManager->flush();

        $controller = static::getContainer()->get(AddProductToBasketController::class);
        $requestDTO = new ProductRequestDTO(
            productUuid: $product->getUuid()->toString(),
            userId: $user->getId()
        );

        $response = $controller($requestDTO);

        $this->assertSame(200, $response->getStatusCode());

        $basketItemRepository = $this->entityManager->getRepository(BasketItem::class);
        $basketItem = $basketItemRepository->findOneBy(['product' => $product]);

        $this->assertNotNull($basketItem, 'Basket item should exist in the database.');
        $this->assertSame($product->getId(), $basketItem->getProduct()->getId());
        $this->assertSame($user->getId(), $basketItem->getBasket()->getUser()->getId());
    }

    protected function tearDown(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\BasketItem')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Basket')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Product')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();

        parent::tearDown();
    }
}
