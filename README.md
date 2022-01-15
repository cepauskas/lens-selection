## Assesment task

Mini project for the attributes lookup task. 

Short task analysis and more detailed choosen solution can be found in [Concetps and choosen solution](CONCEPTS.md)

### Solution:
- Relational normalized mysql database
- Within symfony application cache layer 
  - Implemented using symfony's cache component on demand 
  - In combination of decorator / chain of responsibilities patterns
  - CachedAttributesFetcher decorator service decorates DbAttributesFetcher service
  - Cache TTL configured in ENV as ATTRIBUTES_CACHE_TTL variable

## APP

### Environment

- Nginx 1.21
- Symfony 6
- PHP 8.1
- MySQL 8.0

### Startup

Server and dependencies for development were loaded over docker.

Start services:

```
docker-compose up -d
```

NOTE: It will compile PHP image, it will take some time. There are no non-default symfony's required extensions. Nginx and php services could be commented out, and [Symfony Local Web Server](https://symfony.com/doc/current/setup/symfony_server.html) be used instead if prefferable.

Project setup:

```
docker-compose exec php bash
composer install
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --group=small
```

Fixtures ("small" group) contains own sample data:
```
- 5 attributes 
  - Add
  - Axis
  - Color
  - Sph
  - Cyl
- 6 products & attribute values
  - Product1 Sph: -20.00 | Cyl: -2.25,-1.50 
  - Product2 Sph: 0.00 | Cyl: -2.25,-1.50
  - Product3 Sph: +20.00 | Cyl: -1.50,-0.75
  - Product4 Add: low | Axis: 0,90 | Color: Blue, Green
  - Product5 Add: medium | Axis: 0,90 | Color: Blue, Green
  - Product6 Add: high | Axis: 90,180 | Color: Blue
```

### Testing
- Tests are working with "small" group fixtures
- ATTRIBUTES_CACHE_TTL should be creater than 0

```
php bin/phpunit
```

For simulating expected amount (SQL query checkup) of attributes/values/products there is additional "large" group fixture. Tests are not using it.

### Api usage examples

```
GET http://localhost/parameter
{"Add":["high","low","medium"],"Axis":["0","180","90"],"Color":["Blue","Green"],"Cyl":["-0.75","-1.50","-2.25"],"Sph":["-20.00","+20.00","0.00"]}

GET http://localhost/parameter?Cyl=-0.75
{"Cyl":["-0.75"],"Sph":["+20.00"]}

GET http://localhost/parameter?Sph=-20.00
{"Cyl":["-1.50","-2.25"],"Sph":["-20.00"]}

GET http://localhost/parameter?Axis=90
{"Add":["high","low","medium"],"Axis":["90"],"Color":["Blue","Green"]}

GET http://localhost/parameter?Axis=90&Color=Green
{"Add":["low","medium"],"Axis":["90"],"Color":["Green"]}

GET http://localhost/parameter?Axis=90&Color=Blue
{"Add":["high","low","medium"],"Axis":["90"],"Color":["Blue"]}

```

**NOTE: attribute names are case sensitive.**