<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Product;
use App\Message\ImportComicsMessage;
use App\Service\MarvelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ImportComicsMessageHandler
{
    public function __construct(
        private MarvelService $marvelService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(ImportComicsMessage $message): void
    {
        $comicsDTOs = $this->marvelService->fetchComics($message->getLimit(), $message->getOffset());

        foreach ($comicsDTOs as $comicDTO) {
            $this->entityManager->persist(Product::createFromDTO($comicDTO));
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
