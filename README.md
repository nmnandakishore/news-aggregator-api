# Simple News Aggregator API
.This is a simple news aggregator application that provides a unified API consolidating 3 different news API. This application provides features like search, filter, creation of personalized feed, rate limited API etc


## Installation
First, clone the project repository from GitHub using the following command:
```bash
git clone git@github.com:nmnandakishore/news-aggregator-api.git
```

###  Install the application's dependencies using Docker

Open your terminal or command prompt and navigate to the root directory of your Laravel project and run the Following Command:
 ```bash
docker run  --rm  \
-u "$(id -u):$(id -g)"  \
-v "$(pwd):/var/www/html"  \
-w /var/www/html  \
laravelsail/php83-composer:latest \
composer install  --ignore-platform-reqs
```
This command spins up a Docker container with PHP and Composer, mounts your project directory, and runs the `composer install` command to install the required dependencies.

### Run migrations
Run the below command to run migrations.
```bash
./vendor/bin/sail artisan migrate
```

### Run the application
Run the below command to run the application using docker.
```bash
./vendor/bin/sail up
```

### Run Laravel Scheduled Commads
Run the below command to run scheduled command in dev environment.
```bash
./vendor/bin/sail artisan schedule:work
```
This fetches the news into the database from below APIs once in every minute.
- NewsAPI .org
- The Guardian
- The New York Times API
