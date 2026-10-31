# FoodHub API Implementation Plan

## 1. Purpose and implementation contract

This document converts `PRD.md` into an executable implementation sequence for the
current Laravel application. It is a planning document only; no application
feature code is included in this phase.

The implementation must:

- Follow `AGENTS.md` and the existing repository structure.
- Preserve the current JWT authentication guard and API response conventions.
- Add functionality as small, reviewable vertical slices rather than creating all
  database layers first and wiring them together later.
- Use Laravel 13/PHP 8.3 APIs and the package versions already present in
  `composer.json`.
- Keep customer, restaurant-manager, and administrator authorization boundaries
  explicit in routes and policies.
- Keep all user-facing messages localized in English and Arabic.
- Avoid dependencies, top-level directories, repositories, services, DTOs, or
  actions unless the feature creates a real boundary that justifies them.

## 2. Current baseline

The repository currently contains:

- Laravel 13 and PHP 8.3.
- JWT authentication through `auth:api` and
  `php-open-source-saver/jwt-auth`.
- `UserRole` values for `admin`, `customer`, and
  `restaurant_manager`.
- Role middleware registered as `role`.
- Auth, customer-address, and initial restaurant controllers, requests,
  resources, models, migrations, and translations.
- Spatie settings support for business settings.
- Scramble API documentation support.
- Pint.
- SQLite as the local default database.

The following PRD areas are not yet implemented:

- Menu categories and menu items.
- Public menu browsing/search.
- Cart and cart-item lifecycle.
- Coupons and coupon usage rules.
- Checkout and order creation.
- Order status history/state transitions.
- Stock reservation/decrement/restoration.
- Idempotent checkout.
- Events and queued listeners.
- Reviews and rating recalculation.
- Manager order processing and statistics.
- Admin restaurant/coupon/order/user management.
- Restaurant/menu caching and invalidation.
- Critical-flow seed data and endpoint verification.
- Complete OpenAPI examples/export and final README walkthrough.

## 3. Decisions and PRD compatibility notes

### 3.1 Authentication

Use the existing JWT implementation, not Sanctum. The PRD recommends Sanctum,
but changing the established `auth:api` guard, token response, middleware, and
existing endpoints would be an unrelated compatibility migration. JWT already
supports the required mobile bearer-token workflow, refresh, and logout.

### 3.2 Role naming

Use `restaurant_manager` everywhere in executable code. The PRD alternates
between `manager` and the older `vendor` wording, while the repository already
uses `restaurant_manager` in the enum, routes, middleware, and persistence.
README wording should be corrected only when the relevant feature/documentation
slice is implemented.

### 3.3 API versioning

Do not move existing routes into `/api/v1` as part of the first implementation
pass. The application currently exposes `/api` routes and Scramble is already
configured around them. Introduce a version prefix only as a separately approved
compatibility change. New endpoints should follow the existing route style unless
the route migration is deliberately scheduled.

### 3.4 Money representation

The PRD requires integer minor units, while the existing restaurant migration
stores monetary values as decimals. Before implementing ordering, choose one
consistent representation for new order/cart/coupon calculations. Preferred
direction: migrate all newly affected monetary fields and calculations to integer
minor units, then decide whether existing restaurant monetary columns are
compatibly converted. This decision must be finalized before the pricing and
checkout slices because mixed units would be unsafe.

### 3.5 Architecture boundary

Start with the existing direct Laravel style:

- Controllers coordinate requests, authorization, and responses.
- Form Requests own request validation and normalization.
- Eloquent models own relationships and casts.
- API Resources own stable response shapes.
- Policies own record-level authorization.

Extract a focused action/service only for logic that is reused or independently
maintainable, especially price calculation, coupon validation, state transitions,
checkout, and stock handling. Do not create the entire PRD Domain/Application/
Infrastructure tree speculatively.

### 3.6 Soft deletes

