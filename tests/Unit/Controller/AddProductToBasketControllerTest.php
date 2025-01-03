<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\AddProductToBasketController;
use App\DTO\Request\ProductRequestDTO;
use App\Entity\Basket;
use App\Entity\BasketItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AddProductToBasketControllerTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private BasketRepository $basketRepository;
    private ProductRepository $productRepository;
    private UserRepository $userRepository;
    private AddProductToBasketController $controller;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->basketRepository = $this->createMock(BasketRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->controller = new AddProductToBasketController(
            $this->entityManager,
            $this->basketRepository,
            $this->productRepository,
            $this->userRepository
        );
    }

    public function testAddProductNotFound(): void
    {
        $requestDTO = new ProductRequestDTO('non-existent-uuid', 1);

        $this->productRepository
            ->method('findOneBy')
            ->with(['uuid' => $requestDTO->productUuid])
            ->willReturn(null);

        $response = $this->controller->__invoke($requestDTO);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(
            ['error' => 'Product with uuid: "non-existent-uuid" not found.'],
            json_decode($response->getContent(), true)
        );
    }

    public function testAddUserNotFound(): void
    {
        $requestDTO = new ProductRequestDTO('0890f105-8bab-49b6-8259-a0602c38c251', 1);

        $product = new Product(1, 'title', 'description', 'thumbnail');
        $this->productRepository
            ->method('findOneBy')
            ->with(['uuid' => $requestDTO->productUuid])
            ->willReturn($product);

        $this->userRepository
            ->method('find')
            ->with($requestDTO->userId)
            ->willReturn(null);

        $response = $this->controller->__invoke($requestDTO);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(
            ['error' => 'User with id: "1" not found.'],
            json_decode($response->getContent(), true)
        );
    }

    public function testAddProductSuccessfully(): void
    {
        $requestDTO = new ProductRequestDTO('0890f105-8bab-49b6-8259-a0602c38c251', 1);

        $product = new Product(1, 'title', 'description', 'thumbnail');
        $user = new User('moussa@ttl.be');
        $basket = new Basket($user);
        $basketItem = BasketItem::create($product, $basket);

        $this->productRepository
            ->method('findOneBy')
            ->with(['uuid' => $requestDTO->productUuid])
            ->willReturn($product);

        $this->userRepository
            ->method('find')
            ->with($requestDTO->userId)
            ->willReturn($user);

        $this->basketRepository
            ->method('findOneBy')
            ->with(['user' => $requestDTO->userId])
            ->willReturn($basket);

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = $this->controller->__invoke($requestDTO);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            ['message' => 'Added successfully.'],
            json_decode($response->getContent(), true)
        );
    }
}
