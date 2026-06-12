<?php
namespace App\Controller;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
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
}
