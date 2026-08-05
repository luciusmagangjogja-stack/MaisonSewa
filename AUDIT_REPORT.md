# SewaJas — Full Audit Report

Generated: 2026-07-28

---

## 1. Executive Summary

Project is functionally rich but suffers from **UI inconsistency**, **race-condition exposure**, **duplicate business logic**, **inconsistent authorization**, and **dead/incomplete code paths**.

**Modules audited:**
- Customer (CRUD, soft delete, restore, search, duplicate phone)
- Rental (create, return, payment, status, QR, guarantee)
- Invoice (list, show, print, PDF, cancel)
- Payment (create, update, delete, void, refund)
- Product (CRUD, QR, stock)
- Reports (revenue, transactions, stock, returns)
- Dashboard (real-time widgets + charts)
- Search (global cross-entity)
- Notifications, Broadcast, Settings, Branch, User

**Overall risk level: HIGH**

---

## 2. Critical Bugs (Must Fix Before Production)

### BUG-01: Payment Number Race Condition
| Field | Value |
|-------|-------|
| Issue | `PaymentService::create()` and `RentalService::generatePaymentNumber()` generate payment numbers using `PAY-` + `YmdHis` timestamp with NO pessimistic lock. Two concurrent payments can receive the same number. |
| Root Cause | `PaymentService.php:26` uses `now()->format('YmdHis')` inside a transaction but without `lockForUpdate()` on the last matching row. |
| Files | `app/Services/PaymentService.php`, `app/Services/RentalService.php` |
| Risk | 500 / DuplicateEntryException on `payments.payment_number` unique index (if added) or silent data corruption. |
| Fix Plan | Implement the same retry + `lockForUpdate()` pattern used in `RentalService::generateInvoiceNumber()`. Wrap in DB transaction. |

### BUG-02: Invoice Number Has No DB-Level Uniqueness Guarantee
| Field | Value |
|-------|-------|
| Issue | Invoice number uniqueness is enforced only in application code. If the Laravel app is deployed with multiple workers/queues, race condition is possible. |
| Root Cause | `rentals.invoice_number` column has no unique index. `RentalService::generateInvoiceNumber()` uses `withTrashed()` + `lockForUpdate()` correctly, but lacks a DB constraint as a safety net. |
| Files | `database/migrations/..._create_rental_table.php` (no unique index), `app/Services/RentalService.php` |
| Risk | Duplicate invoice numbers in production. |
| Fix Plan | Add a unique index on `invoice_number` + `branch_id` + `created_at` (or partial unique index). Add DB-level guard. |

### BUG-03: `RentalController::confirmReturnAjax` Returns Wrong JSON Field Names
| Field | Value |
|-------|-------|
| Issue | The JSON response maps `$item->price_per_day` to `"price"`, but `rentals/show.blade.php` AlpineJS reads `item.price` for display. However `RentalItem` model does NOT cast `price_per_day` correctly in all contexts, and the rental show page expects `price` not `price_per_day`. Also `item.size` is used but DB column is `product_size`. |
| Root Cause | Mismatch between backend JSON keys and frontend AlpineJS expectations. `RentalController.php:488-501` returns `price` but other places use `price_per_day`. `rentals/show.blade.php` uses `item.size` which doesn't exist on model. |
| Files | `app/Http/Controllers/RentalController.php`, `resources/views/rentals/show.blade.php` |
| Risk | JS errors, blank prices/sizes on return panel. |
| Fix Plan | Standardize JSON keys to match AlpineJS model. Add `getSizeAttribute` accessor on `RentalItem` or map `product_size` → `size` in JSON. |

