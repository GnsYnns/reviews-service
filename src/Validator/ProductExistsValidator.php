<?php
namespace App\Validator;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class ProductExistsValidator extends ConstraintValidator
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $catalogUrl = 'http://localhost:8000/api/v1'
    ) {}


    public function validate($value, Constraint $constraint): void {
        if (null === $value) return;
        try {
            $response = $this->httpClient->request(
                'GET',
                $this->catalogUrl . '/products/' . $value . '/',
                ['timeout' => 3]
            );
            if ($response->getStatusCode() === 404) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ id }}', $value)->addViolation();
            }
        } catch (\Exception $e) {
            $this->context->buildViolation('Catalogue indisponible')->addViolation();
        }
    }
}
