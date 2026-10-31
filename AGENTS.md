<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.3. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>

=== project implementation conventions ===

# Food Order App Conventions

These are the repository's implementation rules for extending the existing food-ordering API. Match these conventions before introducing a new abstraction. The current codebase is intentionally small and uses direct Laravel primitives; do not add a repository, service, action, DTO, module, or dependency layer unless the feature creates a real boundary that cannot be handled by the existing pattern.

## Required pre-work

- Read the relevant sibling files before creating or modifying a file. Preserve the existing namespace, directory, method naming, response shape, translation style, and authorization boundary.
- Confirm installed package APIs from `composer.json`/`composer.lock` before relying on version-sensitive behavior. The application uses PHP 8.3, Laravel 13, JWT Auth, Spatie Laravel Settings, Scramble, Pest, and Pint.
- Use `php artisan make:* --no-interaction` for generated Laravel files. Use the correct generator instead of manually creating a migration, rule, request, model, controller, job, or test.
- Do not change dependencies or create new top-level directories without explicit approval.
- Do not expose `.env`, JWT secrets, database credentials, seeded credentials, or other sensitive values in source, logs, API responses, or documentation.

## Application structure

Use the existing default Laravel layout:

- `app/Enums/Auth/` contains backed domain enums such as `UserRole`.
- `app/Exceptions/` contains application exceptions rendered centrally from `bootstrap/app.php`.
- `app/Http/Controllers/Api/` contains plain multi-method API controllers.
- `app/Http/Middleware/` contains API middleware, including role and locale middleware.
- `app/Http/Requests/` contains Form Requests grouped by feature (`Auth`, `Customer/Address`, `Restaurant`).
- `app/Http/Resources/` contains JSON Resources for stable API representations.
- `app/Models/` contains Eloquent models and relationships.
- `app/Rules/` contains reusable validation rule objects.
- `app/Settings/` contains Spatie settings classes.
- `app/Traits/Normalizations/` contains reusable input-normalization traits.
- `app/Scramble/` contains API documentation extensions.
- `database/migrations/` contains schema migrations; `database/settings/` contains Spatie settings migrations.
- `routes/api.php` is the API route source; `routes/web.php` and `routes/console.php` remain minimal unless a feature needs them.

There is currently no Actions, Services, Repositories, Queries, DTO, Domain, or module layer. Keep business logic in the controller when it is a small endpoint-specific operation, use Eloquent relationships for persistence, and extract a class only when the logic is reusable or independently testable.

## API authentication and roles

- API authentication uses the `api` JWT guard from `config/auth.php`; use `auth('api')` or `Auth::guard('api')` consistently with the surrounding file.
- JWT controllers return `access_token`, `token_type`, `expires_in`, and `user` through the controller's token response helper.
- Protect authenticated routes with `auth:api`.
- Use the `role` middleware alias registered in `bootstrap/app.php`. It accepts pipe-separated role values, for example `role:customer|restaurant_manager`.
- Keep role values in `App\Enums\Auth\UserRole`; use the enum value (`UserRole::CUSTOMER->value`) in routes, persistence, and claims rather than duplicating string literals.
- Preserve the current role strings (`admin`, `customer`, `restaurant_manager`) and route naming unless a dedicated compatibility change is requested. Do not copy stale `vendor` terminology from older documentation.
- Keep administrator-only, restaurant-manager-only, customer-only, and shared authenticated route groups visibly separated in `routes/api.php`.
- For ownership checks, query through the authenticated user's relationship (`$user->addresses()` or `$user->restaurants()`) so another user cannot access the record by ID. Use `findOrFail()` so missing or unauthorized-owned records become the normal 404 response.

## Controllers and API responses

- Use plain controller methods with explicit request parameters and return types where practical; follow the existing feature controller shape instead of converting the application to resource controllers or invokable controllers.
- Use Form Requests for request validation. Controllers should consume `$request->validated()` and should not duplicate validation rules.
- Use direct Eloquent queries and relationships. Prefer relationship creation (`$user->addresses()->create(...)`) when ownership is part of the write.
- Use `DB::transaction()` for operations that update multiple related records or must maintain an invariant, such as changing the default customer address.
- Throw `App\Exceptions\BusinessException` for expected domain-limit failures. Pass a translation key and HTTP status; `bootstrap/app.php` converts it into the standard JSON error shape.
- Return JSON with the established envelope:
  - collections and simple reads use `['data' => ...]`;
  - successful writes include a translated `message` and `data`;
  - deletes include a translated `message`;
  - errors use `message` and `errors`.
