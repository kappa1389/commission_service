## Summary
This application is a service to calculate commissions for already made transactions

## Installation guide

### Clone the project
Clone this repository to your local machine using the following command:
```bash
git clone git@github.com:kappa1389/commission_service.git
```

### Running containers
Open `Terminal`, cd to the project folder and run the following command:
```bash
docker-compose up -d
```

#### Installing the dependencies
Make an ssh connection to the `core` container using this command:  

```
docker exec -it commission-core bash
```  

Now simply install the dependencies via composer
```bash
composer install
```

### Run Tests
Run tests using this command inside core container
```bash
vendor/bin/phpunit
```

### Manual test
Run this command inside core container to manually test the application, you need internet access for this,
you can modify public/sample.csv file and add transactions for manual test
```bash
php public/test.php public/sample.csv
```

### How to extend
- In order to add support for new currencies, you'll need to add the currency to src/Entity/ValueObject/Currency class

## Technical discussions (Images/Containers)
This project includes one docker container as follows.

`core`
php:8.1-fpm


## Improvements
- We can use a service container in order to resolve dependencies
- We can implement specification pattern to make easier queries to repositories
- we can move list of supported currencies from Currency class into a database to be easier to manage
