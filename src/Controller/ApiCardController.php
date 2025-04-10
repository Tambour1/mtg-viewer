<?php
namespace App\Controller;

use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/card', name: 'api_card_')]
#[OA\Tag(name: 'Card', description: 'Routes for all about cards')]
class ApiCardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {}

    #[Route('/all', name: 'List all cards', methods: ['GET'])]
    #[OA\Parameter(name: 'setCode', description: 'Filter cards by set code', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Get(description: 'Return all cards in the database')]
    #[OA\Response(response: 200, description: 'List all cards')]
    public function cardAll(Request $request): Response
    {
        $this->logger->info('List all cards');
        $setCode = $request->query->get('setCode');

        $queryBuilder = $this->entityManager->getRepository(Card::class)
            ->createQueryBuilder("c")
            ->setMaxResults(1000);

        if ($setCode) {
            $queryBuilder->andWhere('c.setCode = :setCode')
                ->setParameter('setCode', $setCode);
        }

        $cards = $queryBuilder->getQuery()->getResult();
        return $this->json($cards);
    }

    #[Route('/search', name: 'Search cards', methods: ['GET'])]
    #[OA\Parameter(name: 'query', description: 'Search query for card name', in: 'query', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'setCode', description: 'Filter search results by set code', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Get(description: 'Search cards by name')]
    #[OA\Response(response: 200, description: 'List of cards matching the search query')]
    public function cardSearch(Request $request): Response
    {
        $query = $request->query->get('query');
        $setCode = $request->query->get('setCode');
        $this->logger->info('Search cards with query: ' . $query);

        $queryBuilder = $this->entityManager->getRepository(Card::class)
            ->createQueryBuilder("c")
            ->where('c.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(20);

        if ($setCode) {
            $queryBuilder->andWhere('c.setCode = :setCode')
                ->setParameter('setCode', $setCode);
        }

        $cards = $queryBuilder->getQuery()->getResult();
        return $this->json($cards);
    }

    #[Route('/{uuid}', name: 'Show card', methods: ['GET'], requirements: ['uuid' => '[0-9a-fA-F\-]{36}'])]
    #[OA\Parameter(name: 'uuid', description: 'UUID of the card', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Get(description: 'Get a card by UUID')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardShow(string $uuid): Response
    {
        $this->logger->info('Show card ' . $uuid);
        $card = $this->entityManager->getRepository(Card::class)->findOneBy(['uuid' => $uuid]);
        if (!$card) {
            return $this->json(['error' => 'Card not found'], 404);
        }
        return $this->json($card);
    }

    #[Route('/setCodes', name: 'List all set codes', methods: ['GET'])]
    #[OA\Get(description: 'Return all available set codes')]
    #[OA\Response(response: 200, description: 'List all set codes')]
    public function listSetCodes(): Response
    {
        $this->logger->info('List all set codes');
        $setCodes = $this->entityManager->getRepository(Card::class)
            ->createQueryBuilder("c")
            ->select('DISTINCT c.setCode')
            ->getQuery()
            ->getResult();

        $setCodes = array_map(function ($item) {
            return $item['setCode'];
        }, $setCodes);

        return $this->json($setCodes);
    }
}