- Use `App\Http\Resources\*Resource` for a stable or public representation, especially restaurant endpoints. Do not expose a model directly when the feature needs a controlled field list, computed URL, or numeric/date formatting.
- Keep success messages and error messages in `resources/lang/en/*.php` and `resources/lang/ar/*.php`. Use dotted translation keys such as `restaurant.created_successfully`; do not hard-code user-facing API text.
- New API endpoints must be documented by their request/response types in the existing Scramble-compatible style and must preserve the global `Accept-Language`/`Accept` header behavior.

## Validation and input preparation

- Create a Form Request with `php artisan make:request ... --no-interaction` for endpoint input.
- Keep `authorize(): bool` explicit. Use policies or middleware when authorization is needed; do not hide authorization in validation rules.
- Put request-only normalization in `prepareForValidation()`, then validate the normalized value. Use `has()` before merging optional fields.
- Reuse normalization traits from `app/Traits/Normalizations/`:
  - `NormalizesPhoneNumber` converts Egyptian phone input to the canonical `+20...` form.
  - `NormalizesTime` strips invalid characters and pads one-digit hours to `HH:MM`.
- If the same normalization or transformation appears in more than one request, extend an existing normalization trait or create a focused trait under `app/Traits/Normalizations/`; do not copy the method into multiple requests.
- For reusable validation logic, create a rule object with `php artisan make:rule RuleName --no-interaction` under `app/Rules/`. Use `ValidPhoneNumber` for the established Egyptian phone format and attach the rule in the request's rule array.
- Do not use inline validation closures for logic that is reused or domain-specific. Do not put reusable validation in a service provider.
- Use array-form validation rules. Keep create and update rules separate when update semantics require `sometimes` or `Rule::unique(...)->ignore($id)`.
- For unique update rules, obtain the route ID using the route parameter already used by the endpoint and ignore only that record.
- Normalize booleans, slugs, phone numbers, and times before validation. Validate file uploads with the existing image/mime/size pattern and store them on the `public` disk.
- Add or update the matching English and Arabic validation/custom-message translations whenever a rule introduces a user-facing message.

## Normalization and DRY rules

- Before adding code, search for the behavior across `app/`, `database/`, and `routes/`. Repeated behavior belongs in the narrowest reusable abstraction.
- Normalization belongs in `app/Traits/Normalizations/`, reusable validation belongs in `app/Rules/`, and cross-request request preparation belongs in the Form Request only when it is not a reusable normalization concern.
- Keep traits focused on one concern and use descriptive protected methods. Do not create a broad catch-all helper trait.
- Do not duplicate phone format checks, phone normalization, time normalization, slug generation, file replacement, ownership queries, or response formatting when an existing helper/pattern covers the same behavior.
- Prefer framework primitives over new utility packages: Eloquent relationships, `DB::transaction`, `Storage`, `Rule`, `JsonResource`, translation helpers, and settings injection.

## Models and Eloquent

- Use Eloquent models with typed relationship methods (`BelongsTo`, `HasMany`, and the appropriate relation type).
- Use Laravel 13 model attributes for mass-assignment and hidden fields, matching the existing `#[Fillable(...)]` and `#[Hidden(...)]` style.
- New model casts should use the `protected function casts(): array` convention. Include booleans, decimal precision, integers, hashed passwords, and date/time formats explicitly. Do not reproduce an existing singular `cast()` typo.
- Use `$model->relation()` for ownership-scoped reads and writes. Avoid raw table queries unless Eloquent cannot express the operation.
- Add relationships on both sides when a feature introduces a new foreign key.
- Use database constraints (`foreignId()->constrained()`, appropriate cascade behavior, unique indexes, and numeric precision) as the final integrity boundary; validation is not a replacement for schema constraints.
- Avoid hidden N+1 queries. Eager-load relationships when serializing collections or when a resource accesses relationships.
- Keep calculated fields and public output formatting in the resource/model boundary, not in Blade or route closures.
- Preserve automatic integer primary keys and string role storage unless a feature explicitly requires a different key strategy.