The PRD requests soft deletes for users, restaurants, and menu items. Existing
models/migrations must be audited before enabling this because adding the trait
without a `deleted_at` column is invalid. Add soft deletes through dedicated
migrations and update authorization/query behavior deliberately; do not edit
already-applied migrations.

## 4. Implementation order

The work is organized into vertical modules. Each module is complete only when
its schema, model relationships, requests, authorization, endpoints, resources,
translations, and documentation impact are addressed.

### Module 0 — Baseline and compatibility hardening

**I will add the foundation first with the minimum changes needed to make the
existing application a reliable base for the remaining modules.**

Scope:

1. Audit existing auth, roles, routes, exception rendering, locale middleware,
   restaurant/address behavior, settings, and API response shapes.
2. Resolve terminology consistently around `restaurant_manager`.
3. Correct tightly coupled existing defects discovered during implementation,
   such as model cast method naming or missing relationship typing, without
   broad unrelated refactoring.
4. Decide and document the monetary-unit representation before order tables are
   created.
5. Add shared enums only where multiple modules need the same domain value:
   order status, payment method/status, coupon type, and any active-state
   representation.
6. Establish shared validation/normalization patterns for IDs, quantities,
   money, status values, and idempotency keys.

Expected files/areas:

- `app/Enums/`
- `app/Models/User.php` and existing related models
- `routes/api.php`
- `bootstrap/app.php` only if shared error behavior requires a targeted change
- `resources/lang/en/` and `resources/lang/ar/`
- focused baseline endpoint verification

Exit criteria:

- Existing auth, address, and restaurant behavior remains compatible.
- All later modules have an agreed role, money, status, and response contract.

### Module 1 — Restaurant browsing and menu management

Build the public browsing slice and the manager-owned menu slice before carts
because carts and checkout depend on stable restaurant/menu records.

Schema:

- `menu_categories`: restaurant foreign key, name, sort order, timestamps.
- `menu_items`: restaurant/category foreign keys, name, description, price,
  image path, availability, nullable stock quantity, timestamps, and any required
  soft-delete column.
- Add foreign keys, indexes, and delete behavior that preserve category and
  restaurant ownership.

Models and relationships:

- `MenuCategory` belongs to `Restaurant` and has many `MenuItem`.
- `MenuItem` belongs to `Restaurant` and `MenuCategory`.
- Add inverse relationships on `Restaurant`.
- Use typed casts and fillable attributes consistent with current Laravel 13
  model conventions.

HTTP/API:

- Public restaurant list filters: search, cuisine, open-now, stable sort, and
  pagination.
- Public restaurant details with categories and available menu items.
- Public menu-item search within a restaurant.
- Manager category CRUD scoped to the authenticated manager's restaurant.
- Manager menu-item CRUD, availability toggle, and stock update.
- Admin restaurant listing/status controls remain isolated from manager routes.

Validation and authorization:

- Form Requests for category/menu-item create and update.
- Policies or relationship-scoped queries must prevent a manager from touching
  another manager's records.
- Validate category ownership and restaurant/category consistency.
- Validate quantity-independent menu item stock values and money values.
- Validate image uploads using the established public-disk pattern if menu item
  images are included in the first slice.

Resources and translations:

- Add controlled `RestaurantResource`, category, and menu-item resources or
  extend the existing resource without exposing internal fields.
- Add English and Arabic success, validation, and domain messages.
- Preserve global locale and JSON behavior.

Caching:

- Introduce restaurant list/detail/menu caching only after query behavior is
  correct.
- Use cache keys based on normalized filters and restaurant identity.
- Use cache tags only when the configured driver supports them; otherwise use
  versioned keys or explicit key invalidation.
- Invalidate public restaurant/menu data after manager/admin writes.

Verification:

- Verify public filtering, sorting, pagination, open/active visibility, and menu
  search through the API.
- Verify manager ownership isolation for categories/items.
- Verify availability, stock update validation, resource shape, and localized
  error behavior.
- Verify cache invalidation using the configured cache driver.

Exit criteria:

- A customer can browse an active restaurant and its available menu.
- A manager can manage only their own categories/items.
- The menu contract is stable enough for cart creation.