### BUG-04: `RentalReturn` Table Never Populated
| Field | Value |
|-------|-------|
| Issue | `RentalReturn` model and table exist, but `RentalService::processReturn()` and `RentalController::updateStatus()` never create `RentalReturn` records. The dashboard `DashboardService` queries `rental_returns` table for `pengembalian_hari_ini`, which will always return 0. |
| Root Cause | Business logic was refactored to store return data on `rental_items` and `rentals` directly, but the old `rental_returns` table queries were never removed from dashboard. |
| Files | `app/Services/RentalService.php`, `app/Models/RentalReturn.php`, `app/Services/DashboardService.php` |
| Risk | Dashboard widget "Pengembalian Hari Ini" always shows 0 even when returns happen. |
| Fix Plan | Either (a) create `RentalReturn` records in `processReturn()`, or (b) rewrite dashboard to count rentals with `rental_status = 'returned'` and `actual_return_date = today()`. |

### BUG-05: `products.update` Route Registered Twice
| Field | Value |
|-------|-------|
| Issue | In `routes/web.php`, products have both `PATCH /{product}` (line 190) and `PUT /{product}` (line 191) mapped to the same controller method. This creates duplicate routes and ambiguous method resolution. |
| Root Cause | Copy-paste error during route definition. |
| Files | `routes/web.php` |
| Risk | 405 Method Not Allowed or unexpected behavior depending on middleware order. |
| Fix Plan | Remove the `PUT` duplicate. |

### BUG-06: Duplicate Report Routes
| Field | Value |
|-------|-------|
| Issue | `reports/stock` and `reports/returns` are defined inside TWO separate middleware groups (lines 196-201 duplicate lines 175-180). |
| Root Cause | Copy-paste error in route definition. |
| Files | `routes/web.php` |
| Risk | Middleware confusion; stock/returns reports accessible to `sales` role when they should only be for `super_admin,admin_toko`. |
| Fix Plan | Remove the duplicate route block. Keep only the first definition (inside `EnsureBranchScope` group) or consolidate with proper role middleware. |

### BUG-07: `RentalController::updateStatus` Late Fee Formula Inconsistency
| Field | Value |
|-------|-------|
| Issue | `RentalController::updateStatus()` calculates overdue days as `max(0, now()->diffInDays($rental->return_due_date, false) * -1)` (line 560). `RentalService::processReturn()` calculates `diffInDays($now)` (line 323). The two formulas can produce different results when `return_due_date` is today vs tomorrow. |
| Root Cause | Inconsistent date math. `diffInDays` with `false` returns negative when past, multiplied by -1 gives positive. But `diffInDays` with default (true) returns absolute. |
| Files | `app/Http/Controllers/RentalController.php`, `app/Services/RentalService.php` |
| Risk | Late fee amount differs depending on whether return goes through `updateStatus` (AJAX) or `processReturn` (form submit). |
| Fix Plan | Extract late-fee calculation into a single method on `Rental` model or `RentalService`. Use `max(0, $dueDate->diffInDays($now))`. |

### BUG-08: `RentalItem` Fillable Missing `product_size` but Has `size` Accessor Issue
| Field | Value |
|-------|-------|
| Issue | `RentalItem::$fillable` includes `product_size` but the view `rentals/show.blade.php` accesses `item.size` (line 179). The model has no `size` accessor. |
| Root Cause | Column name mismatch. |
| Files | `app/Models/RentalItem.php`, `resources/views/rentals/show.blade.php` |
| Risk | `item.size` returns null in UI. |
| Fix Plan | Add `getSizeAttribute()` accessor on `RentalItem` that returns `$this->product_size`. |

### BUG-09: `Product` Model `getPhotoUrlAttribute` Uses Unsplash External URL
| Field | Value |
|-------|-------|
| Issue | Default product photo falls back to `https://images.unsplash.com/...`. If offline or API changes, images break. |
| Root Cause | Hardcoded external dependency. |
| Files | `app/Models/Product.php:52` |
| Risk | Broken images in production. |
| Fix Plan | Use local placeholder image or SVG data URI. |

