
# News Aggregator API

Welcome to the News Aggregator API project! This is a RESTful API built with Laravel that aggregates news from various sources, supports user authentication, article management, user preferences, caching strategies, and includes scheduled data aggregation. This guide will help you set up, run, and understand the core features of the project.

---

## Table of Contents
1. [Requirements](#requirements)
2. [Setup Instructions](#setup-instructions)
3. [Configuration](#configuration)
4. [Usage](#usage)
    - [Authentication](#authentication)
    - [Article Management](#article-management)
    - [User Preferences](#user-preferences)
5. [Scheduled Data Aggregation](#scheduled-data-aggregation)
6. [Caching Strategy](#caching-strategy)
7. [API Documentation](#api-documentation)
8. [Testing](#testing)
9. [Docker Setup](#docker-setup)
10. [Additional Notes](#additional-notes)

---

## 1. Requirements

- **PHP**: 8.0+
- **Laravel**: 9.x
- **Docker** (optional, for containerized environment)
- **Database**: MySQL or any supported database
- **API Keys**: Access keys for selected news APIs (e.g., NewsAPI, The Guardian, NYTimes)

---

## 2. Setup Instructions

### Clone the Repository
```bash
git clone https://github.com/your-username/news-aggregator.git
cd news-aggregator
```

### Install Dependencies
```bash
composer install
```

### Configure the Application
1. Copy the `.env.example` file:
   ```bash
   cp .env.example .env
   ```
2. Update `.env` with your database connection, caching, and API keys.

### Generate Application Key
```bash
php artisan key:generate
```

### Run Migrations
```bash
php artisan migrate
```

### Install Laravel Sanctum
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### Seed the Database (Optional)
```bash
php artisan db:seed
```

### Run the Application
```bash
php artisan serve
```

---

## 3. Configuration

### Database
Update the following in your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### API Keys
API keys are stored in the `config/api_keys.php` file:
```php
return [
    'news_api' => 'YOUR_NEWSAPI_KEY',
    'guardian_api' => 'YOUR_GUARDIAN_API_KEY',
    'nytimes_api' => 'YOUR_NYTIMES_API_KEY',
];
```

---

## 4. Usage

### Authentication
- **Register**: `POST /api/register`
- **Login**: `POST /api/login`
- **Logout**: `POST /api/logout`

Include the token in the `Authorization` header for protected routes:
```http
Authorization: Bearer <your-token>
```

### Article Management
- **Fetch All Articles**: `GET /api/articles`
    - Supports pagination and filters (keyword, date, category, source).
- **Fetch Single Article**: `GET /api/articles/{id}`

### User Preferences
- **Set Preferences**: `POST /api/preferences` (JSON payload with categories, sources, authors)
- **Get Personalized Feed**: `GET /api/personalized-feed`

---

## 5. Scheduled Data Aggregation

The application fetches news articles daily using Laravel's scheduler.

### Fetch Command
```bash
php artisan articles:fetch
```

### Automate via Cron
Add the following to your `crontab`:
```bash
* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
```

---

## 6. Caching Strategy

- **Articles List**: Cached for 60 minutes with user-specific filters as cache keys.
- **Single Article**: Cached for 60 minutes using the article ID as the key.
- Cache invalidates automatically when an article is updated or deleted.

---

## 7. API Documentation

API documentation is generated using Swagger/OpenAPI.

- Access API Docs: `http://localhost:8000/api/documentation`
- Regenerate Docs:
  ```bash
  php artisan l5-swagger:generate
  ```

---

## 8. Testing

### Run All Tests
```bash
php artisan test
```

### Test Coverage
- **Authentication**: Registration, login, logout.
- **Articles**: Fetch all articles, fetch single article, handle errors.
- **Preferences**: Store and fetch user preferences.

---

## 9. Docker Setup

### Start Docker Containers
```bash
docker-compose up -d
```

### Run Migrations
```bash
docker-compose exec app php artisan migrate
```

### Fetch Articles
```bash
docker-compose exec app php artisan articles:fetch
```

### Access the Application
- API: `http://localhost:8000`
- Swagger Docs: `http://localhost:8000/api/documentation`

---

## 10. Additional Notes

### Performance Enhancements
- Caching for faster API responses.
- Database indices for frequently queried fields.

### Security
- Input validation to prevent XSS and SQL injection.
- Token-based authentication using Sanctum.
- Rate limiting applied to API endpoints.

### Deployment
- Secure `.env` and `config/api_keys.php`.
- Use a process manager (e.g., Supervisor) for queue workers.

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
