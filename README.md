# Atarim Test - URL shortening service

Includes two POST endpoints at api/encode and api/decode.
Both endpoints expect a "url" field.

This submission also includes:
- Hourly automated job to prune old URLs after 10 days
- Rate limits on each endpoint
- Collision prevention of encoded URLs
- Automated tests
- Validation

## Project Setup

### Install dependencies

```sh
composer install
```

### Create .env file

```sh
cp .env.example .env
```

### Launch db server and create a new schema

However you prefer

### Add db server information to .env file

### Run migrations

```sh
php artisan migrate:fresh
```

### Run dev server

```sh
php artisan serve
```

### Run automated jobs

```sh
php artisan schedule:work
```

### Run automated tests

```sh
php artisan test
```

### Sending API requests

I used Postman, if using this, ensure to add this header to each request:

```sh
Accept: application/json
```

This prevents Postman from ignoring validation JSON responses.
