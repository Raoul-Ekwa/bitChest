# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

BitChest is a cryptocurrency trading platform built with **Symfony 7.4** (PHP 8.2+). It allows clients to buy/sell cryptocurrencies and manage a portfolio, while admins manage users and crypto listings.

## Common Commands

### Development Server
```bash
symfony server:start          # Start local dev server (requires Symfony CLI)
symfony server:stop           # Stop the server
```

### Database
```bash
php bin/console doctrine:migrations:migrate          # Run pending migrations
php bin/console doctrine:fixtures:load               # Load fixtures (resets DB data)
php bin/console doctrine:fixtures:load --append      # Load fixtures without purging
```

### Generate Crypto Price Quotes
```bash
php bin/console app:generate-quotes                  # Run the quote generator command
```

### Tests
```bash
php bin/phpunit                                      # Run all tests
php bin/phpunit tests/Unit                           # Run unit tests only
php bin/phpunit tests/Functional                     # Run functional tests only
php bin/phpunit --filter TestMethodName              # Run a single test
```

### Assets
```bash
php bin/console importmap:install                    # Install JS dependencies (no npm build step needed)
php bin/console asset-map:compile                    # Compile assets for production
```

### Cache
```bash
php bin/console cache:clear                          # Clear application cache
```

## Architecture

### User Hierarchy (Doctrine Joined Table Inheritance)
`User` is an abstract base entity. Both `Administrator` and `Client` extend it via joined table inheritance. Role-based access control is enforced at the route level:
- `/admin/*` → `ROLE_ADMIN`
- `/client/*` → `ROLE_CLIENT`
- The home route `/` redirects based on the authenticated user's role.

### Key Modules
- **Admin module** (`src/Controller/Admin/`): CRUD for clients and cryptocurrencies, admin dashboard.
- **Client module** (`src/Controller/Client/`): Dashboard, wallet, buy/sell transactions, transaction history, profile, crypto browser.
- **Security** (`src/Controller/SecurityController.php`): Login, logout, registration.
- **API** (`src/Controller/Api/`): Minimal, partially implemented.

### Service Layer (`src/Service/`)
Business logic lives in six services — do not put it in controllers or entities:
- `CalculationService` — portfolio value, P&L, net worth calculations.
- `TransactionService` — buy/sell logic with validation.
- `WalletService` — wallet and holding management.
- `CryptocurrencyService` — crypto data and price history.
- `QuoteGeneratorService` — generates price snapshots.
- `PasswordGeneratorService` — utility for generating passwords.

### Database Schema
Core entities and their relationships:
- `Client` → has one `Wallet`
- `Wallet` → has many `Holdings`, has many `Transactions`
- `Holding` → links `Wallet` + `Cryptocurrency` with `quantity` and `average_purchase_price`
- `Transaction` → buy/sell record with `price_at_transaction` and `total_amount`
- `Quote` → historical price snapshot for a `Cryptocurrency` at a point in time

All monetary/crypto values use `DECIMAL` columns (bcmath-safe precision).

### Frontend Stack
No Node.js build step. The project uses **Symfony Asset Mapper** with **Stimulus.js** (controllers in `assets/controllers/`) and **Turbo** for SPA-like navigation. CSS and JS are vendored directly under `assets/vendor/`. To add a JS package, use `importmap:require`.

### Forms & DTOs
Forms are in `src/Form/`. DTOs in `src/DTO/` (`ClientDTO`, `TransactionDTO`, `WalletDTO`) are used to transfer data between layers rather than passing entities directly to forms.

### Fixtures
Test data is loaded via Doctrine Fixtures (`src/DataFixtures/`):
- `UserFixtures.php` — creates admin and client users
- `CryptocurrencyFixtures.php` — seeds crypto assets
- `AppFixtures.php` — orchestrates fixture loading order

### Environment
- `.env` — base config (MySQL connection, app secret, messenger transport)
- `.env.dev` — development overrides
- `.env.test` — test environment (separate DB)
- Database: MySQL 8.0.32 at `127.0.0.1:3306/bitchest`
- Docker Compose (`compose.yaml`) provides PostgreSQL 16 and Mailpit for local email testing.

### Async Messaging
The Symfony Messenger component is configured with a `doctrine://default` transport. Async messages are stored in the `messenger_messages` table.
