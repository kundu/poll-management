#!/bin/bash

# Docker management script for Poptin Poll Management System

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if Docker is running
check_docker() {
    if ! docker info > /dev/null 2>&1; then
        print_error "Docker is not running. Please start Docker and try again."
        exit 1
    fi
}

# Function to check if Docker Compose is available
check_docker_compose() {
    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose is not installed. Please install Docker Compose and try again."
        exit 1
    fi
}

# Function to start development environment
start_dev() {
    print_status "Starting development environment..."
    docker-compose up -d --build
    print_success "Development environment started successfully!"
    print_status "Access your application at: http://localhost:8000"
    print_status "Access PHPMyAdmin at: http://localhost:8080"
}

# Function to start production environment
start_prod() {
    print_status "Starting production environment..."
    docker-compose -f docker-compose.prod.yml up -d --build
    print_success "Production environment started successfully!"
    print_status "Access your application at: http://localhost"
}

# Function to stop services
stop_services() {
    print_status "Stopping services..."
    docker-compose down
    docker-compose -f docker-compose.prod.yml down 2>/dev/null || true
    print_success "Services stopped successfully!"
}

# Function to restart services
restart_services() {
    print_status "Restarting services..."
    stop_services
    start_dev
}

# Function to view logs
view_logs() {
    local service=${1:-app}
    print_status "Viewing logs for service: $service"
    docker-compose logs -f "$service"
}

# Function to execute command in container
exec_command() {
    local service=${1:-app}
    local command=${2:-bash}
    print_status "Executing command in $service container: $command"
    docker-compose exec "$service" "$command"
}

# Function to run Laravel artisan commands
artisan() {
    local command=$1
    print_status "Running Laravel artisan command: $command"
    docker-compose exec app php artisan "$command"
}

# Function to install dependencies
install_deps() {
    print_status "Installing PHP dependencies..."
    docker-compose exec app composer install
    print_success "Dependencies installed successfully!"
}

# Function to run migrations
run_migrations() {
    print_status "Running database migrations..."
    docker-compose exec app php artisan migrate
    print_success "Migrations completed successfully!"
}

# Function to seed database
seed_database() {
    print_status "Seeding database..."
    docker-compose exec app php artisan db:seed
    print_success "Database seeded successfully!"
}

# Function to clear caches
clear_caches() {
    print_status "Clearing Laravel caches..."
    docker-compose exec app php artisan config:clear
    docker-compose exec app php artisan route:clear
    docker-compose exec app php artisan view:clear
    docker-compose exec app php artisan cache:clear
    print_success "Caches cleared successfully!"
}

# Function to show status
show_status() {
    print_status "Checking service status..."
    docker-compose ps
    echo ""
    print_status "Resource usage:"
    docker stats --no-stream
}

# Function to backup database
backup_db() {
    local filename="backup_$(date +%Y%m%d_%H%M%S).sql"
    print_status "Creating database backup: $filename"
    docker-compose exec mysql mysqldump -u root -ppassword poll_management > "$filename"
    print_success "Database backed up to: $filename"
}

# Function to restore database
restore_db() {
    local filename=$1
    if [ -z "$filename" ]; then
        print_error "Please specify a backup file to restore"
        echo "Usage: $0 restore <filename>"
        exit 1
    fi
    if [ ! -f "$filename" ]; then
        print_error "Backup file not found: $filename"
        exit 1
    fi
    print_status "Restoring database from: $filename"
    docker-compose exec -T mysql mysql -u root -ppassword poll_management < "$filename"
    print_success "Database restored successfully!"
}

# Function to view Reverb WebSocket logs
reverb_logs() {
    print_status "Viewing Reverb WebSocket logs..."
    docker-compose logs -f reverb
}

# Function to check Reverb WebSocket status
reverb_status() {
    print_status "Checking Reverb WebSocket status..."
    print_status "Service status:"
    docker-compose ps reverb
    echo ""
    print_status "Testing WebSocket connection..."
    if command -v curl &> /dev/null; then
        local response=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/health || echo "000")
        if [ "$response" = "200" ]; then
            print_success "Reverb WebSocket is running and healthy!"
        else
            print_warning "Reverb WebSocket returned status code: $response"
        fi
    else
        print_warning "curl not available, cannot test WebSocket health"
    fi
}

# Function to show help
show_help() {
    echo "Docker Management Script for Poptin Poll Management System"
    echo ""
    echo "Usage: $0 [COMMAND] [OPTIONS]"
    echo ""
    echo "Commands:"
    echo "  start-dev      Start development environment"
    echo "  start-prod     Start production environment"
    echo "  stop           Stop all services"
    echo "  restart        Restart development environment"
    echo "  logs [SERVICE] View logs (default: app)"
    echo "  exec [SERVICE] [COMMAND] Execute command in container (default: app bash)"
    echo "  artisan [CMD]  Run Laravel artisan command"
    echo "  install        Install PHP dependencies"
    echo "  migrate        Run database migrations"
    echo "  seed           Seed database"
    echo "  clear          Clear Laravel caches"
    echo "  status         Show service status and resource usage"
    echo "  backup         Create database backup"
    echo "  restore <FILE> Restore database from backup file"
    echo "  reverb-logs    View Reverb WebSocket logs"
    echo "  reverb-status  Check Reverb WebSocket status"
    echo "  help           Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 start-dev"
    echo "  $0 logs mysql"
    echo "  $0 exec app composer install"
    echo "  $0 artisan migrate"
    echo "  $0 backup"
    echo "  $0 restore backup_20231201_120000.sql"
    echo "  $0 reverb-logs"
}

# Main script logic
main() {
    # Check prerequisites
    check_docker
    check_docker_compose

    case "${1:-help}" in
        start-dev)
            start_dev
            ;;
        start-prod)
            start_prod
            ;;
        stop)
            stop_services
            ;;
        restart)
            restart_services
            ;;
        logs)
            view_logs "$2"
            ;;
        exec)
            exec_command "$2" "${3:-bash}"
            ;;
        artisan)
            if [ -z "$2" ]; then
                print_error "Please specify an artisan command"
                exit 1
            fi
            artisan "$2"
            ;;
        install)
            install_deps
            ;;
        migrate)
            run_migrations
            ;;
        seed)
            seed_database
            ;;
        clear)
            clear_caches
            ;;
        status)
            show_status
            ;;
        backup)
            backup_db
            ;;
        restore)
            restore_db "$2"
            ;;
        reverb-logs)
            reverb_logs
            ;;
        reverb-status)
            reverb_status
            ;;
        help|--help|-h)
            show_help
            ;;
        *)
            print_error "Unknown command: $1"
            show_help
            exit 1
            ;;
    esac
}

# Run main function with all arguments
main "$@"
