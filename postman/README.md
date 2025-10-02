# VOSIZ API Postman Usage

This folder contains a ready-to-import Postman collection and environment for testing the ecommerce API.

## Files
- `VOSIZ_API.postman_collection.json` – All public, authenticated, and admin endpoints.
- `VOSIZ_API.postman_environment.json` – Environment variables (`base_url`, `api_token`, `admin_token`).

## Import Steps
1. Open Postman > Import > Select both JSON files in this folder.
2. Select the imported environment (top-right in Postman) and edit:
   - `base_url`: your running domain, e.g. `https://vosiz.run.place` (no trailing slash) OR local dev `http://127.0.0.1:8000`.
   - Leave tokens empty for now.

## Getting Auth Tokens (Laravel Sanctum)
There is no explicit login API route defined in `routes/api.php`; authentication happens through the web/session flow (Fortify/Jetstream). To call protected API routes you need a **personal access token**.

### Option A: Create Token via UI
1. Log into the app as a normal user in the browser.
2. Visit your API tokens page (e.g. `/admin/api-tokens` or the profile token management page if available).
3. Create a token named `Postman`.
4. Copy the generated plain-text token **once** and paste it into the Postman environment variable `api_token`.

### Option B: Use Tinker
```bash
php artisan tinker --execute='echo App\\Models\\User::where("role","admin")->first()->createToken("PostmanUI", ["*"])->plainTextToken."\n";'
```
Copy result into `admin_token` (and `api_token` if you want to test as admin only).

## Using the Collection
- Public folder: can be sent immediately (no auth header).
- Authenticated folder: requires `Authorization: Bearer {{api_token}}`.
- Admin folder: requires `Authorization: Bearer {{admin_token}}` (user with role `admin`).

## Creating Additional Test Data
If categories are empty, create at least one category in the Admin UI so `category_id` 1 exists (or adjust JSON body in *Create Product (Admin)* request).

## Common Issues & Fixes
| Problem | Cause | Fix |
|---------|-------|-----|
| 401 Unauthorized | Missing/expired token | Generate new token; ensure header: `Authorization: Bearer <token>` |
| 419 CSRF / HTML response | Hitting web route instead of API or sending cookies unexpectedly | Ensure URL starts with `/api/...` and remove cookies tab in Postman |
| 422 Validation error on create product | Required fields missing | Include `name`, `description`, `price`, `category_id` |
| 500 Mongo error on admin products | Mongo unreachable (IP not whitelisted) | Whitelist server IP in Atlas or restore rule |

## Extending
Add your own endpoints by duplicating requests; keep variables so environments stay portable.

## Quick Smoke Test Order
1. GET Featured Products
2. GET Categories
3. GET Search Products (ensure non-empty or returns empty array)
4. Generate `admin_token`
5. GET Admin Products
6. POST Admin Create Product (check 201 / JSON result)
7. GET Admin Products again (pagination updated)

Happy testing! 🚀
