# SPK Pemilihan Supplier Ban (Native PHP Framework)

A web-based Decision Support System utilizing the **SMART** (Simple Multi Attribute Rating Technique) method for selecting airport tire suppliers. Built on a custom lightweight PHP MVC framework with **zero external dependencies** — no Composer, no third-party packages.

---

## Prerequisites

- **PHP 8.1** or higher
- **PDO extension** enabled (`php-pdo`)
- **MySQL** (recommended) or **PostgreSQL**
- Database driver extension (`php-mysql` or `php-pgsql`)

---

## Initialization & Setup

### Step 1: Clone the Repository

```bash
git clone <repository-url> spk-smart
cd spk-smart
```

### Step 2: Configure Environment

Copy the example environment file and edit your database credentials:

```bash
cp .env.example .env
```

Edit `.env` with your database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=spk_smart
DB_USER=root
DB_PASS=root
```

- Set `DB_CONNECTION` to `mysql` or `pgsql` depending on your database.
- Create the database (`spk_smart` in the example above) before running migrations.

### Step 3: Run Migrations

```bash
php migrate.php
```

This creates all required tables: `users`, `criteria`, `suppliers`, `evaluation_sessions`, `session_criteria`, `session_suppliers`, and `session_scores`.

### Step 4: Start Development Server

```bash
php -S localhost:8080 -t public/
```

Open `http://localhost:8080` in your browser.

---

## Available CLI Commands

| Command | Description |
|---------|-------------|
| `php migrate.php` | Execute all pending database migrations in a transaction. Tracks executed migrations to avoid duplicates. |
| `php make_migration.php <name>` | Generate a new timestamped migration file boilerplate in `database/migrations/`. Example: `php make_migration.php create_products_table` |

---

## Architecture Reference

This project is built on a custom native PHP MVC semi-framework. For deep technical specifications covering the core engine — including the custom PSR-4 Autoloader, Router, Query Builder, dual-database PDO layer, .env parser, and migration system — refer to the blueprint document:

**[`codebase.md`](codebase.md)**

---

## Project Structure

```
/
├── app/
│   ├── Controllers/          # Application controllers
│   └── Models/               # Application models
├── core/                     # Framework engine (Router, DB, ORM, etc.)
├── database/migrations/      # Database migration files
├── public/                   # Web root (entry point, assets)
├── views/                    # PHP view templates
├── .env                      # Environment configuration
├── .env.example              # Environment template
├── codebase.md               # Technical blueprint
├── make_migration.php        # Migration generator
├── migrate.php               # Migration runner
└── README.md
```

---

## License

This project is for internal/academic use.
