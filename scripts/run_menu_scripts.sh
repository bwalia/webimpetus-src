#!/bin/bash
# Menu Management Script Runner
# Runs PHP scripts directly from host machine
# Connects to database via exposed port 3309

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/SQLs"

echo "=================================="
echo "Menu Management Script Runner"
echo "=================================="
echo ""

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP is not installed on this system"
    echo "Please install PHP to run these scripts"
    echo ""
    echo "On Ubuntu/Debian: sudo apt install php-cli php-mysqli"
    echo "On RHEL/CentOS: sudo yum install php-cli php-mysqlnd"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
echo "✓ PHP version: $PHP_VERSION"

# Check if mysqli extension is available
if ! php -m | grep -q mysqli; then
    echo "❌ Error: PHP mysqli extension is not installed"
    echo "Please install php-mysqli extension"
    echo ""
    echo "On Ubuntu/Debian: sudo apt install php-mysqli"
    echo "On RHEL/CentOS: sudo yum install php-mysqlnd"
    exit 1
fi

echo "✓ PHP mysqli extension: installed"

# Check if database port is accessible
if ! command -v nc &> /dev/null; then
    echo "⚠ Warning: 'nc' command not found, skipping port check"
elif ! nc -z 127.0.0.1 3309 2>/dev/null; then
    echo "⚠ Warning: Cannot connect to database on localhost:3309"
    echo "Make sure the webimpetus-db container is running and port 3309 is exposed"
    echo "Run: docker ps | grep webimpetus-db"
    echo ""
else
    echo "✓ Database port 3309: accessible"
fi

echo ""

# Function to run PHP script directly
run_php_script() {
    local script_name=$1
    local script_path="$SCRIPT_DIR/$script_name"

    echo "Running: $script_name"
    echo "=================================="
    echo ""

    # Check if script exists
    if [ -f "$script_path" ]; then
        php "$script_path"
        local exit_code=$?
        if [ $exit_code -ne 0 ]; then
            echo ""
            echo "❌ Script failed with exit code: $exit_code"
            exit $exit_code
        fi
    else
        echo "❌ Error: Script not found: $script_path"
        exit 1
    fi
}

# Menu
if [ $# -eq 0 ]; then
    echo "Usage: $0 [command]"
    echo ""
    echo "Available commands:"
    echo "  check          - Check missing routes (dry run - no changes)"
    echo "  add            - Add missing routes to menu table"
    echo "  grant          - Grant all menu permissions to admin"
    echo "  all            - Run check, add, and grant in sequence"
    echo ""
    echo "Examples:"
    echo "  $0 check       # Safe - just shows what's missing"
    echo "  $0 add         # Adds routes and updates permissions"
    echo "  $0 all         # Interactive - prompts before making changes"
    echo ""
    echo "Scripts connect to database via: localhost:3309"
    echo "Database: myworkstation_dev"
    echo "User: wsl_dev"
    echo ""
    exit 0
fi

COMMAND=$1

case $COMMAND in
    check)
        run_php_script "check_missing_routes.php"
        ;;
    add)
        run_php_script "add_missing_routes_to_menu.php"
        ;;
    grant)
        run_php_script "grant_all_menu_permissions_to_admin.php"
        ;;
    all)
        echo "Step 1: Checking missing routes..."
        echo ""
        run_php_script "check_missing_routes.php"
        echo ""
        echo "=================================="
        echo "Press Enter to continue with adding routes, or Ctrl+C to cancel..."
        read
        echo ""
        echo "Step 2: Adding missing routes..."
        echo ""
        run_php_script "add_missing_routes_to_menu.php"
        echo ""
        echo "Step 3: Granting permissions to admin..."
        echo ""
        run_php_script "grant_all_menu_permissions_to_admin.php"
        echo ""
        echo "=================================="
        echo "✓ All steps completed!"
        ;;
    *)
        echo "❌ Error: Unknown command '$COMMAND'"
        echo "Run '$0' without arguments to see available commands"
        exit 1
        ;;
esac

echo ""
echo "=================================="
echo "✓ Done"
echo "=================================="
