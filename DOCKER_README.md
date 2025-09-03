# Docker Setup for Poptin Poll Management System

This document explains how to run the Poptin Poll Management System using Docker.

## Prerequisites

- Docker (version 20.10 or higher)
- Docker Compose (version 2.0 or higher)
- At least 4GB of available RAM
- At least 10GB of available disk space

## Quick Start

### 1. Development Environment

```bash
# Clone the repository (if not already done)
git clone <repository-url>
cd poptin

# Copy environment file
cp .env.example .env

# Build and start all services
docker-compose up -d --build

# Install dependencies and run migrations
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Access the application
# Main app: http://localhost:8000
# PHPMyAdmin: http://localhost:8080
```

### 2. Production Environment

```bash
# Copy production environment file
cp .env.production .env

# Edit .env file with your production values
nano .env

# Build and start production services
docker-compose -f docker-compose.prod.yml up -d --build

# Run production setup
docker-compose -f docker-compose.prod.yml exec app composer install --no-dev --optimize-autoloader
docker-compose -f docker-compose.prod.yml exec app php artisan key:generate
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

## Services

### App Service
- **Port**: 8000 (dev) / 80 (prod)
- **Image**: Custom PHP 8.2 + Nginx
- **Features**: PHP-FPM, Nginx, Supervisor, Queue Worker

### MySQL Service
- **Port**: 3306
- **Image**: MySQL 8.0
- **Database**: poll_management
- **User**: poll_user / poll_password

### Redis Service
- **Port**: 6379
- **Image**: Redis 7 Alpine
- **Purpose**: Caching and Queue Management

### Reverb WebSocket Service
- **Port**: 8080
- **Image**: Laravel Reverb
- **Purpose**: Real-time WebSocket communication for live voting
- **Features**: Broadcasting, presence channels, private channels

### PHPMyAdmin Service (Development Only)
- **Port**: 8081
- **Image**: phpMyAdmin
- **Purpose**: Database Management Interface

## WebSocket Configuration

The system includes Laravel Reverb for real-time WebSocket communication, which is essential for live voting updates.

### WebSocket Service
- **Service Name**: reverb
- **Port**: 8080
- **Protocol**: WebSocket (ws:// for dev, wss:// for prod)
- **Features**: Broadcasting, presence channels, private channels

### Frontend Integration
Include the WebSocket configuration in your Blade templates:

```html
<script src="{{ asset('js/websocket-config.js') }}"></script>
<script>
    // Get WebSocket configuration
    const wsConfig = window.WebSocketConfig.getWebSocketConfig();
    
    // Create WebSocket connection
    const socket = new WebSocket(`${wsConfig.scheme}://${wsConfig.host}:${wsConfig.port}`);
    
    // Handle WebSocket events
    socket.onopen = function() {
        console.log('WebSocket connected');
    };
    
    socket.onmessage = function(event) {
        const data = JSON.parse(event.data);
        // Handle incoming messages (e.g., vote updates)
        console.log('Received:', data);
    };
</script>
```

### Testing WebSocket
```bash
# Check WebSocket service status
./docker.sh reverb-status

# View WebSocket logs
./docker.sh reverb-logs

# Test WebSocket health endpoint
curl http://localhost:8080/health
```

## Environment Configuration

### Development (.env.docker)
```env
APP_ENV=local
APP_DEBUG=true
DB_HOST=mysql
DB_DATABASE=poll_management
DB_USERNAME=poll_user
DB_PASSWORD=poll_password
REDIS_HOST=redis
```

### Production (.env.production)
```env
APP_ENV=production
APP_DEBUG=false
FORCE_HTTPS=true
DB_HOST=mysql
DB_DATABASE=poll_management
DB_USERNAME=poll_user
DB_PASSWORD=poll_password
REDIS_HOST=redis
```

## Docker Commands

### Development
```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f app

# Execute commands in container
docker-compose exec app php artisan migrate
docker-compose exec app composer install
docker-compose exec app bash

# Rebuild containers
docker-compose up -d --build
```

### Production
```bash
# Start services
docker-compose -f docker-compose.prod.yml up -d

# Stop services
docker-compose -f docker-compose.prod.yml down

# View logs
docker-compose -f docker-compose.prod.yml logs -f app

# Execute commands
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

## File Structure

```
docker/
├── nginx.conf          # Nginx configuration
├── supervisord.conf    # Process management
├── php.ini            # PHP configuration
└── mysql/
    └── init.sql       # Database initialization

Dockerfile              # Main application container
docker-compose.yml      # Development services
docker-compose.prod.yml # Production services
.dockerignore          # Build optimization
```

## Customization

### Changing Ports
Edit the `docker-compose.yml` file:
```yaml
ports:
  - "8080:80"  # Change 8080 to your preferred port
```

### Adding Environment Variables
```yaml
environment:
  - CUSTOM_VAR=value
  - ANOTHER_VAR=value
```

### Modifying PHP Configuration
Edit `docker/php.ini` and rebuild:
```bash
docker-compose up -d --build
```

## Troubleshooting

### Common Issues

1. **Port Already in Use**
   ```bash
   # Check what's using the port
   sudo netstat -tulpn | grep :8000
   
   # Kill the process or change port in docker-compose.yml
   ```

2. **Permission Issues**
   ```bash
   # Fix storage permissions
   docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
   ```

3. **Database Connection Issues**
   ```bash
   # Check if MySQL is running
   docker-compose ps mysql
   
   # Check MySQL logs
   docker-compose logs mysql
   ```

4. **Container Won't Start**
   ```bash
   # Check container logs
   docker-compose logs app
   
   # Rebuild container
   docker-compose up -d --build
   ```

### Logs
```bash
# View all logs
docker-compose logs

# View specific service logs
docker-compose logs app
docker-compose logs mysql
docker-compose logs redis

# Follow logs in real-time
docker-compose logs -f app
```

### Database Management
```bash
# Access MySQL CLI
docker-compose exec mysql mysql -u root -p

# Backup database
docker-compose exec mysql mysqldump -u root -p poll_management > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u root -p poll_management < backup.sql
```

## Performance Optimization

### Development
- Use volume mounts for live code changes
- Enable Xdebug for debugging
- Use Redis for caching

### Production
- Enable OpCache
- Use Redis for sessions and cache
- Implement proper logging
- Use CDN for static assets

## Security Considerations

1. **Change default passwords** in production
2. **Use environment variables** for sensitive data
3. **Enable HTTPS** in production
4. **Regular security updates** for base images
5. **Network isolation** between services

## Monitoring

### Health Checks
```bash
# Check service status
docker-compose ps

# Monitor resource usage
docker stats
```

### Log Rotation
Configure log rotation in your host system or use Docker's built-in log drivers.

## Backup Strategy

### Database
```bash
# Create backup script
#!/bin/bash
docker-compose exec mysql mysqldump -u root -p poll_management > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Application Files
```bash
# Backup storage and uploads
tar -czf storage_backup_$(date +%Y%m%d_%H%M%S).tar.gz storage/
```

## Support

For issues related to:
- **Docker**: Check Docker documentation and logs
- **Laravel**: Check Laravel documentation and logs
- **Application**: Check application logs in `storage/logs/`
