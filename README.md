# OncoChemo — Chemotherapy Protocol Ordering System

A complete offline-capable Laravel 11 web application for oncology departments to manage chemotherapy protocols, patient records, and drug orders with automatic clinical calculations.

---

## Requirements

- PHP >= 8.2
- Composer
- SQLite (default, zero config) **or** MySQL 8+
- Node is NOT required (Tailwind CSS loaded via CDN, Alpine.js via CDN)

---

## Installation

### 1. Extract the project

```bash
unzip oncochemo.zip -d oncochemo
cd oncochemo
```

### 2. Install PHP dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:

```
HOSPITAL_NAME="Your Hospital Name"
APP_URL=http://localhost:8000
```

**For SQLite (default — no database server needed):**
```
DB_CONNECTION=sqlite
```
Then create the database file:
```bash
touch database/database.sqlite
```

**For MySQL:**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oncochemo
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

### 4. Run migrations and seed demo data

```bash
php artisan migrate --seed
```

This will create all tables and load:
- 25 common chemotherapy drugs
- 6 oncology diagnoses
- 8 ready-to-use protocols (AC, EC, R-CHOP, FOLFOX, FOLFIRI, Carboplatin+Paclitaxel, Gemcitabine+Carboplatin, Docetaxel)

### 5. Start the application

```bash
php artisan serve
```

Open: **http://localhost:8000**

---

## Offline Usage (No Internet Required)

The application is **fully offline after setup**. However, Tailwind CSS, Alpine.js, and Font Awesome are loaded from CDNs by default.

To make it **completely offline**, download these files and update `layouts/app.blade.php`:

| Library | URL | Save as |
|---------|-----|---------|
| Tailwind CSS CDN | https://cdn.tailwindcss.com | `public/tailwind.js` |
| Alpine.js | https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js | `public/alpine.min.js` |
| Font Awesome CSS | https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css | `public/fa/all.min.css` |
| Font Awesome Webfonts | (download the webfonts folder from Font Awesome) | `public/fa/webfonts/` |

Then update `resources/views/layouts/app.blade.php`, replacing the CDN links with:
```html
<script src="/tailwind.js"></script>
<script defer src="/alpine.min.js"></script>
<link rel="stylesheet" href="/fa/all.min.css">
```

Run for print view too in `resources/views/orders/print.blade.php`.

---

## Features

- **Patient Management** — MRN-based registration with anthropometric data
- **Diagnosis & Protocol Administration** — Full CRUD with drug builder UI
- **Clinical Calculations** — Auto BSA (Mosteller), CrCl (Cockcroft-Gault), Carboplatin (Calvert)
- **Order Creation** — Step-by-step ordering with real-time dose calculation
- **Dose Modifications** — Global % modification with per-drug manual override
- **Safety Checks** — Per-cycle caps, lifetime dose caps with modal acknowledgment
- **Cycle Tracking** — Auto cycle numbering, same-cycle detection (within 6 days)
- **Print Form** — Complete printable chemotherapy order with signature blocks
- **Order History** — Filterable by MRN, protocol, date, status
- **Cumulative Dose Tracking** — Per-patient lifetime dose tracker with visual bar

---

## Project Structure

```
app/
  Http/Controllers/
    Admin/          — DiagnosisController, ProtocolController, DrugController
    Api/            — PatientApiController, ProtocolApiController, OrderCalculationApiController
    DashboardController, PatientController, OrderController
  Models/           — Patient, Diagnosis, Protocol, ProtocolDrug, Drug, Order, OrderDrug, PatientCumulativeDose
  Services/         — ClinicalCalculationService (BSA, CrCl, Carboplatin, caps)
database/
  migrations/       — 8 migration files
  seeders/          — DatabaseSeeder with 8 real protocols
resources/views/
  layouts/app.blade.php
  dashboard.blade.php
  admin/diagnoses/, admin/protocols/, admin/drugs/
  patients/
  orders/           — index, create, show, print
  partials/alerts.blade.php
routes/web.php
```

---

## License

For internal hospital use only. Not for redistribution.
