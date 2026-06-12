<?php
namespace App\Controller;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/stats')]
class ReviewStatsController extends AbstractController
{
    #[Route('/reviews/average/{productId}', methods: ['GET'])]
    public function getAverageRating(
        int $productId,
        ReviewRepository $reviewRepository
    ): JsonResponse {
        $average = $reviewRepository->getAverageRatingForProduct($productId);

        if ($average === null) {
            return $this->json([
                'productId' => $productId,
                'averageRating' => null,
                'message' => 'Aucun avis pour ce produit'
            ], 404);
        }

        return $this->json([
            'productId' => $productId,
            'averageRating' => round($average, 2),
            'totalReviews' => count($reviewRepository->findBy(['productId' => $productId]))
        ]);
    }

    #[Route('/reviews/search', methods: ['GET'], priority: 10)]
    public function searchReviews(
        Request $request,
        ReviewRepository $reviewRepository
    ): JsonResponse {
        $query = $request->query->get('q');

        if (empty($query) || strlen($query) < 2) {
            return $this->json([
                'error' => 'Paramètre "q" requis (min 2 caractères)'
            ], 400);
        }

        $results = $reviewRepository->searchByComment($query);

        return $this->json([
            'query' => $query,
            'count' => count($results),
            'reviews' => $results
        ]);
    }
}
