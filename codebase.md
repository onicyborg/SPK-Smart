# Native PHP Semi-Framework Architecture

## Rule #1

**NO COMPOSER. NO THIRD-PARTY PACKAGES.** Everything in this project is pure native PHP (8.1+). No `composer.json` file exists or should ever be created.

---

## Directory Structure

```
/
├── app/
│   ├── Controllers/          # Application controllers (App\Controllers namespace)
│   └── Models/               # Application models (App\Models namespace)
├── core/
│   ├── Autoload.php          # Custom PSR-4-style autoloader
│   ├── Controller.php        # Base controller with view rendering
│   ├── Database.php          # PDO connection singleton
│   ├── Env.php               # Native .env file parser
│   ├── Model.php             # Base model with chainable query builder
│   ├── Router.php            # HTTP request router
│   └── Seeder.php            # Seeder interface (enforces run() method)
├── database/
│   ├── migrations/           # Migration files (YYYYMMDD_HHMMSS_description.php)
│   └── seeders/              # Database seeder classes
├── public/
│   ├── assets/               # Static assets (CSS, JS, images)
│   └── index.php             # Application entry point
├── views/                    # PHP view templates
├── .env                      # Environment variables (gitignored)
├── .env.example              # Environment variable template
├── codebase.md               # This blueprint document
├── make_migration.php         # Migration file generator
├── migrate.php               # CLI migration runner
└── seed.php                  # CLI seeder runner
```

---

## Core Component Specs

### `core/Autoload.php`

Custom autoloader using `spl_autoload_register()`. Simulates PSR-4 namespace-to-directory mapping:

| Namespace | Directory |
|-----------|-----------|
| `App\`    | `/app`    |
| `Core\`   | `/core`   |

- `Autoload::addNamespace(string $prefix, string $baseDir)` — Registers a namespace-to-directory mapping.
- `Autoload::register()` — Registers the autoloader with SPL.
- On include, the file automatically registers `App\` and `Core\` namespaces and activates the autoloader.

### `core/Router.php`

Simple HTTP router that maps URL paths to controller methods.

**Methods:**
- `get(string $path, array $handler)` — Register a GET route.
- `post(string $path, array $handler)` — Register a POST route.
- `put(string $path, array $handler)` — Register a PUT route.
- `patch(string $path, array $handler)` — Register a PATCH route.
- `delete(string $path, array $handler)` — Register a DELETE route.
- `dispatch()` — Match the current request URI and method against registered routes, instantiate the controller, and call the handler method.

**Handler format:** `[ControllerClass::class, 'methodName']`

**Named parameters:** Use `{paramName}` in the path (e.g., `/users/{id}`). Parameters are extracted and passed as arguments to the controller method.

**404 handling:** Returns a plain "404 - Page Not Found" response if no route matches.

### `core/Controller.php`

Base controller class. All application controllers should extend this.

**Methods:**
- `render(string $view, array $data = [])` — Loads a PHP file from `/views/{view}.php`, extracts the `$data` array into variables, and renders it. Returns a 500 error if the view file is not found.

### `core/Database.php`

Singleton PDO connection manager with dual-database support.

**Methods:**
- `connection(): PDO` — Returns the singleton PDO instance. Creates it on first call using credentials from `$_ENV` (see .ENV Spec below).
- `reset(): void` — Destroys the singleton instance, forcing a fresh connection on the next `connection()` call.

**Credentials sourced from `$_ENV`:**
- `DB_CONNECTION` (default: `mysql`) — Driver selector. Supported values: `mysql`, `pgsql`.
- `DB_HOST` (default: `127.0.0.1`)
- `DB_PORT` (default: `3306`)
- `DB_NAME` (default: `test`)
- `DB_USER` (default: `root`)
- `DB_PASS` (default: empty string)

**Dual-Database DSN Logic:**

The `connection()` method reads `DB_CONNECTION` and uses a `match` expression to build the correct PDO DSN:

| Driver  | DSN Format |
|---------|------------|
| `mysql` | `mysql:host={host};port={port};dbname={dbname};charset=utf8mb4` |
| `pgsql` | `pgsql:host={host};port={port};dbname={dbname};options='--client_encoding=UTF8'` |

If an unsupported value is provided, a `RuntimeException` is thrown.

### `core/Model.php`

Base model class with a chainable query builder. All application models should extend this.

**Properties:**
- `$table` — The database table name (set in child class).
- `$primaryKey` — Primary key column name (default: `id`).

**Chainable Query Builder Methods:**

| Method | Description |
|--------|-------------|
| `table(string $table)` | Set the table to query. |
| `select(string\|array $columns)` | Set columns to select (default: `*`). |
| `where(string $column, string $operator, mixed $value)` | Add a WHERE condition. Multiple calls chain with AND. |
| `orderBy(string $column, string $direction = 'ASC')` | Add ORDER BY clause. |
| `limit(int $limit)` | Add LIMIT clause. |
| `offset(int $offset)` | Add OFFSET clause. |

**Execution Methods:**

| Method | Returns | Description |
|--------|---------|-------------|
| `get()` | `array` | Execute SELECT and return all matching rows. |
| `first()` | `?array` | Execute SELECT with LIMIT 1, return single row or null. |
| `insert(array $data)` | `int\|string` | Insert a row. Returns last insert ID. |
| `update(array $data)` | `bool` | Update matching rows. Requires prior `where()` calls. |
| `delete()` | `bool` | Delete matching rows. Requires prior `where()` calls. |

**Security:** All queries use PDO prepared statements with named parameter binding. Query state resets after each execution.

### `core/Env.php`

Native `.env` file parser. No third-party dependencies.

**Methods:**
- `Env::load(string $path)` — Reads the file at `$path`, parses each line, and loads variables into both `$_ENV` and `putenv()`.

**Parsing rules:**
- Ignores empty lines.
- Ignores lines starting with `#` (comments).
- Splits on the first `=` character into key and value.
- Trims whitespace from both key and value.
- Strips surrounding single or double quotes from the value.
- Sets `$_ENV[$key] = $value` and `putenv("$key=$value")`.

