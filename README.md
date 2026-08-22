# Knowledge Learning

E-learning platform built with Symfony 8, allowing users to purchase and follow online courses.

## 🚀 Live Demo

[]()

**Test credentials:**
- Admin: `admin@admin.com` / `admintest`
- Client: `user@user.com` / `useruser`

**Stripe test card:** `4242 4242 4242 4242` — any future date — any CVC

## 📋 Prerequisites

- PHP 8.4+
- Composer
- Symfony CLI
- MySQL 8.0+ or PostgreSQL 16+
- A Stripe account (test keys)
- A mailer (Mailpit or similar)

## ⚙️ Installation

**1. Clone the repository**
```bash
git clone https://github.com/Rromanee/knowledge-learning
cd knowledge-learning
```

**2. Install dependencies**
```bash
composer install
```

**3. Configure environment**
```bash
cp .env .env.local
```

Edit `.env.local` :
DATABASE_URL=
STRIPE_SECRET_KEY=sk_test_your_key
MAILER_DSN=smtp://127.0.0.1:1025
APP_SECRET=your_secret

**4. Create database and run migrations**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

**5. Load fixtures**
```bash
php bin/console doctrine:fixtures:load
```

**6. Start the server**
```bash
symfony server:start
```

App available at `http://localhost:8000`

## 🧪 Run tests

```bash
# Create test database
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test

# Run all tests
php bin/phpunit
```

## 🏗️ Architecture

src/
├── Controller/        # HTTP controllers (Admin + Frontend)
├── Entity/            # Doctrine ORM entities
├── Form/              # Symfony form types
├── Repository/        # Data access layer
├── Security/          # Authentication & email verification
└── Service/           # Stripe payment service

## 🔧 Tech Stack

- **Framework:** Symfony 8
- **Database:** PostgreSQL
- **ORM:** Doctrine
- **Payments:** Stripe Checkout
- **Auth:** Symfony Security + email verification
- **Tests:** PHPUnit
- **Deploy:** Railway