### Module 2 — Customer address completion

Review the existing address module against checkout requirements and complete
only the missing behavior:

- Ensure list/create/update/delete/default operations are ownership-scoped.
- Ensure the default-address invariant is transaction-safe.
- Confirm settings-based address limits and translated business errors.
- Ensure checkout can request an address ID and obtain a stable snapshot.
- Verify ownership, default replacement, limit enforcement, and
  validation/normalization through the API.

Exit criteria:

- Checkout can safely select an address belonging to the authenticated customer.

### Module 3 — Cart and pricing preview

Schema:

- `carts`: unique user foreign key, restaurant foreign key, nullable coupon
  foreign key, timestamps.
- `cart_items`: cart/menu-item foreign keys, quantity, notes, timestamps,
  unique cart/menu-item constraint.

Models and boundaries:

- `Cart` belongs to user, restaurant, and optional coupon; has many items.
- `CartItem` belongs to cart and menu item.
- Add user cart relationship.
- Keep one active cart per customer enforced by a unique database constraint.

Operations:

- Get current cart with items and computed preview totals.
- Add item with quantity and notes.
- Update quantity.
- Remove item.
- Clear cart.
- Apply coupon code.
- Remove coupon.

Business rules:

- A cart may contain items from one restaurant only.
- Adding a different restaurant item returns a clear translated error unless a
  separately approved `force` behavior is implemented.
- Item must belong to the selected restaurant, be available, and satisfy the
  quantity range of 1–20.
- Stock is checked for preview and again under lock at checkout.
- Cart subtotal and discount preview are server-calculated.

Implementation boundary:

- Extract a focused cart operation/action only if it prevents duplicated rules
  across endpoints.
- Extract `PriceCalculator` before checkout if it can be made deterministic and
  independently reviewed with integer amounts.
- Coupon validation must be reusable by cart preview and checkout, but checkout
  remains the final authority.

Verification:

- Verify the single-restaurant rule, quantity boundaries, unavailable item
  rejection, ownership isolation, coupon apply/remove, and recalculation after
  quantity/item changes.

Exit criteria:

- A customer can build one restaurant cart and see correct server-side totals.

### Module 4 — Coupons

Schema:

- `coupons`: unique code, type, value, optional maximum discount, minimum order,
  start/end timestamps, usage limit, per-user limit, active flag, timestamps.
- `coupon_usages`: coupon/user/order foreign keys and uniqueness constraints
  needed to enforce usage rules.

Domain behavior:

- Support percentage and fixed coupons.
- Enforce active period, minimum subtotal, global usage limit, per-user limit,
  and percentage maximum discount.
- Normalize codes consistently, preferably case-insensitive while preserving a
  canonical stored form.
- Prevent coupon usage records from being created before a successful order.

Admin API:

- List/create/update/delete coupons.
- Validate date ordering, values, caps, usage limits, and code uniqueness.
- Keep admin authorization separate from customer cart application.

Verification:

- Verify valid percentage/fixed discounts and expired, inactive, not-started,
  minimum-order, global-limit, per-user-limit, and max-cap failures.
- Verify admin-only management and customer ownership of usage checks.
- Verify concurrency-sensitive usage behavior during checkout.

Exit criteria:

- Coupon calculations are deterministic and reusable by cart and checkout.

### Module 5 — Orders, checkout, inventory, and idempotency

This is the highest-risk module and must be implemented as one transaction-safe
vertical slice.

Schema:

- `orders`: unique order number, customer/restaurant foreign keys, address JSON
  snapshot, status, payment method/status, subtotal, discount, delivery fee,
  tax, total, optional coupon, notes, rejection reason, user-scoped
  idempotency key, placed timestamp, timestamps.
- `order_items`: order/menu-item foreign keys, name snapshot, unit price,
  quantity, line total, notes, timestamps.
- `order_status_histories`: order foreign key, nullable from status, to status,
  actor user foreign key, note, timestamps.
