# Jewelry E-Commerce Platform

A Laravel 12 jewelry e-commerce application with a customer storefront, admin dashboard, inventory management, product publishing workflow, cart and checkout flow, order management, reviews, wishlists, support tickets, and realtime customer support chat.

The codebase is organized as a modular Laravel application under `app/Modules`, with separate modules for users, products, inventory, cart, orders, admin tools, and support.

## Features

- Customer storefront with home, about, product listing, product details, wishlist, cart, checkout, and order history pages.
- Authentication powered by Laravel Breeze-style flows, including registration, login, password reset code verification, email verification views, profile editing, and admin registration.
- Admin dashboard with customer management, product publishing, product enhancement, review moderation, inventory management, order management, chat queue handling, and ticket management.
- Inventory module for jewelry stock, SKU-level variations, status toggling, and inventory-to-product publishing.
- Product module with public catalog browsing, product search, reviews, wishlist integration, secure admin product management, and decorator-based product presentation.
- Cart and checkout module with strategy classes for shipping, payments, and promocodes.
- Order module for customer order tracking, completion, refund requests, order item views, and admin shipping/refund handling.
- Support module with tickets, replies, FAQ/self-service, live chat, queue management, chat history, and Laravel Reverb broadcasting support.
- Sanctum API authentication and module API endpoints for external clients.
- Postman collection included at `Jewelry_E-commerce_API.postman_collection.json`.
- Tailwind CSS, Alpine.js, Vite, Argon Dashboard Tailwind assets, and Laravel Echo/Pusher client support.

## Tech Stack

- PHP `^8.2`
- Laravel `^12.0`
- Laravel Sanctum
- Laravel Reverb
- Laravel Breeze
- Pest / PHPUnit
- Vite `^6`
- Tailwind CSS
- Alpine.js
- Axios
- Laravel Echo and Pusher JS
- SQLite by default for local development, with Docker support for Apache/PHP

## Project Structure

```text
app/
  Modules/
    Admin/       Admin dashboard, customers, and admin views
    Cart/        Cart, checkout, payment/shipping/promocode strategies
    Inventory/   Inventory models, factories, services, views, and APIs
    Order/       Orders, order items, and order management
    Product/     Catalog, reviews, wishlist, product management, security middleware
    Support/     Tickets, chat, queue, FAQ, self-service, broadcasting events
    User/        Auth, profile, users, API auth, and user views
database/
  migrations/    Core and module-related database migrations
  seeders/       Demo users, products, orders, and inventory seeders
resources/
  views/         Shared Blade views and layouts
  css, js/       Vite-managed frontend assets
routes/
  web.php        Web routes
  api.php        Main API route loader
public/          Static images, videos, CSS, JS, fonts, and built assets
```

## Requirements

Install these before running the project locally:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- SQLite, MySQL, or PostgreSQL
- Git

For realtime chat, also run Laravel Reverb locally.

## Installation

Clone the repository:

```bash
git clone <repository-url>
cd jewelry-e-commerce-php-laravel
```

Install PHP dependencies:

```bash
composer install
```

Install JavaScript dependencies:

```bash
npm install
```

Create your environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Create the default SQLite database if you are using the included `.env.example` configuration:

```bash
touch database/database.sqlite
```

On Windows PowerShell:

```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
```

Run migrations:

```bash
php artisan migrate
```

Seed demo data:

```bash
php artisan db:seed
```

Link public storage:

```bash
php artisan storage:link
```

Build frontend assets:

```bash
npm run build
```

## Environment Configuration

The default `.env.example` uses SQLite:

```env
DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
BROADCAST_CONNECTION=log
MAIL_MAILER=log
```

For realtime chat with Laravel Reverb, update the broadcasting settings:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=local-app
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=localhost
REVERB_PORT=8081
REVERB_SCHEME=http
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

If you use MySQL or PostgreSQL instead of SQLite, update `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`.

## Running Locally

Start the Laravel development server:

```bash
php artisan serve
```

Start the Vite development server in a separate terminal:

```bash
npm run dev
```

Run the queue worker in another terminal:

```bash
php artisan queue:work
```

Run Reverb if realtime chat/broadcasting is enabled:

```bash
php artisan reverb:start --port=8081
```

Open the app at:

```text
http://127.0.0.1:8000
```

You can also run the bundled Composer development command:

```bash
composer run dev
```

That starts the Laravel server, queue listener, Laravel Pail logs, and Vite together.

## Demo Accounts

The `SimpleUsersSeeder` creates these accounts:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| Admin | `admin2@example.com` | `password` |
| Customer | `user@example.com` | `password` |
| Customer | `user2@example.com` | `password` |

Seed them with:

```bash
php artisan db:seed --class=SimpleUsersSeeder
```

## Main Web Routes

| Area | Route |
| --- | --- |
| Home | `/` |
| About | `/about-us` |
| Product catalog | `/products` |
| Product details | `/products/{productOrInventoryId}` |
| Wishlist | `/wishlist` |
| Cart | `/cart` |
| Checkout | `/cart/checkout` |
| Orders | `/orders` |
| Tickets | `/tickets` |
| FAQ | `/faq` |
| Self-service | `/self-service` |
| Profile | `/profile` |
| Admin registration | `/admin/register` |
| Admin dashboard | `/admin/dashboard` |
| Admin customers | `/admin/customers` |
| Admin inventory | `/admin/inventory` |
| Admin product management | `/admin/product-management` |
| Admin reviews | `/admin/reviews` |
| Admin chat | `/admin/chat` |
| Admin chat queue | `/admin/chat-queue` |
| Admin tickets | `/admin/tickets` |
| Admin order management | `/admin/ordermanagement` |

