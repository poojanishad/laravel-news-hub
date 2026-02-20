# 📰 News Aggregator Backend (Laravel 12)

A scalable and production-ready News Aggregation Backend built with Laravel 12.

This system fetches live news articles from multiple external APIs, stores them locally in a unified database structure, and exposes clean RESTful endpoints for frontend applications (React, Vue, SPA, Mobile Apps, etc.).

## Features

- Multiple live news provider integration
- Unified article storage format
- Advanced filtering support
- User preference-based personalization
- Scheduled automatic article fetching
- Clean architecture with SOLID principles
- Fully REST API driven
- Easily extendable provider system

## Integrated News Providers
The system currently integrates:

- NewsAPI
- The Guardian
- GNews

Each provider follows a common interface contract to maintain loose coupling and scalability.

## Architecture & Design Patterns
This project follows a clean layered architecture:

- Provider Interface Pattern
- Factory Pattern
- Service Layer Pattern
- Scheduled Command Pattern
- Dedicated Filtering Service
- Dedicated Preference Management Service

##  Design Principles Applied
- DRY (Don’t Repeat Yourself)
- KISS (Keep It Simple, Stupid)
- SOLID Principles:
  - Single Responsibility Principle
  - Open/Closed Principle
  - Liskov Substitution Principle
  - Interface Segregation Principle
  - Dependency Inversion Principle

## Tech Stack
- Laravel 12
- PHP 8+
- MySQL / SQLite
- REST API
- Laravel Scheduler (Cron)



## ⚙ Installation Guide

### 1️⃣ Clone Repository

```bash
git clone <repository-url>
cd project-folder
```

### 2️⃣ Install Dependencies

```bash
composer install
```

### 3️⃣ Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4️⃣ Add API Keys in `.env`
```
NEWSAPI_KEY=your_newsapi_key
GNEWS_KEY=your_gnews_key
GUARDIAN_KEY=your_guardian_key
```

### 5️⃣ Run Database Migrations

```bash
php artisan migrate
```

##  Fetch Articles Manually
To fetch articles from all configured providers:

```bash
php artisan news:fetch
```

This command pulls live articles and stores them in the database.



##  Automated Article Fetching (Scheduler)

Articles are configured to refresh hourly.

Run locally:

```bash
php artisan schedule:work
```

For deploy in live configure a cron job:
Run crontab -e

Enter your desired cron schuled time
* * * * * cd /var/www/your-project && php artisan schedule:run >> /dev/null 2>&1

```bash
* * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1
```


##  API Endpoints
Base URL:

```
http://localhost:8000/api
```

###  Articles

| Method | Endpoint | Description |
|--|-||
| GET | /articles | Get all articles |
| GET | /articles?search=AI | Search articles |
| GET | /articles?source=guardian | Filter by source |
| GET | /articles?category=technology | Filter by category |
| GET | /articles?date=2026-02-20 | Filter by date |
| GET | /articles?preferences=1 | Apply user preferences |
| GET | /articles?page=1 | Pagination support |

Multiple filters can be combined in a single request.

Example:
```
/api/articles?search=AI&source=guardian&category=technology
```

##  User Preferences

The system supports multiple selections for:

- Sources
- Categories
- Authors

Preferences are stored as JSON arrays in the database.

Example:

```json
{
  "sources": ["guardian", "newsapi"],
  "categories": ["technology", "business"],
  "authors": ["John Doe"]
}
```

Preferences dynamically influence article queries.

Authentication can be enabled depending on business requirements.



##  Database Structure (High Level)

### Tables

- articles
- users
- preferences

All external provider data is normalized into a unified article schema.


## 📁 Project Structure (Simplified)

```
app/
 ├── Console/
 ├── Contracts/
 ├── Services/
 ├── Providers/
 ├── Http/Controllers/
 ├── Models/
 └── Swagger/

routes/
 ├── api.php

database/
 ├── migrations/
```