- Add indexes from the PRD:
  `orders(user_id, status)`,
  `orders(restaurant_id, status, created_at)`, and appropriate idempotency and
  lookup indexes.

Checkout flow:

1. Require an `Idempotency-Key` header and validate its format/length.
2. Resolve the authenticated customer's cart and selected owned address.
3. Start a database transaction.
4. Load cart items and lock all referenced menu-item rows with
   `lockForUpdate()`.
5. Revalidate restaurant active/open state, item availability, quantities, and
   stock.
6. Revalidate the coupon and calculate subtotal, discount, delivery fee, tax,
   and total on the server.
7. Enforce the restaurant minimum order amount.
8. Detect an existing order for the same customer/idempotency key and return
   the original result without creating a duplicate.
9. Create the order and order-item snapshots.
10. Create the initial status-history row.
11. Record coupon usage only for the successful order.
12. Decrement finite stock values without changing unlimited stock.
13. Clear the cart.
14. Commit the transaction.
15. Dispatch post-commit order events/listeners.

Payment:

- Support cash and simulated card only.
- Keep payment status explicit even though no real gateway is integrated.
- Use a small boundary/contract only if the simulated implementation needs to be
  replaceable or independently verified.

Customer API:

- Create order/checkout.
- Paginated order history with status filtering.
- Order details with items and status timeline.
- Cancel only while pending.

Verification:

- Verify successful checkout and exact totals.
- Verify address and item snapshots, unavailable/insufficient-stock failures,
  restaurant state failures, coupon failures, stock decrement, cart clearing,
  idempotent repeated checkout, customer ownership, and cancellation behavior.

Exit criteria:

- Checkout is transaction-safe, race-aware, idempotent, and independently
  verifiable through the API and transaction checks.

### Module 6 — Order state machine and manager processing

Domain state:

- Define the order status enum with the PRD transitions:
  `pending`, `accepted`, `rejected`, `cancelled`, `preparing`, `ready`,
  `out_for_delivery`, and `delivered`.
- Centralize allowed transitions and actor permissions in one focused state
  machine/service.
- Reject invalid transitions with a translated business exception.
- Require a rejection reason when moving from pending to rejected.

Transition behavior:

- Every valid transition writes an `order_status_histories` row.
- Stock is restored for rejected orders according to the agreed inventory
  invariant.
- Customer cancellation remains limited to pending.
- Dispatch `OrderStatusChanged` after the transaction commits.

Manager API:

- List own restaurant orders with status/date filters.
- View an own-restaurant order and its timeline.
- Transition status through the state machine.
- Never expose or mutate another restaurant's orders.

Admin API:

- View all orders with controlled filters and resources.

Verification:

- Verify every allowed and invalid transition, actor authorization, required
  rejection reason, history creation, and stock restoration behavior.

Exit criteria:

- All status changes go through one validated state machine and are auditable.

### Module 7 — Events, queued notifications, and operational hardening

Events/listeners:

- Add `OrderPlaced` and `OrderStatusChanged` events with the minimum safe
  payload.
- Add queued listeners for order/status notifications using the configured
  database queue.
- Ensure listeners are dispatched after successful transactions.
- Keep notification delivery implementation simple (log/mail) because push
  notifications are out of scope.

Hardening:

- Add rate limits for login and checkout/order creation.
- Ensure failures are visible through standard exception handling and queue
  failures are not swallowed.
- Review sensitive fields in resources/logs/events.
- Add stable ordering to all paginated queries.

Verification:

- Verify event dispatch after success and not after rollback.
- Verify queued listener behavior and rate-limit thresholds.

Exit criteria:

- Critical asynchronous behavior is observable, retryable, and does not create
  duplicate orders.

### Module 8 — Reviews and rating aggregates

Schema:

- `reviews`: unique order foreign key, user and restaurant foreign keys, rating
  1–5, comment, timestamps.
- Add indexes/constraints for one review per order and efficient restaurant
  listing.

Behavior:

