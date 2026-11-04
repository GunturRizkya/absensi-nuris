# AGENT INSTRUCTION & CODING STANDARDS
**Stack Target:** Laravel (Latest Stable), Tailwind CSS, Alpine.js / Livewire
**Use Case:** Enterprise / Commercial Client Applications (High Quality, Production-Ready)

---

## 1. GENERAL PRINCIPLES & ANTI-SLOP POLICY
* **Production-Ready First:** Write code meant for real commercial clients (schools, businesses, government agencies). Never output placeholder logic, pseudo-code, unhandled edge cases, or "TODO" comments in primary code.
* **No AI Slop / Bloat:**
  * DO NOT add unnecessary comments stating the obvious (e.g., `// Initialize controller`).
  * DO NOT leave unused imports, dead variables, or leftover debugging calls (`dd()`, `dump()`, `console.log()`).
  * DO NOT create overly nested logic. Use early returns and small, single-responsibility functions/methods.
* **Planning First:** Before writing code for any complex task or multi-file feature, present a concise architectural plan and wait for confirmation if the implementation involves significant database changes or core refactoring.

---

## 2. ARCHITECTURE & LARAVEL CONVENTIONS
* **Strict Modern Laravel Standards:**
  * Adhere to standard Laravel conventions: PSR-12, clean naming (PascalCase for classes, camelCase for methods, snake_case for DB columns).
  * Enforce strict type hinting for all method arguments, returns, and properties (`declare(strict_types=1)` where applicable).
* **Fat Models, Thin Controllers:**
  * Keep Controllers light. Business logic belongs in dedicated **Action Classes**, **Services**, or **Form Requests**.
  * Use **Form Request Classes** for all validation logic instead of inline controller validation.
* **Database & ORM Safety:**
  * Always address the N+1 query problem using eager loading (`with()`).
  * Use Database Transactions (`DB::transaction()`) for any multi-step DB write operations.
  * Use proper Foreign Key constraints, DB indexing for searchable columns, and Soft Deletes where appropriate for enterprise software.

---

## 3. UI/UX & TAILWIND DESIGN SYSTEM
* **Non-Generic Aesthetics:**
  * Avoid raw, default Tailwind looks (e.g., standard `bg-blue-600` buttons on stark white backgrounds).
  * Use modern, clean color palettes with refined slate/zinc shades for backgrounds, soft borders (`border-zinc-200` / `border-zinc-800`), and purposeful accent colors.
  * Ensure generous whitespace (`padding`/`margin`), high-contrast typography, and smooth micro-interactions (`transition-all duration-200 ease-in-out`, `hover:`, `active:` states).
* **The 5-State UX Rule:** Every dynamic UI component or data listing MUST account for:
  1. **Ideal State:** Clean presentation of populated data.
  2. **Loading State:** Skeleton loaders (`animate-pulse`) instead of basic "Loading..." text.
  3. **Empty State:** Informative graphics/messages with clear CTA buttons (e.g., "Belum ada data siswa. Tambah Siswa Baru").
  4. **Error State:** Friendly inline validation messages and error boundaries.
  5. **Edge Cases:** Long text truncations, mobile screen adaptations, and overflow handling.
* **Micro-Interactions & Reactivity:**
  * Use **Alpine.js** (or Livewire) for smooth client-side interactions (modals, dropdowns, tabs, real-time filtering) without full-page refreshes.
  * Always ask for or implement confirmation modals (not browser native `confirm()`) for destructive actions like deleting records.

---

## 4. SECURITY & PERFORMANCES FOR COMMERCIAL APPS
* **Enterprise Security Standard:**
  * Always apply proper Authorization (Laravel **Policies** or **Gates**) before serving data or allowing CRUD actions. Never rely solely on UI button hiding.
  * Prevent mass-assignment vulnerabilities (explicit `$fillable` or protected `$guarded`).
  * Sanitize user input and enforce proper CSRF protection.
* **Data Privacy & Auditing:**
  * Design systems with proper user roles and permissions (e.g., Admin, Guru, Siswa).
  * For sensitive client platforms, include status tracking, soft deletion, and timestamp auditing (`created_by`, `updated_by` where critical).

---

## 5. RESPONSE & CODE OUTPUT FORMAT
* When asked to build a feature, provide:
  1. **Clean Code:** Complete, non-truncated code blocks. Do not skip parts with `// ... rest of the code`.
  2. **Security & Validation:** Fully implemented validation rules and authorization checks.
  3. **UX Components:** Integrated Blade templates with full Tailwind styling and Alpine.js interactivity.