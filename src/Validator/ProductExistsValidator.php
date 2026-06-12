<?php
namespace App\Validator;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class ProductExistsValidator extends ConstraintValidator
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[\Symfony\Component\DependencyInjection\Attribute\Autowire(service: 'catalog.cache')]
        private CacheInterface $cache,
        private string $catalogUrl = 'http://localhost:8001/api/v1'
    ) {}


    public function validate($value, Constraint $constraint): void {
        if (null === $value) return;

        $cacheKey = 'product_exists_' . $value;

        try {
            $exists = $this->cache->get($cacheKey, function () use ($value) {
                $response = $this->httpClient->request(
                    'GET',
                    $this->catalogUrl . '/products/' . $value . '/',
                    ['timeout' => 3]
                );
                return $response->getStatusCode() !== 404;
            });

            if (!$exists) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ id }}', $value)->addViolation();
            }
        } catch (\Exception $e) {
            $this->context->buildViolation('Catalogue indisponible')->addViolation();
        }
    }
}