### `core/Seeder.php`

Abstract base class that all seeder classes must extend.

**Properties:**
- `$priority` (int, default: `100`) — Controls execution order. Lower values run first.

**Methods:**
- `run(): void` — Abstract. Executes the seeder logic (inserts, updates, etc.).
- `getPriority(): int` — Returns the seeder's priority value.

---

## Seeder Spec

### CLI Runner: `seed.php`

Run from the project root:
```bash
# Run all seeders (sorted by priority)
php seed.php

# Run a single seeder
php seed.php UserSeeder
```

**Behavior:**
1. Loads `.env` and connects to the database.
2. Scans `/database/seeders/` for `.php` files.
3. If a seeder name argument is provided, only that seeder runs.
4. For each file, resolves the class name as `Database\Seeders\{Filename}`.
5. Instantiates the class and verifies it extends `Core\Seeder`.
6. Sorts seeders by `$priority` (ascending).
7. Calls `$seeder->run()` inside a try/catch. Stops on first error.

### Creating a New Seeder

1. Create a file in `database/seeders/` (e.g., `SupplierSeeder.php`).
2. Use namespace `Database\Seeders`.
3. Extend `Core\Seeder` and define the `run()` method.
4. Optionally override `$priority` to control execution order (lower = earlier).
5. Use `Core\Database::connection()` to get the PDO instance.
6. Run `php seed.php`.

**Example:**
```php
<?php

namespace Database\Seeders;

use Core\Database;
use Core\Seeder;

class SupplierSeeder extends Seeder
{
    protected int $priority = 30;

    public function run(): void
    {
        $db = Database::connection();
        $db->exec("INSERT INTO suppliers (...) VALUES (...)");
        echo "  SupplierSeeder: Data inserted.\n";
    }
}
```

---

## Routing Spec

Routes are defined in `public/index.php` before calling `$router->dispatch()`.

**Example:**
```php
$router->get('/', [HomeController::class, 'index']);
$router->get('/users', [UserController::class, 'index']);
$router->get('/users/{id}', [UserController::class, 'show']);
$router->post('/users', [UserController::class, 'store']);
```

The router matches the HTTP method and URI path. Named parameters like `{id}` are captured via regex and passed as arguments to the controller method.

---

## Migration Spec

### CLI Runner: `migrate.php`

Run from the project root:
```bash
php migrate.php
```

**Behavior:**
1. Connects to the database using credentials from `.env`.
2. Creates a `migrations` tracking table if it doesn't exist, using DB-aware SQL:
   - **MySQL:** `id INT AUTO_INCREMENT PRIMARY KEY`
   - **PostgreSQL:** `id SERIAL PRIMARY KEY`
   - `migration_name` — Unique filename of the migration.
   - `executed_at` — Timestamp of execution.
3. Scans `/database/migrations/` for `.php` files, sorted alphabetically.
4. Compares against the `migrations` table to find unexecuted migrations.
5. For each new migration: starts a transaction, executes the SQL, logs the migration name, and commits. Rolls back on failure.

> **CRITICAL WARNING:** Because migrations use raw SQL, developers MUST ensure the SQL dialect (MySQL vs PostgreSQL) matches the current `DB_CONNECTION`. For example, primary keys use `AUTO_INCREMENT` in MySQL but `SERIAL` in PostgreSQL. MySQL-specific clauses like `ENGINE=InnoDB` and `CHARSET=utf8mb4` are not valid in PostgreSQL and will cause migration failures.

### Migration Generator: `make_migration.php`

Scaffold a new migration file from the command line:

```bash
php make_migration.php <migration_name>
```

**Example:**
```bash
php make_migration.php create_products_table
```

**Behavior:**
1. Accepts a single argument: the migration name (e.g., `create_users_table`).
2. Sanitizes the name (lowercase, underscores only).
3. Generates a timestamped filename: `YYYYMMDD_HHMMSS_{name}.php`.
4. Creates the file in `/database/migrations/` with a boilerplate template containing the SQL dialect warning comment and a placeholder `return ""` statement.

### Migration File Format

Files in `/database/migrations/` must follow this naming convention:
```
YYYYMMDD_HHMMSS_descriptive_name.php
```

Each file must `return` a raw SQL string:
```php
<?php

return "
    CREATE TABLE IF NOT EXISTS example (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
```

---

## .ENV Spec

Environment variables are loaded from the `.env` file at the project root.

### Loading

- **Web requests:** `public/index.php` calls `Env::load(__DIR__ . '/../.env')` before routing.
- **CLI (migrations):** `migrate.php` calls `Env::load(__DIR__ . '/.env')` before connecting to the database.

### File Format

```
# Comment lines start with #
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=my_database
DB_USER=root
DB_PASS=
```

**`DB_CONNECTION` values:** `mysql` or `pgsql`.

### Files

- `.env` — Actual environment values. **Must be gitignored.**
- `.env.example` — Template with blank/dummy values. Committed to version control.

---

## MAINTENANCE RULE

**Whenever a new core feature is added, a folder structure is changed, or core logic is modified, this `codebase.md` file MUST be updated simultaneously to reflect the latest state.**