## Database and settings

- Create migrations with anonymous migration classes and implement a complete reversible `down()` method.
- Use the existing timestamped migration naming and make each migration narrowly scoped to one schema change.
- Prefer `foreignId(...)->constrained(...)->cascadeOnDelete()` for ownership relationships. Add indexes/unique constraints when the lookup or business invariant requires them.
- Use `Schema::create` for new tables and `Schema::table` for later changes; never edit an already-applied migration to change production schema.
- Use `database/settings/` and `Spatie\LaravelSettings\Migrations\SettingsMigration` for application-wide business settings. Add corresponding typed properties to an `app/Settings/*` class and register the class in `config/settings.php`.
- Inject settings classes into controllers/services rather than reading raw settings-table values or hard-coding limits.
- Use transactions around multi-write invariants, and keep migrations free of runtime application logic.
- Keep generated SQLite/database artifacts out of commits according to the existing `database/.gitignore`.

## File uploads

- Validate images as request input with the established `image`, allowed MIME extensions, and size limit rules.
- Store uploads under a feature-specific `public` disk path such as `restaurants/logos`.
- Save only the relative storage path in the model. Expose a public URL through the resource.
- When replacing or deleting a file, delete the old path through the same disk before/with the record change. Do not trust a client-provided path.

## Routing

- Put API endpoints in `routes/api.php`, grouped by prefix, authentication, and role.
- Use controller class handlers rather than route closures for feature behavior.
- Follow the existing explicit verb route style and string `{id}` parameters unless implicit model binding is introduced consistently for the whole feature.
- Register new middleware aliases in `bootstrap/app.php` and apply them to the smallest route group that needs them.
- Keep locale handling on the API middleware stack. User-facing responses must remain translatable in English and Arabic.

## Localization and errors

- Add keys to both `resources/lang/en/` and `resources/lang/ar/` for every new message, validation message, and domain error.
- Use domain-specific translation files (`auth.php`, `restaurant.php`, `customer_address.php`, etc.) rather than putting feature text in an unrelated file.
- Throw translation-key-based `BusinessException` instances; do not throw translated text from domain code.
- Preserve the central JSON exception rendering in `bootstrap/app.php` for authentication, JWT, business, and validation failures.

## Tests and verification

- This repository uses Pest. Add focused feature tests for endpoint behavior and unit tests only for isolated logic.
- Use factories for model setup when available; expand a factory state instead of repeating raw model construction.
- Test authorization boundaries, ownership scoping, validation/normalization, translated messages, resource shape, file storage behavior, settings limits, and transaction invariants for new modules.
- Prefer `RefreshDatabase` or the database strategy already used by the surrounding tests. Do not delete existing tests.
- Run the narrowest relevant test file/filter first, then run `vendor/bin/pint --dirty --format agent` for modified PHP files. Run broader tests only when the change affects shared authentication, middleware, models, migrations, or route behavior.

## Naming and style

- Use explicit type declarations, return types, curly braces for control structures, and descriptive names.
- Keep enum names and case values consistent with `UserRole`; do not introduce a second role representation.
- Match existing feature folder names and controller/request/resource naming.
- Prefer named routes and `route()` when generating application URLs; use `asset('storage/...')` only for the established public-upload resource URL pattern.
- Add comments only for non-obvious business rules. Prefer PHPDoc when a type or invariant needs explanation.

## Existing implementation notes

- The current modules are authentication, customer addresses, and restaurants. Future modules should follow the same route-group, Form Request, controller, model, resource, migration, settings, translation, and test boundaries.
- `RestaurantController` is the reference for manager-owned CRUD, public active listing, admin activation/deactivation, public-disk logo handling, and `RestaurantResource` responses.
- `CustomerAddressController` is the reference for user-owned CRUD, settings-based limits, default-record invariants, and transaction usage.
- `AuthController` is the reference for JWT registration/login/refresh/logout/me behavior.
- Treat stale README examples and comments as documentation debt when they conflict with the executable routes, enums, migrations, or configuration. Preserve executable behavior unless a compatibility change is explicitly requested.