Most customer actions require authentication. The main admin dashboard and admin management pages require an authenticated user with `is_admin = true`.

## API Overview

API routes are served under `/api`.

Authentication endpoints:

```text
POST   /api/register
POST   /api/login
POST   /api/logout
GET    /api/me
PUT    /api/me
PUT    /api/me/password
DELETE /api/me
POST   /api/password/request-code
POST   /api/password/verify-code
POST   /api/password/reset
```

Product endpoints:

```text
GET    /api/products
GET    /api/products/search
GET    /api/products/{product}
GET    /api/products/stats/overview
GET    /api/products/inventory/{inventoryId}
POST   /api/products
PUT    /api/products/{product}
DELETE /api/products/{product}
```

Inventory endpoints:

```text
GET    /api/inventory
GET    /api/inventory/{inventory}
POST   /api/inventory
PUT    /api/inventory/{inventory}
DELETE /api/inventory/{inventory}
```

Cart endpoints:

```text
GET    /api/cart
POST   /api/cart/add/{productId}
PUT    /api/cart/update/{id}
DELETE /api/cart/remove/{id}
DELETE /api/cart/clear
GET    /api/cart/checkout
POST   /api/cart/place-order
```

Order endpoints:

```text
GET    /api/orders
GET    /api/orders/{id}
GET    /api/orders/status/{status}
GET    /api/orders/{id}/items
GET    /api/orders/{id}/tracking
PATCH  /api/orders/{id}/complete
PATCH  /api/orders/{id}/refund
POST   /api/orders/{id}/refund-reason
```

Support endpoints:

```text
GET    /api/support/tickets
POST   /api/support/tickets
GET    /api/support/tickets/{ticket}
PUT    /api/support/tickets/{ticket}
POST   /api/support/tickets/{ticket}/reply
POST   /api/support/tickets/{ticket}/close
POST   /api/support/chat/start
GET    /api/support/chat/queue/{conversationId}
POST   /api/support/chat/{conversationId}/leave
POST   /api/support/chat/{conversationId}/terminate
GET    /api/support/chat/conversations/{conversation}
GET    /api/support/chat/conversations/{conversation}/messages
POST   /api/support/chat/conversations/{conversation}/messages
POST   /api/support/chat/messages
GET    /api/support/faq
GET    /api/support/categories
POST   /api/support/self-service/track-order
POST   /api/support/self-service/check-availability
```

Use the included Postman collection for request examples:

```text
Jewelry_E-commerce_API.postman_collection.json
```

For Sanctum-protected endpoints, include a bearer token:

```http
Authorization: Bearer <token>
Accept: application/json
```

## Testing

Run the test suite:

```bash
php artisan test
```

Or use the Composer script:

```bash
composer test
```

Run Laravel Pint formatting:

```bash
./vendor/bin/pint
```

On Windows PowerShell:

```powershell
vendor\bin\pint
```

## Frontend Assets

Run Vite in development:

```bash
npm run dev
```

Create a production build:

```bash
npm run build
```

Prepare local production-style caches and assets:

```bash
npm run prod:prepare
```

Run the production-style local stack:

```bash
npm run prod:all
```

## Docker

The repository includes a `Dockerfile` that builds a PHP 8.2 Apache image, installs Composer dependencies, installs Node dependencies, builds Vite assets, points Apache to `public/`, and starts the app through `start.sh`.

Build the image:

```bash
docker build -t jewelry-ecommerce .
```

Run the container:

```bash
docker run --rm -p 8000:80 --env-file .env jewelry-ecommerce
```

The startup script respects the `PORT` environment variable, which is useful for platforms such as Render:

```bash
docker run --rm -p 10000:10000 -e PORT=10000 --env-file .env jewelry-ecommerce
```

The Docker image installs PostgreSQL PDO extensions. If you deploy with PostgreSQL, set the appropriate `DB_*` environment variables in your deployment platform.

## Useful Artisan Commands

Generate formatted product IDs:

```bash
php artisan app:generate-product-ids
```

Run the chat observer test command:

```bash
php artisan chat:test-observer
```

Clear cached configuration, routes, and views:

```bash
php artisan optimize:clear
```

Cache production configuration:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Development Notes

- Module service providers are registered in `bootstrap/providers.php`.
- User API routes are loaded by `App\Modules\User\UserServiceProvider`.
- Product, cart, order, inventory, support, and admin API routes are included from `routes/api.php`.
- Realtime support chat uses Laravel Reverb-compatible broadcasting configuration.
- Chat and ticket actions use named rate limiters defined in `App\Providers\AppServiceProvider`.
- Product management uses custom middleware aliases for database security and secure error handling.
- Uploaded product media can be served through `/media/{path}` for files under the public disk `products/` directory.

## Troubleshooting

If database-backed sessions, cache, or queues fail, make sure migrations have been run:

```bash
php artisan migrate
```

If frontend changes are not appearing, rebuild or restart Vite:

```bash
npm run dev
```

If uploaded files are not visible, recreate the storage link:

```bash
php artisan storage:link
```

If routes or configuration look stale, clear Laravel caches:

```bash
php artisan optimize:clear
```

If realtime chat does not connect, verify that Reverb is running and that `BROADCAST_CONNECTION`, `REVERB_*`, and `VITE_REVERB_*` values match.

## License

This project is open-sourced software licensed under the MIT license.