- Only the customer who owns the order may review it.
- Order must be delivered.
- Each order may be reviewed once.
- Restaurant association must match the order.
- Recalculate `avg_rating` and `ratings_count` safely after create/update/delete
  decisions are finalized.

API:

- Customer creates a review for a delivered order.
- Public paginated restaurant reviews.
- Admin/manager read access follows the existing visibility rules.

Verification:

- Verify delivered-order success, invalid/duplicate rejection, rating range
  validation, and aggregate update correctness.

Exit criteria:

- Restaurant ratings are derived from valid reviews and exposed consistently.

### Module 9 — Manager statistics and admin management

Manager:

- Add cached daily statistics for orders today, revenue today, and top items.
- Scope every metric to the authenticated manager's restaurant.
- Use stable date boundaries and invalidate or expire cache after order changes.

Admin:

- Restaurant create/update/status management and manager assignment.
- Coupon management.
- All-order listing.
- User listing and active/inactive status management.
- Restaurant/user deactivation must preserve referential integrity and produce
  the standard translated response.

Verification:

- Verify manager statistics isolation, statistic values, cache refresh behavior,
  admin-only access, and status changes.

Exit criteria:

- Admin and manager operational workflows are complete without introducing an
  admin web UI.

### Module 10 — Seeds, documentation, and release polish

Seed data:

- One admin.
- Managers and restaurants with categories and approximately ten items each.
- Customers.
- Valid, expired, and usage-limited coupons.
- Demo data must use safe development-only credentials and never expose secrets
  in committed source or documentation beyond explicitly approved demo values.

Documentation:

- Update README setup, migrations/seeding, demo accounts, roles, and happy-path
  API flow.
- Complete Scramble-compatible request/response definitions.
- Add examples for success and 401/403/404/422 errors.
- Document pagination, status values, idempotency key usage, money units, and
  simulated payment behavior.
- Export OpenAPI JSON and a Postman collection only after the generated contract
  is stable and the user explicitly accepts committing generated artifacts.

Final validation:

- Fresh setup/migrate/seed works.
- Critical feature suite is green.
- Pint passes for changed PHP files.
- Route/resource/docs output matches the implemented API, not stale PRD examples.

## 5. Cross-cutting file plan

Expected additions or targeted changes, introduced module-by-module:

### Application

- `app/Enums/` for shared domain enums.
- `app/Models/` for menu, cart, coupon, order, history, and review records.
- `app/Http/Controllers/Api/` for customer, manager, and admin endpoints.
- `app/Http/Requests/` grouped by feature.
- `app/Http/Resources/` for stable public and authenticated representations.
- `app/Policies/` for ownership and role-sensitive record access.
- `app/Rules/` only for reusable validation logic.
- `app/Exceptions/` only for expected domain failures that need centralized
  rendering.
- `app/Events/` and `app/Listeners/` for post-commit order events.
- `app/Jobs/` only where queued behavior requires an explicit job boundary.
- `app/Providers/` only for actual interface bindings or event registration.

### Persistence

- One narrowly scoped anonymous migration per schema concern.
- Seeders for demo data and edge-case coupon data.
- Dedicated migrations for soft deletes or later schema changes.

### HTTP

- `routes/api.php` grouped visibly by public, customer, manager, and admin
  access.
- Existing locale, auth, role, and JSON exception behavior preserved.
- Rate-limit configuration added through the existing Laravel mechanism rather
  than custom middleware unless the framework configuration cannot express it.

### Localization

- Feature-specific translation files in both `resources/lang/en/` and
  `resources/lang/ar/`.
- No hard-coded user-facing success or error messages in controllers/actions.

### Verification

- Verify request-facing behavior and authorization boundaries through the API.
- Use representative data and repeatable request payloads for calculations and
  state-machine behavior.

## 6. Dependency and sequencing rules

The implementation order is intentionally dependency-driven:

1. Baseline decisions and shared contracts.
2. Restaurant/menu data, because carts need valid menu items.
3. Address completion, because checkout needs owned addresses.
4. Cart/pricing, because checkout consumes the cart.
5. Coupons, because both cart preview and checkout use the same validator.
6. Orders/checkout, because state transitions need orders and snapshots.
7. State machine/manager processing, because it consumes order records.
8. Events/queues/rate limits, because they depend on stable order operations.
9. Reviews, because they require delivered orders and restaurant aggregates.
10. Statistics/admin polish, because they aggregate stable operational data.
11. Seed/docs/release validation, because generated documentation must match the
    final contracts.

No module should silently depend on a later module. If a requirement cannot be
implemented without one, record the dependency in the module's acceptance
criteria before coding.

## 7. Validation strategy

For each module:

1. Read the relevant sibling implementation and applicable skill rules.
2. Generate Laravel files with `php artisan make:* --no-interaction`.
3. Add schema, model, request, resource, route, and translation changes as
   one coherent vertical slice.
4. Verify the changed endpoints, persistence behavior, and error responses.
5. Run `vendor/bin/pint --dirty --format agent` for modified PHP files.
6. Review the diff against `AGENTS.md`, the applicable best-practice rules, and
   this plan before moving to the next module.

Required critical-flow verification before completion:

- Register/login/protected access.
- Restaurant/menu browsing and manager ownership.
- Cart single-restaurant and quantity rules.
- Coupon validity and usage limits.
- Successful checkout and exact totals.
- Stock failure and stock decrement/restoration.
- Idempotent checkout.
- Invalid order transition.
- Customer cancellation.
- Manager order isolation.
- Delivered-order review and rating aggregation.
- Admin authorization and status management.

## 8. Risks and mitigations

| Risk | Mitigation |
| --- | --- |
| PRD says Sanctum while the app uses JWT | Keep JWT; treat auth migration as out of scope unless separately approved. |
| PRD uses manager/vendor inconsistently | Use `restaurant_manager` from the existing enum and routes. |
| Decimal restaurant money conflicts with integer order money | Decide units before pricing/checkout and migrate deliberately. |
| Cache tags unavailable on SQLite/database cache | Use driver-aware invalidation or versioned keys. |
| Overselling stock during concurrent checkout | Lock menu-item rows inside the checkout transaction and revalidate. |
| Duplicate mobile checkout submissions | Require a customer-scoped idempotency key under a unique constraint. |
| Invalid status changes scattered across controllers | Centralize transitions in one state machine/service. |
| Hidden N+1 queries in restaurant/order resources | Eager-load all resource relationships and verify representative endpoints. |
| Soft delete added without schema support | Add dedicated reversible migrations before enabling model traits. |
| Seven-day scope expands beyond interview value | Implement MVP first; defer favorites, auto-cancel scheduling, reorder, and
  advanced stats until all critical flows are green. |

## 9. Definition of done

The PRD implementation is complete when:

- All MVP endpoints are implemented under the repository-compatible API route
  structure.
- Authentication and role behavior remain compatible with current JWT routes.
- All business rules in the PRD have a single authoritative implementation.
- Checkout is transactional, stock-safe, server-priced, and idempotent.
- Order lifecycle transitions are validated and audited.
- Manager/customer/admin ownership boundaries are verified.
- Events/listeners, caching, rate limits, and API resources are operational.
- English and Arabic translations exist for all new user-facing messages.
- Seed data supports a complete demo flow and edge cases.
- Scramble/OpenAPI output reflects the actual endpoints and response contracts.
- Required endpoint checks pass, and modified PHP files are Pint-clean.
- README and generated artifacts describe the implemented behavior accurately.

## 10. First coding session after plan approval

The first implementation session should execute **Module 0** only:

1. Inspect and verify the current auth, restaurant, and address flows.
2. Confirm the money-unit decision and shared enum names.
3. Fix only tightly coupled baseline issues required by the next module.
4. Add the first menu migration/model/request/resource slice only after the
   baseline contract is settled.

No cart, checkout, order, coupon, event, or review code should be started before
the menu contract and its ownership behavior are stable.
