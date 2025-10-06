# Breadcrumb Productivity Tracker

A simple, beautiful productivity tracking web application built with Laravel and Vue.js. Track your productive time with customizable timers and visualize your progress with a GitHub-style activity grid.

## Features

- **Customizable Timer**: Set your preferred session duration (default 10 minutes)
- **Task Tracking**: Log what you're working on with each session
- **Activity Grid**: GitHub-style visualization showing your productivity over the past year
- **User Dashboard**: See your recent sessions and productivity patterns
- **Responsive Design**: Works beautifully on desktop and mobile devices
- **Docker Support**: Easy development setup with Docker

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Vue 3 with TypeScript and Inertia.js
- **Styling**: Tailwind CSS
- **Authentication**: Laravel Fortify
- **Database**: MySQL 8.0
- **Development Environment**: Docker & Docker Compose

## Prerequisites

- Docker Desktop installed on your machine
- Git

## Quick Start with Docker

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd breadcrumb-productivity
   ```

2. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

3. **Start Docker containers**
   ```bash
   docker-compose up -d
   ```

4. **Install dependencies**
   ```bash
   # Install PHP dependencies
   docker-compose exec app composer install

   # Install Node dependencies
   docker-compose exec app npm install
   ```

5. **Generate application key**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. **Run database migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

7. **Build frontend assets**
   ```bash
   docker-compose exec app npm run build
   # Or for development with hot reload:
   docker-compose exec app npm run dev
   ```

8. **Access the application**
   - Open your browser and navigate to: `http://localhost:8000`
   - Register a new account
   - Start tracking your productivity!

## Docker Services

The application runs with the following Docker services:

- **app**: PHP-FPM container running Laravel
- **nginx**: Web server (port 8000)
- **db**: MySQL database (port 3306)

## Database Structure

### Tables

1. **users**: User authentication and profile data
2. **user_settings**: Per-user configuration (timer duration)
3. **productivity_sessions**: Completed productivity sessions with task descriptions

## How to Use

1. **Register/Login**: Create an account or log in
2. **Set Timer Duration**: Go to Settings > Preferences to customize your timer (default: 10 minutes)
3. **Start a Session**:
   - Enter what you're working on in the task input
   - Click "Start Timer"
   - Focus on your task until the timer completes
4. **Track Progress**: Watch your activity grid fill up as you complete sessions
5. **Review History**: See your recent sessions and productivity patterns

## Development

### Running Tests
```bash
docker-compose exec app php artisan test
```

### Code Style
```bash
# PHP CS Fixer
docker-compose exec app ./vendor/bin/php-cs-fixer fix

# ESLint
docker-compose exec app npm run lint
```

### Database Commands
```bash
# Fresh migration (warning: deletes all data)
docker-compose exec app php artisan migrate:fresh

# Seed database with test data
docker-compose exec app php artisan db:seed

# Fresh migration with seeding
docker-compose exec app php artisan migrate:fresh --seed
```

## Project Structure

```
breadcrumb-productivity/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── ProductivitySessionController.php
│   │   └── UserSettingsController.php
│   └── Models/
│       ├── User.php
│       ├── UserSettings.php
│       └── ProductivitySession.php
├── database/
│   └── migrations/
│       ├── 2025_10_06_000001_create_user_settings_table.php
│       └── 2025_10_06_000002_create_productivity_sessions_table.php
├── resources/
│   └── js/
│       ├── components/
│       │   ├── ActivityGrid.vue
│       │   └── ProductivityTimer.vue
│       └── Pages/
│           ├── Dashboard.vue
│           └── Settings/
│               └── Preferences.vue
├── routes/
│   └── web.php
├── docker/
│   └── nginx/
│       └── default.conf
├── docker-compose.yml
├── Dockerfile
└── README.md
```

## Configuration

### Timer Duration

Users can customize their timer duration in Settings > Preferences. Each completed timer session represents one "box" on the activity grid, similar to GitHub's contribution graph.

**Recommended durations:**
- **10 minutes**: Quick tasks, high granularity
- **15 minutes**: Balanced approach
- **25 minutes**: Pomodoro technique

### Activity Grid

The activity grid shows:
- **One year** of productivity data
- **Color intensity** based on number of sessions completed per day
- **Hover details** showing tasks completed on each day

## Troubleshooting

### Docker Issues

**Containers won't start:**
```bash
docker-compose down
docker-compose up -d --build
```

**Permission issues:**
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Issues

Ensure the `.env` file has correct database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=breadcrumb
DB_USERNAME=breadcrumb
DB_PASSWORD=root
```

### Frontend Not Loading

Make sure to build assets:
```bash
docker-compose exec app npm run build
# Or for development:
docker-compose exec app npm run dev
```

## License

This project is open-source software licensed under the MIT license.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

If you encounter any issues or have questions, please open an issue on GitHub.
