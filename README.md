# Reviews Service (FlexShop)


Service Symfony de gestion des avis clients.


## Démarrer
```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n
symfony serve
```


## Endpoints
- Swagger : http://localhost:8000/api
- API : http://localhost:8000/api/reviews
