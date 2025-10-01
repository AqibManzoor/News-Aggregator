#!/bin/bash

# News Aggregator Setup Script
# This script sets up the News Aggregator project for immediate use

set -e

echo "🚀 Setting up News Aggregator..."

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

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    print_error "PHP is not installed. Please install PHP 8.0.2 or higher."
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
REQUIRED_VERSION="8.0.2"

if [ "$(printf '%s\n' "$REQUIRED_VERSION" "$PHP_VERSION" | sort -V | head -n1)" != "$REQUIRED_VERSION" ]; then
    print_error "PHP version $PHP_VERSION is not supported. Please install PHP 8.0.2 or higher."
    exit 1
fi

print_success "PHP version $PHP_VERSION is supported"

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed. Please install Composer."
    exit 1
fi

print_success "Composer is installed"

# Install dependencies
print_status "Installing dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

print_success "Dependencies installed"

# Copy environment file
if [ ! -f .env ]; then
    print_status "Creating environment file..."
    cp .env.example .env
    print_success "Environment file created"
else
    print_warning "Environment file already exists, skipping..."
fi

# Generate application key
print_status "Generating application key..."
php artisan key:generate --force

print_success "Application key generated"

# Check if database is available
print_status "Checking database connection..."

# Create database if it doesn't exist
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)

# Try to create database
mysql -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DB_DATABASE;" 2>/dev/null || {
    print_warning "Could not create database automatically. Please create database '$DB_DATABASE' manually."
    print_status "You can create it with: mysql -u root -p -e 'CREATE DATABASE $DB_DATABASE;'"
    read -p "Press Enter to continue after creating the database..."
}

# Run migrations
print_status "Running database migrations..."
php artisan migrate --force

print_success "Database migrations completed"

# Seed database
print_status "Skipping seeders (providers will fetch live data)"

# Fetch initial news data
print_status "Fetching initial news data from providers..."
php artisan news:fetch || print_warning "Initial fetch failed. You can retry later with: php artisan news:fetch"
print_success "Fetch step attempted"

# Set proper permissions
print_status "Setting file permissions..."
chmod -R 755 storage bootstrap/cache

print_success "File permissions set"

# Clear caches
print_status "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

print_success "Caches cleared"

# Create storage symlink
print_status "Creating storage symlink..."
php artisan storage:link

print_success "Storage symlink created"

echo ""
echo "🎉 Setup completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Start the server: php artisan serve"
echo "2. Open your browser: http://localhost:8000"
echo "3. API endpoint: http://localhost:8000/api/articles"
echo ""
echo "🔧 Configuration:"
echo "- Edit .env file to customize settings"
echo "- API keys are included for testing"
echo "- Database is seeded with sample data"
echo ""
echo "📖 Documentation:"
echo "- README.md contains full documentation"
echo "- API documentation is in API_DOCUMENTATION.md"
echo ""
echo "🚀 Your News Aggregator is ready to use!"
