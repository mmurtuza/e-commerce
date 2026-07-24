# DinajpurITPark E-Commerce Platform

A modern, high-performance, multi-theme E-Commerce platform built with **Laravel 13**, **Filament v3**, **Livewire 3**, **Tailwind CSS v4**, and **Alpine.js**.

---

## 🌟 Key Features

### 🛒 Storefront & Shopping Experience
- **Multi-Theme Engine**: Real-time dynamic layout switching between **Classic**, **Modern**, and **Liquid** (Botiga Plants style) themes.
- **Product Catalog & Management**: Multi-variant products, photo galleries, brand & category hierarchies, tags, and multi-language product translations.
- **Shopping Cart & Wishlist**: Interactive cart management, coupon code discounts, and persistent customer wishlists.
- **Multi-Gateway Payment Integration**:
  - 💳 **Stripe**
  - 📱 **bKash** (with simulator and callback support)
  - 🇧🇩 **SSLCommerz**
  - 🌊 **Paddle**
  - 🍋 **Lemon Squeezy**
- **Customer Portal**: Dedicated user dashboard for order history, tracking, address book management, profile updates, and password management.
- **Product Reviews & Ratings**: Verified purchaser feedback and rating management.
- **Smart Search**: Fast full-text search powered by **Laravel Scout**.

### 🛠️ Admin Dashboard (Filament v3)
- **Comprehensive Management**: CRUD interfaces for Products, Orders, Customers, Categories, Brands, Banners, Blog Posts, Coupons, Reviews, Shipping Zones, and Pages.
- **Role-Based Access Control (RBAC)**: Fine-grained permissions powered by **Filament Shield**.
- **Theme & Navigation Control**: Dynamic category navigation menu layout and theme selector via administration settings.
- **Invoice & Reporting**: Order invoice generation in PDF via **DomPDF** and data export functionality using **Laravel Excel**.

---

## ⚡ Tech Stack

| Component | Technology / Library |
| :--- | :--- |
| **Framework** | [Laravel 13](https://laravel.com) |
| **PHP Version** | PHP 8.4 / ^8.2 |
| **Admin Panel** | [Filament v3](https://filamentphp.com) + Filament Shield |
| **Frontend Stack** | Livewire 3, Alpine.js v3, Tailwind CSS v4 |
| **Search Engine** | [Laravel Scout](https://laravel.com/docs/scout) |
| **Media & PDF** | Intervention Image, DomPDF, Laravel Excel |
| **Development** | Laravel Boost, Laravel Pint, PHPUnit 12 |

---

## 🚀 Quick Start

### Prerequisites
- **PHP** >= 8.2 (PHP 8.4 recommended) with PDO, MBString, BCMath, GD/Imagick extensions
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm**
- **SQLite** or **MySQL / PostgreSQL** database

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd dinajpuritpark
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database & Seeding**
   Configure your database credentials in `.env`, then run:
   ```bash
   php artisan migrate --seed
   ```

5. **Build Assets & Link Storage**
   ```bash
   php artisan storage:link
   npm run build
   ```

---

## 💻 Development Workflow

Start all development services concurrently (Laravel server, queue worker, logs, and Vite dev server):

```bash
composer run dev
# or
npm run dev
```

### Running Tests
Execute the PHPUnit test suite:
```bash
composer test
# or
php artisan test
```

### Code Formatting
Ensure PHP code adheres to project standards using Laravel Pint:
```bash
vendor/bin/pint
```

---

## 📁 Directory Overview

```
e-commerce/
├── app/
│   ├── Filament/            # Admin Panel resources, pages, and widgets
│   ├── Http/Controllers/    # Storefront & Payment controllers
│   ├── Models/              # Eloquent models & translation relations
│   └── Providers/           # App & Theme service providers
├── database/                # Migrations, seeders, and factories
├── resources/
│   ├── views/
│   │   ├── components/      # Blade components
│   │   ├── themes/          # Dynamic frontend themes (classic, modern, liquid)
│   │   └── storefront/      # Storefront page templates
├── routes/                  # Web, API, and auth routes
└── tests/                   # PHPUnit Feature & Unit tests
```

---

## 📄 License

This software is licensed under the [MIT license](LICENSE).
