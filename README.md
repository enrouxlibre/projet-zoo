# Zoo Project

A full-stack application with Symfony backend, React frontend, and MySQL database.

## Tech Stack

- **Backend**: PHP 8.4 (Alpine) with Symfony 8.0
- **Frontend**: Node 24 (Alpine) with React + Vite
- **Database**: MySQL 8.0
- **Development Mailer**: Mailpit

## Quick Start

### Prerequisites

- Docker
- Docker Compose

### Launch the entire project

Run this single command to start all services:

**PowerShell (Windows):**

```powershell
docker-compose up --build
```

**Bash (Linux/Mac):**

```bash
docker-compose up --build
```

> **Note**: The project uses environment variables from `.env.dev` for development. You can customize ports and other settings by editing this file.

### Access the application

Once all containers are running:

- **Frontend**: http://localhost:5173

### Stop the project

**PowerShell (Windows):**

```powershell
docker-compose down
```

**Bash (Linux/Mac):**

```bash
docker-compose down
```

### Stop and remove all data (including database)

**PowerShell (Windows):**

```powershell
docker-compose down -v
```

**Bash (Linux/Mac):**

````bash
docker-compose down -v
```bash
docker-compose down
The backend runs on PHP 8.4-alpine with the built-in development server.

To run Symfony console commands:

**PowerShell (Windows):**
```powershell
docker-compose exec backend php bin/console <command>
````

**Bash (Linux/Mac):**

```bash
docker-compose exec backend php bin/console <command>
```

Examples:

**PowerShell (Windows):**

```powershell
# Create a migration
docker-compose exec backend php bin/console make:migration

# Run migrations
docker-compose exec backend php bin/console doctrine:migrations:migrate

# Clear cache
docker-compose exec backend php bin/console cache:clear
```

**Bash (Linux/Mac):**

````bash
# Create a migration
docker-compose exec backend php bin/console make:migration

# Run migrations
docker-compose exec backend php bin/console doctrine:migrations:migrate
The frontend runs with Vite's development server with hot-reload enabled.

To run npm commands:

**PowerShell (Windows):**
```powershell
docker-compose exec frontend npm <command>
````

**Bash (Linux/Mac):**

```bash
docker-compose exec frontend npm <command>
```

Examples:

**PowerShell (Windows):**

````powershell
### Database

To access MySQL directly:

**PowerShell (Windows):**
```powershell
docker-compose exec database mysql -u app -papp123 zoo_app
````

**Bash (Linux/Mac):**

```bash
docker-compose exec database mysql -u app -papp123 zoo_app
```

**Bash (Linux/Mac):**

```bash
# Install a new package
docker-compose exec frontend npm install <package-name>

# Build for production
docker-compose exec frontend npm run build
```

### Frontend (React + Vite)

The frontend runs with Vite's development server with hot-reload enabled.

To run npm commands:

```bash
docker-compose exec frontend npm <command>
```

Examples:

````bash
# Install a new package
docker-compose exec frontend npm install <package-name>

### Permission issues

On Linux/Mac, if you encounter permission issues:

```bash
sudo chown -R $USER:$USER backend/var backend/vendor
sudo chown -R $USER:$USER frontend/node_modules
````

### Rebuild containers

If you make changes to Dockerfiles:

**PowerShell (Windows):**

```powershell
docker-compose up --build
```

**Bash (Linux/Mac):**

```bash
docker-compose up --build
```

### View logs

**PowerShell (Windows):**

```powershell
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f database
```

**Bash (Linux/Mac):**

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f database
```

### Clean restart

If you need to completely reset everything:

**PowerShell (Windows):**

```powershell
docker-compose down -v; docker-compose up --build
```

**Bash (Linux/Mac):**

````bash
docker-compose down -v && docker-compose up --build
```o chown -R $USER:$USER backend/var backend/vendor
sudo chown -R $USER:$USER frontend/node_modules
````

### Rebuild containers

If you make changes to Dockerfiles:

```bash
docker-compose up --build
```

### View logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f database
```
