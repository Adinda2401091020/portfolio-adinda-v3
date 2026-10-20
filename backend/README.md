# Seroja Coffee API

Laravel API backend for the Seroja Coffee store. The existing portfolio remains in the repository root; this Laravel app lives in `backend/`.

## Requirements

- PHP 8.3 or newer with PDO MySQL enabled
- Composer
- MySQL (XAMPP is supported)

## Local setup (PowerShell)

From the repository root:

```powershell
Set-Location backend
Copy-Item .env.example .env
composer install
php artisan key:generate
```

Start Apache and MySQL from the XAMPP Control Panel. In phpMyAdmin, create a database named `seroja_coffee` using `utf8mb4_unicode_ci`. The sample `.env.example` uses XAMPP's usual local defaults (`127.0.0.1:3306`, user `root`, empty password); change `DB_PORT`, `DB_USERNAME`, or `DB_PASSWORD` in `.env` if your XAMPP configuration differs. Run:

```powershell
php artisan migrate --seed
php artisan serve
```

The seed command adds three sample products. Prices are whole rupiah values and are examples only.

## Product API

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/products` | List active products, 15 per page |
| `GET` | `/api/products/{id}` | Show one active product |

Responses use Laravel's JSON resource envelope (`data`). Product data contains `id`, `name`, `description`, `price`, `stock`, and `image_path`. Inactive or soft-deleted products are not exposed.

## Data model

- `users`: customer/admin identity, phone, and role (public mass assignment cannot set role)
- `products`: catalog, stock, active flag, and soft deletion
- `carts`, `cart_items`: one cart per user, unique product per cart
- `orders`, `order_items`: shipping-address snapshot and item name/price snapshots
- `payments`: one manual payment record per order, with optional verifier

Money columns are unsigned integer rupiah amounts. Checkout, payment verification, order-state transitions, and authentication endpoints are not implemented yet; the schema is ready for those flows.

See [the database field specification](docs/spesifikasi-database.md) for the complete schema, field types, defaults, and relationships.

## Tests

Create a separate MySQL database named `kel3_pu_mi3c_test` in phpMyAdmin before running tests. Tests use the MySQL connection settings from `.env.testing` and refresh tables in this test database; they do not use the development database.

The verified testing connection is MySQL at `127.0.0.1:3306`, using database `kel3_pu_mi3c_test`. Keep any local database credentials in `.env.testing`; this environment file is ignored by Git and must not be committed.

From `backend/`:

```powershell
php artisan test
```