### BUG-10: `User` Model Avatar Falls Back to External Service
| Field | Value |
|-------|-------|
| Issue | `User::getAvatarUrlAttribute` uses `ui-avatars.com` API. Same offline risk. |
| Root Cause | External dependency. |
| Files | `app/Models/User.php:152` |
| Risk | Broken avatars. |
| Fix Plan | Use local generated SVG or initial avatar component. |

---

## 3. High-Priority Issues

### HIGH-01: Massive Duplicate Activity Log Code
Every controller/service method manually constructs `ActivityLog::create([...])` with identical structure (`user_id`, `branch_id`, `action`, `model_type`, `model_id`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`).

| Files | 8+ controllers/services |
| Risk | Inconsistent logs, missing fields, hard to change format. |
| Fix Plan | Create `Loggable` trait or `ActivityLogger` service with `log(string $action, Model $subject, string $description, array $oldValues = [], array $newValues = [])`. |

### HIGH-02: Authorization Bypass via Inline Checks Instead of Policies
Policies exist (`CustomerPolicy`, `ProductPolicy`, `CategoryPolicy`, `BranchPolicy`) but **controllers never call them**. Every controller uses inline `if (!$user->isSuperAdmin()) abort(403)`.

| Files | All controllers |
| Risk | Policy changes have no effect; authorization logic is scattered and inconsistent. |
| Fix Plan | Replace inline checks with `$this->authorize()` or `Gate::allows()`. |

### HIGH-03: `RentalService::createRental` Locks Products Twice (Performance)
In `RentalService::createRental()`, products are locked in Step 1 (`lockForUpdate()` for branch check), then locked AGAIN in the item creation loop.

| Files | `app/Services/RentalService.php:159-161` and `:221` |
| Risk | Unnecessary DB lock contention; slower under load. |
| Fix Plan | Reuse the locked product instances from Step 1. |

### HIGH-04: `RentalController::update()` Has Duplicate Guarantee Logic
`RentalController::update()` (lines 238-268) duplicates guarantee upload/update logic that already exists in `RentalService::createRental()` (lines 249-267).

| Files | `app/Http/Controllers/RentalController.php` |
| Risk | Bug fixes must be applied twice. |
| Fix Plan | Extract to `RentalService::syncGuarantee(Rental $rental, Request $request)`. |

### HIGH-05: `RentalController::update()` Phone Normalization Missing
`CustomerController::store()` and `update()` call `$this->normalizePhone()`, but `RentalController` never normalizes customer phone for display. Not a data bug, but customer phone formats are inconsistent (some stored as `628...`, some `08...`).

| Files | `app/Http/Controllers/CustomerController.php`, `app/Http/Controllers/RentalController.php` |
| Risk | Search misses customers if phone format differs. |
| Fix Plan | Always normalize on input. Always search with normalized digits. |

### HIGH-06: `SearchController` Includes Soft-Deleted Customers Without Indication
| Field | Value |
|-------|-------|
| Issue | `SearchController::index()` uses `Customer::withTrashed()` (line 24). Deleted customers appear in search results for all roles. Non-super-admin users see deleted customers from other branches. |
| Root Cause | `withTrashed()` used without branch scope for deleted records. |
| Files | `app/Http/Controllers/SearchController.php` |
| Risk | Privacy leak; users see data they shouldn't. |
| Fix Plan | Scope `withTrashed()` by branch, and visually mark deleted results. |

### HIGH-07: `RentalController::edit()` Restricts Products to "Jas%" Category
| Field | Value |
|-------|-------|
| Issue | `RentalController::edit()` (line 97-101) filters products to only categories starting with "Jas". This hardcoded business rule prevents editing rentals with other product categories. |
| Root Cause | Legacy hardcoded filter. |
| Files | `app/Http/Controllers/RentalController.php:97` |
| Risk | Data integrity; rentals with non-Jas products cannot be edited. |
| Fix Plan | Remove hardcoded filter; show all products or use rental's existing products + available products. |

### HIGH-08: `Rental` Model `status_label` Has Phantom Statuses
`Rental::statusLabels()` includes `'booked'` and `'completed'` which are never set by any controller or seeder. The actual statuses are `waiting`, `active`, `overdue`, `returned`, `cancelled`.

| Files | `app/Models/Rental.php:172-183` |
| Risk | UI shows "Booked" or "Completed" badges if data is inconsistent. |
| Fix Plan | Remove phantom statuses or add migration/constants for them. |

### HIGH-09: Dashboard Polling Every 10 Seconds Without Backoff
| Field | Value |
|-------|-------|
| Issue | `_dashboard_common.blade.php` polls `/dashboard/data` every 10 seconds (line 338). No exponential backoff, no pause on error. |
| Root Cause | Aggressive polling. |
| Files | `resources/views/dashboard/_dashboard_common.blade.php` |
| Risk | Server overload with many concurrent admin users. |
| Fix Plan | Use SSE/WebSocket, or at minimum pause polling on 5xx errors. |

### HIGH-10: `ProductController::generateCode()` Race Condition
| Field | Value |
|-------|-------|
| Issue | Product code generation (`PRD` + branch + sequence) has no `lockForUpdate()`. Two concurrent product creates can get the same code. |
| Root Cause | No pessimistic lock. |
| Files | `app/Http/Controllers/ProductController.php:158-167` |
| Risk | Duplicate product codes. |
| Fix Plan | Add `lockForUpdate()` on last matching product. |

---

## 4. Medium-Priority Issues

### MED-01: Inconsistent Card Classes Across Views
- `customers/index.blade.php` uses `ds-card` and `card-container`
- `rentals/index.blade.php` uses `card-container`
- `products/index.blade.php` uses `card`
- `reports/*.blade.php` uses `card`
- `dashboard/*.blade.php` uses `ds-card`

### MED-02: Inconsistent Button Classes
- Some views use `btn-primary` / `btn-secondary` (CSS classes)
- Some views use inline `class="inline-flex items-center rounded-xl border..."` (e.g., `rentals/show.blade.php:46-54`)
- Some views use the `components.btn` Blade include

### MED-03: Inconsistent Avatar Rendering
- Sidebar user card: gradient `#2563eb → #1d4ed8` (inline style)
- Customer table: gradient `#6366f1 → #8b5cf6` (inline style)
- Search page: solid `bg-blue-50 font-bold text-blue-700` (no gradient)
- Customer profile: uses external `ui-avatars.com`

### MED-04: `RentalController::scanQr()` Regex Allows `RCPT-INV` Prefix
The scan page accepts `RCPT-INV2026070310014` format but `Rental` model `invoice_number` is generated with `INV` prefix only. Receipt numbers are `RCPT-INV...` but they point to the same rental. This is OK functionally but confusing.

### MED-05: `CustomerController::index()` Filter Inconsistency
Route param is `status` but values are `active`, `deactivated`, `all`. The view uses `request('status')`. The `deactivatedCount` query uses `onlyTrashed()`. This is correct but the naming is inconsistent with the user-facing "Dinonaktifkan" label.

### MED-06: `RentalService::recalculatePaymentStatus()` Logs on Every Call
Every payment update triggers `logActivity('recalculate_payment_status', ...)` (line 67). This floods activity logs with low-value entries.

### MED-07: `RentalReceiptController` Routes But No Controller Logic Reviewed
`RentalReceiptController` handles receipt views but was not fully audited for PDF generation or QR logic.

### MED-08: `notifications/index.blade.php` Uses `btn-sm` Class That Doesn't Exist
`class="btn-secondary btn-sm"` — `btn-sm` is not defined in CSS.

### MED-09: Missing `remaining_amount` Cast on Rental Model
`remaining_amount` is updated dynamically but not in `$casts`. It works because it's cast on the fly, but TypeScript/IDE tooling won't catch issues.

### MED-10: `Payment` Model Lacks `SoftDeletes`
Payments should be soft-deletable for audit compliance. Currently `Payment::destroy()` hard-deletes.

---

## 5. Low-Priority / Technical Debt

### LOW-01: Legacy `.bb_tmp_` File in Controllers
`app/Http/Controllers/.bb_tmp_confirm_return_ajax_snippet_note.txt` — leftover from AI coding session.

### LOW-02: TODO Markdown Files in Root
`TODO.md`, `TODO_FIXES_CUSTOMERS_BUGS.md`, `TODO_DASHBOARD.md`, `TODO_REDESIGN_UI.md`, etc. Should be cleaned up or moved to `.claude/` or docs.

### LOW-03: `package-lock.json` at Root
Should be in `frontend/` or ignored if using Laravel Mix/Vite only.

### LOW-04: `composer.json` Name Mismatch
`"name": "laravel/laravel"` — should be `rental-jas/rental-jas` or similar.

### LOW-05: `User::hasRole()` Conflicts with `CheckRole` Middleware
Both `User::hasRole()` and middleware exist. Middleware is used in routes; `hasRole()` is used in some controllers. Should standardize on one approach.

### LOW-06: `Rental::STATUS_WAITING` Used But `waiting` Not in `updateStatus` Validated Values
`RentalController::updateStatus()` validates `rental_status` as `active,returned,overdue` only — `waiting` is excluded.

### LOW-07: `CustomerPolicy` Not Registered
`AuthServiceProvider.php` may not map `CustomerPolicy` to `Customer` model.

### LOW-08: `rentals/show.blade.php` Uses `displayedPhotoModal` Typo
`displayedPhotoModal = null` (line 568) — should be `displayPhotoModal` or similar.

### LOW-09: `lucide.createIcons()` Called Redundantly
Called in `DOMContentLoaded`, `alpine:init`, and `@push('scripts')` in multiple views. Should be called once globally.

### LOW-10: `RentalService::generateQrCode()` Uses `file_put_contents` Without Error Handling
If `storage/app/public/qrcodes/rentals/` is not writable, the QR generation fails silently or with a PHP warning.

---

## 6. UI Consistency Audit

| Component | Current State | Required State |
|-----------|--------------|----------------|
| **Cards** | Mix of `ds-card`, `card`, `card-container`, `stat-card` with different shadows, borders, radii | Unified `ds-card` class: `rounded-xl border shadow-sm hover:shadow-md transition-all` |
| **Buttons** | Mix of `btn-primary`, `btn-secondary`, inline Tailwind, missing `rounded-xl` on some | All buttons: `rounded-xl border min-h-[44px] px-5 hover:-translate-y-0.5 transition-all focus:ring-2` |
| **Search Boxes** | Navbar has `ds-search` (height 50px, rounded-[18px]); page search uses `form-input` (rounded-xl) | Unified: `form-input pl-10 rounded-xl` with icon vertically centered |
| **Dropdowns** | Some use `appearance-none` with custom chevron; others are plain selects | Unified: `form-input pr-10 appearance-none` with consistent chevron |
| **Tables** | `elegant-table` exists but some tables use plain `w-full` or `table` | All tables wrapped in `ds-card overflow-hidden` with `elegant-table` |
| **Forms** | Labels sometimes `text-sm font-medium`, sometimes `text-xs font-semibold uppercase` | Unified: `block text-sm font-semibold mb-1.5` |
| **Avatar** | Mix of inline gradients, solid blue boxes, external APIs | Use `avatar-premium` class (gradient indigo→violet + ring) |
| **Badges** | Mix of `badge-green`, `badge-red`, inline `bg-amber-100`, `badge-menunggu`, etc. | Unified: `badge` component with `type` param |
| **Empty States** | Custom HTML in each view; `components.empty-state` exists but rarely used | Use `components.empty-state` everywhere |
| **Modals** | SweetAlert2 inline in layout + per-view custom alerts | Centralize in layout or create `components.confirm-modal` |
| **Pagination** | `components.pagination` exists; some views use `$paginator->links()` without component | Always use `components.pagination` |

---

## 7. Dependency Graph (Simplified)

```
Routes (web.php)
├── DashboardController → DashboardService → [Customer, Product, Rental, Payment, RentalReturn, ActivityLog, Guarantee]
├── CustomerController → Customer, Rental, Payment, Guarantee, ActivityLog
├── RentalController → RentalService → [Rental, RentalItem, Product, Payment, Guarantee, ActivityLog, QrCode]
│   └── RentalReceiptController → Rental, DomPDF
├── InvoiceController → InvoiceService → [Rental, Payment, Product, ActivityLog, QrCode, DomPDF]
├── PaymentController → PaymentService → [Rental, Payment, ActivityLog, RentalService]
├── ProductController → Product, Category, QrCode
├── SearchController → Customer, Rental, Product
├── ReportController → [Rental, Product, Branch, Category, Payment, DomPDF]
├── BranchController → Branch
├── UserController → User
├── CategoryController → Category
├── NotificationController → Notification
├── BroadcastController → BroadcastTemplate, BroadcastSchedule
└── SettingsController → Setting (via DB)

Models
├── User ──┬── branch
│         ├── rentals (created_by)
│         ├── customer (hasOne)
│         └── notifications
├── Customer ──┬── branch
│             ├── user
│             ├── rentals
│             └── broadcastLogs
├── Rental ──┬── branch
│           ├── customer
│           ├── createdBy
│           ├── returnedBy
│           ├── items
│           ├── guarantees
│           ├── payments
│           ├── returnRecord
│           ├── activityLogs
│           └── statusHistories
├── RentalItem ──┬── rental
│               └── product
├── Payment ──┬── rental
│           └── receivedBy
├── Product ──┬── branch
│           ├── category
│           └── rentalItems
├── Guarantee ── rental
├── Branch ──┬── users
│           ├── rentals
│           ├── customers
│           └── products
└── RentalReturn ── rental
```

---

## 8. Fix Priority Matrix

| Priority | Count | Items |
|----------|-------|-------|
| P0 (Critical) | 10 | BUG-01 through BUG-10 |
| P1 (High) | 10 | HIGH-01 through HIGH-10 |
| P2 (Medium) | 10 | MED-01 through MED-10 |
| P3 (Low) | 10 | LOW-01 through LOW-10 |

---

## 9. Recommended Execution Order

1. **Phase 1 — Stabilize Core Data Integrity** (BUG-01 to BUG-04)
   - Fix payment number race condition
   - Add DB unique index for invoice numbers
   - Fix confirmReturnAjax JSON keys
   - Fix RentalReturn dashboard query

2. **Phase 2 — Fix Route & Controller Bugs** (BUG-05 to BUG-10)
   - Remove duplicate routes
   - Fix late-fee formula inconsistency
   - Add RentalItem size accessor
   - Replace external image fallbacks

3. **Phase 3 — Deduplicate Business Logic** (HIGH-01 to HIGH-04)
   - Activity logger trait
   - Authorization via policies
   - Extract guarantee sync to service
   - Remove double-lock in createRental

4. **Phase 4 — Security Hardening** (HIGH-05 to HIGH-08)
   - Fix soft-deleted customer search leakage
   - Remove hardcoded "Jas%" product filter
   - Clean phantom rental statuses

5. **Phase 5 — UI Unification** (MED-01 to MED-04)
   - Standardize card, button, avatar, badge classes
   - Consolidate search input styling

6. **Phase 6 — Technical Debt** (MED-05 to LOW-10)
   - Cleanup TODO files
   - Fix lucide icon calls
   - Add soft deletes to Payment

---

*End of Audit Report*
