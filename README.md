# Food Order App

A Laravel starter kit for building a food ordering platform with a role-aware, JWT-authenticated API. The project currently provides the foundation for customer, vendor, and administrator accounts so that ordering, restaurant, menu, cart, and delivery features can be added on top of a consistent authentication layer.

## Current Features

- Laravel 13 application running on PHP 8.3+
- JWT-based API authentication
- Customer, vendor, and administrator roles
- Role-protected API routes
- Registration endpoints for customers and vendors
- Administrator registration restricted to administrator users
- Login, logout, token refresh, and authenticated-user endpoints
- English and Arabic localization resources
- SQLite database configuration for local development
- Vite and Tailwind CSS frontend tooling
- Pest test suite
- Laravel Scramble API documentation support

> This is the starter foundation. Food ordering workflows such as restaurants, menus, products, carts, orders, payments, and delivery tracking are not implemented yet.

## Technology Stack

| Layer             | Technology                                                             |
| ----------------- | ---------------------------------------------------------------------- |
| Backend           | Laravel 13                                                             |
| Runtime           | PHP 8.3+                                                               |
| Authentication    | `php-open-source-saver/jwt-auth`                                       |
| API documentation | Laravel Scramble                                                       |
| Database          | SQLite by default; other Laravel-supported databases can be configured |
| Frontend tooling  | Vite and Tailwind CSS                                                  |
| Testing           | Pest                                                                   |

## Requirements

Make sure the following tools are installed:

- PHP 8.3 or higher
- Composer
- Node.js and npm
- A database supported by Laravel

The default configuration uses SQLite, so no separate database server is required for local development.

## Installation

Clone the repository and enter the project directory:

```bash
git clone https://github.com/Eyad-Dawood/Food-Order-App.git
cd Food-Order-App
```

Install the PHP and JavaScript dependencies:

```bash
composer install
npm install
```

Create the environment file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Create the SQLite database if it does not already exist:

```bash
touch database/database.sqlite
```

On Windows PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File
```

Run the migrations:

```bash
php artisan migrate
```

Build the frontend assets:

```bash
npm run build
```

The Composer setup script can perform the main setup steps automatically:

```bash
composer run setup
```

## Running the Application

Start the Laravel development server:

```bash
php artisan serve
```

In a separate terminal, start the Vite development server when working on frontend assets:

```bash
npm run dev
```

The application is available at [http://localhost:8000](http://localhost:8000) by default.

## API Authentication

API routes are prefixed with `/api`. Authentication uses bearer JWTs.

### Public authentication endpoints

| Method | Endpoint                      | Description                           |
| ------ | ----------------------------- | ------------------------------------- |
| `POST` | `/api/auth/register/customer` | Register a customer                   |
| `POST` | `/api/auth/register/vendor`   | Register a vendor                     |
| `POST` | `/api/auth/login`             | Authenticate a user and receive a JWT |
| `POST` | `/api/auth/refresh`           | Refresh a valid JWT                   |

### Authenticated endpoints

Send the token in the request header:

```http
Authorization: Bearer <access-token>
```

| Method | Endpoint                   | Access                 |
| ------ | -------------------------- | ---------------------- |
| `GET`  | `/api/auth/me`             | Any authenticated user |
| `POST` | `/api/auth/logout`         | Any authenticated user |
| `POST` | `/api/auth/register/admin` | Administrator only     |

Successful login and registration responses include:

```json
{
    "access_token": "eyJ...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
        "id": 1,
        "name": "Example User",
        "email": "user@example.com",
        "role": "customer"
    }
}
```

The available roles are:

- `admin`
- `vendor`
- `customer`

Role middleware accepts one or more pipe-separated roles. For example:

```php
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    // Administrator-only routes
});
```

## Project Structure

```text
app/
├── Enums/Auth/              User roles
├── Http/Controllers/Api/    API controllers
├── Http/Middleware/         Locale and role middleware
├── Http/Requests/           Request validation
└── Models/                  Eloquent models

database/
├── factories/               Model factories
├── migrations/              Database schema
└── seeders/                 Database seeders

routes/
├── api.php                  API routes
├── console.php              Console routes
└── web.php                  Web routes

resources/
└── lang/                    English and Arabic translations

tests/
├── Feature/                 HTTP and integration tests
└── Unit/                    Isolated unit tests
```

## Configuration

Environment-specific values belong in `.env`. The committed `.env.example` contains the local defaults.

Important settings include:

```dotenv
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
JWT_SECRET=
```

If `JWT_SECRET` is not present after installation, generate it with:

```bash
php artisan jwt:secret
```

Never commit `.env` or any secret keys to source control.

## Testing

Run the complete test suite with:

```bash
php artisan test
```

Or use the Composer shortcut:

```bash
composer test
```

Run a specific test file:

```bash
php artisan test tests/Feature/ExampleTest.php
```

## Code Quality

Format changed PHP files with Laravel Pint:

```bash
vendor/bin/pint --dirty --format agent
```

## API Documentation

Laravel Scramble is included for API documentation. In a running local application, inspect the configured Scramble routes or generate documentation using the commands provided by the installed Scramble version:

```bash
php artisan list
```

## Roadmap

The starter kit is prepared for the following domain modules:

1. Restaurant and vendor management
2. Menu and product management
3. Customer browsing and search
4. Cart and checkout
5. Order lifecycle management
6. Payment integration
7. Delivery and order tracking
8. Notifications and reporting

## Contributing

1. Create a feature branch.
2. Keep changes focused and follow the existing Laravel structure.
3. Add or update tests for behavior changes.
4. Run the relevant tests and code formatter before opening a pull request.
5. Describe API or database changes clearly in the pull request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
