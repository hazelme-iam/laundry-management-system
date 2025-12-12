## Laundry Management System

This is a Laundry Management System built with **Laravel** and the **Jetstream** application starter kit. It manages customers, orders, machines, loads, reports, and in-app notifications for both admins and online customers.

The project is designed for a small to medium laundry shop, with support for:

- **Admin panel** for managing customers, orders, machines, and reports
- **Online customers** placing orders through a user portal
- **Automatic price calculation** (weight, add-ons, discount, total)
- **Machine assignment** and load optimization
- **Status tracking** (pending → washing → drying → ready → completed, etc.)
- **In-app notifications** for admins and customers (new orders, status changes, capacity alerts)

---

## Tech Stack

- **Language & Framework**
  - PHP 8.2+
  - Laravel 12.x

- **Starter Kit / Auth / UI**
  - [Laravel Jetstream](https://jetstream.laravel.com/) (teams optional, profile, sessions)
  - [Laravel Sanctum](https://laravel.com/docs/sanctum) (used via Jetstream)
  - [Livewire](https://livewire.laravel.com/) for some interactive components
  - [Tailwind CSS](https://tailwindcss.com/) (via Vite)

- **Frontend Tooling**
  - Vite
  - Node.js & npm

- **Database & Storage**
  - MySQL (or MariaDB)
  - Laravel Migrations & Seeders
  - Laravel Cache (for customer & order lists)

---

## Prerequisites

Make sure you have the following installed on your machine:

- PHP **8.2+**
- Composer
- Node.js **16+** and npm
- MySQL (or compatible) database server
- Git (optional, but recommended)

---

## 1. Clone the Repository

```bash
git clone https://github.com/your-username/laundry-management-system.git
cd laundry-management-system
```

If you already have the project (e.g. downloaded as ZIP), just open it in your editor and continue with the next steps.

---

## 2. Install PHP Dependencies

```bash
composer install
```

This installs Laravel, Jetstream, Livewire and all backend packages.

---

## 3. Install Frontend Dependencies

```bash
npm install
```

Then build the assets (for development or production):

```bash
# Development (watches for changes)
npm run dev

# OR single build
npm run build
```

> Make sure Vite is running (`npm run dev`) while you develop so styles and JS load correctly.

---

## 4. Environment Configuration

Copy the example environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

Then open `.env` and configure your database and other basics:

```env
APP_NAME="Laundry Management System"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laundry_system
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

Create the database manually in MySQL (e.g. via phpMyAdmin or CLI):

```sql
CREATE DATABASE laundry_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 5. Run Migrations and Seeders

This will create all tables and seed initial data (admin user, test customers, machines, etc.).

```bash
php artisan migrate --seed
```

If you ever need to reset:

```bash
php artisan migrate:fresh --seed
```

> Seeders include: machines (washers/dryers), test customers, customer clean-up, and possibly demo data for orders.

---

## 6. Run the Application

### Start the Laravel backend

```bash
php artisan serve
```

By default this runs at: `http://127.0.0.1:8000`

### Start Vite (frontend assets)

In another terminal:

```bash
npm run dev
```

Now open `http://127.0.0.1:8000` in your browser.

---

## 7. Default Access & Roles

During seeding, the project usually creates an **admin** user and some **regular users/customers**. Check the seeders in `database/seeders` for the exact credentials you configured (e.g. `DatabaseSeeder`, `TestCustomerSeeder`).

Typical pattern (adjust to your actual seeders):

- Admin user: `admin@example.com` / `password`
- Test customer users: `user1@example.com`, etc.

Roles/usage:

- **Admin**
  - Access to `/admin` sections via Jetstream/Sidebar layout
  - Manage customers, orders, machines, reports
  - See in-app notifications for new/online orders and capacity alerts

- **Online Customer**
  - Registers/logs in via Jetstream auth screens
  - Can create online orders
  - Sees order status and notifications in the user layout

---

## 8. Key Features Implemented

- **Order Management**
  - Automatic subtotal & total calculation (based on weight and add-ons)
  - Validation rules for weight, money, and dates
  - Status workflow (pending → approved → washing → drying → ready → completed, etc.)

- **Customer Management**
  - Real customers + virtual customers for users without a customer record
  - Differentiation between walk-in and online customers

- **Machines & Loads**
  - Washers and dryers with capacity
  - Load optimization logic (grouping orders by weight)

- **Notifications**
  - In-app notification bell (admin & user layouts)
  - New order notifications for admins
  - Order status change notifications for customers
  - Capacity alerts when machine capacity is nearly full

---

## 9. Running Tests (Optional)

If you have PHPUnit tests configured:

```bash
php artisan test
```

---

## 10. Tools & Libraries Summary

- Laravel 12.x
- Laravel Jetstream (with Sanctum & optional teams/profile management)
- Livewire
- Tailwind CSS
- Vite
- MySQL

---

## Troubleshooting

- **Blank CSS / broken layout**
  - Make sure `npm run dev` (or `npm run build`) has been run.
  - Check that `@vite(['resources/css/app.css', 'resources/js/app.js'])` is present in your layout.

- **Database errors**
  - Confirm DB settings in `.env`.
  - Ensure `php artisan migrate --seed` has run successfully.

- **Login issues**
  - Double-check seeded admin credentials.
  - Run `php artisan migrate:fresh --seed` if you changed seeders.

---

## License

This project is built on top of the Laravel framework, which is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

