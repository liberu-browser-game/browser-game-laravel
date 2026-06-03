#!/bin/bash
# Setup script for the Liberu Browser Game project.
#
# Provides installation options for Standalone, Docker, or Kubernetes deployments.
# Handles composer/npm with fallback logic, socialstream migrations, and Filament setup.
#
# Usage:
#   ./setup.sh [--standalone|--docker|--k8s|--help]
#   ./setup.sh --standalone --ci   (non-interactive, requires .env pre-configured)

set -e

RED='\e[91m'
GREEN='\e[92m'
YELLOW='\e[93m'
BLUE='\e[94m'
RESET='\e[39m'

CI_MODE=false

print_message() { echo -e "${1}${2}${RESET}"; }
print_header()  { echo ""; echo "=================================="; echo "$1"; echo "=================================="; echo ""; }
print_error()   { print_message "$RED"    "ERROR: $1"; }
print_success() { print_message "$GREEN"  "SUCCESS: $1"; }
print_info()    { print_message "$BLUE"   "INFO: $1"; }
print_warning() { print_message "$YELLOW" "WARNING: $1"; }

command_exists() { command -v "$1" >/dev/null 2>&1; }

# ---------------------------------------------------------------------------
# Help
# ---------------------------------------------------------------------------
show_help() {
    echo ""
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --standalone    Run standalone installation (default interactive)"
    echo "  --docker        Run Docker-based installation"
    echo "  --k8s           Run Kubernetes installation"
    echo "  --ci            Non-interactive mode (standalone only; requires .env)"
    echo "  --help          Show this help message"
    echo ""
    echo "Examples:"
    echo "  ./setup.sh                     Interactive menu"
    echo "  ./setup.sh --standalone        Jump straight to standalone install"
    echo "  ./setup.sh --standalone --ci   CI/CD non-interactive install"
    echo "  ./setup.sh --k8s               Kubernetes deployment"
    echo ""
    exit 0
}

# ---------------------------------------------------------------------------
# PHP extension checks
# ---------------------------------------------------------------------------
check_php_extensions() {
    local required_exts=("pdo" "pdo_mysql" "mbstring" "openssl" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo" "curl")
    local missing=()

    for ext in "${required_exts[@]}"; do
        php -r "extension_loaded('$ext') or exit(1);" 2>/dev/null || missing+=("$ext")
    done

    if [ ${#missing[@]} -gt 0 ]; then
        print_error "Missing required PHP extensions: ${missing[*]}"
        print_info "Install with: sudo dnf install php-${missing[*]// / php-}   (RHEL/AlmaLinux)"
        print_info "              sudo apt install php-${missing[*]// / php-}   (Debian/Ubuntu)"
        return 1
    fi

    print_success "All required PHP extensions present"
    return 0
}

# ---------------------------------------------------------------------------
# PHP version check
# ---------------------------------------------------------------------------
check_php_version() {
    local required="8.5"
    local current
    current=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')

    if php -r "exit(version_compare('$current', '$required', '>=') ? 0 : 1);"; then
        print_success "PHP $current meets minimum requirement ($required+)"
    else
        print_error "PHP $current is below the required version $required."
        exit 1
    fi
}

# ---------------------------------------------------------------------------
# Node.js version check
# ---------------------------------------------------------------------------
check_node_version() {
    local required="18"
    if ! command_exists node; then
        print_warning "Node.js not found — frontend assets cannot be built."
        return 1
    fi

    local current
    current=$(node -e 'process.stdout.write(process.versions.node.split(".")[0])')

    if [ "$current" -ge "$required" ] 2>/dev/null; then
        print_success "Node.js $current meets minimum requirement ($required+)"
        return 0
    else
        print_warning "Node.js $current is below recommended version $required — upgrade if build fails."
        return 0
    fi
}

# ---------------------------------------------------------------------------
# Composer
# ---------------------------------------------------------------------------
ensure_composer() {
    if command_exists composer; then
        COMPOSER_CMD="composer"
        return 0
    fi

    print_warning "Composer not found. Downloading composer.phar..."
    command_exists curl  || { print_error "curl is required to download Composer."; return 1; }
    command_exists php   || { print_error "PHP is required."; return 1; }

    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --quiet
    php -r "unlink('composer-setup.php');"

    if [ -f "composer.phar" ]; then
        COMPOSER_CMD="php composer.phar"
        return 0
    fi

    print_error "Failed to download composer.phar"
    return 1
}

install_composer_dependencies() {
    print_header "COMPOSER INSTALL"

    if [ -d "vendor" ] && [ -f "vendor/autoload.php" ]; then
        if [ "$CI_MODE" = false ]; then
            print_info "Vendor directory exists."
            read -p "Reinstall composer dependencies? (y/n) " -n 1 -r; echo
            [[ ! $REPLY =~ ^[Yy]$ ]] && { print_success "Skipping composer install"; return 0; }
        else
            print_info "CI mode: skipping composer install (vendor already present)"
            return 0
        fi
    fi

    ensure_composer || { print_error "Cannot proceed without Composer"; return 1; }

    local flags="--no-interaction --prefer-dist --optimize-autoloader"
    if [ "${APP_ENV:-}" = "production" ]; then
        flags="$flags --no-dev"
    fi

    print_info "Running: $COMPOSER_CMD install $flags"
    eval "$COMPOSER_CMD install $flags" \
        && print_success "Composer dependencies installed" \
        || { print_error "Composer install failed"; return 1; }
}

# ---------------------------------------------------------------------------
# NPM
# ---------------------------------------------------------------------------
install_npm_dependencies() {
    print_header "NPM INSTALL"

    if [ -d "node_modules" ] && [ "$CI_MODE" = false ]; then
        print_info "node_modules exists."
        read -p "Reinstall npm dependencies? (y/n) " -n 1 -r; echo
        [[ ! $REPLY =~ ^[Yy]$ ]] && { print_success "Skipping npm install"; return 0; }
    fi

    command_exists npm || { print_error "npm not installed. Visit: https://nodejs.org/"; return 1; }
    npm install && print_success "NPM dependencies installed" || { print_error "NPM install failed"; return 1; }
}

build_frontend_assets() {
    print_header "NPM BUILD"
    command_exists npm || { print_error "npm not installed."; return 1; }
    npm run build && print_success "Frontend assets built" || { print_error "NPM build failed"; return 1; }
}

# ---------------------------------------------------------------------------
# Post-install artisan steps
# ---------------------------------------------------------------------------
run_post_install() {
    print_header "POST-INSTALL STEPS"

    print_info "Creating storage symlink..."
    php artisan storage:link --quiet 2>/dev/null || true

    print_info "Publishing vendor assets..."
    php artisan vendor:publish --tag=laravel-assets --force --quiet || true

    print_info "Publishing Socialstream assets..."
    php artisan vendor:publish --tag=socialstream-migrations --force --quiet || true
    php artisan vendor:publish --tag=socialstream-config     --force --quiet || true
    php artisan vendor:publish --tag=socialstream-views      --force --quiet || true

    print_info "Publishing Jetstream assets..."
    php artisan vendor:publish --tag=jetstream-views       --force --quiet || true
    php artisan vendor:publish --tag=jetstream-migrations  --force --quiet || true

    print_info "Publishing Horizon assets..."
    php artisan vendor:publish --tag=horizon-assets        --force --quiet || true

    print_info "Publishing Filament Shield config..."
    php artisan vendor:publish --tag=filament-shield-config --force --quiet || true

    print_info "Running Filament upgrade..."
    php artisan filament:upgrade --quiet || true

    print_info "Clearing caches..."
    php artisan optimize:clear --quiet || true

    print_success "Post-install steps completed"
}

# ---------------------------------------------------------------------------
# Standalone installation
# ---------------------------------------------------------------------------
install_standalone() {
    print_header "STANDALONE INSTALLATION"

    echo "=================================="
    echo "===== USER: [$(whoami)]"
    printf "===== [PHP %s]\n" "$(php -r 'echo phpversion();')"
    echo "=================================="
    echo ""

    check_php_version
    check_php_extensions || print_warning "Some PHP extensions may be missing. Proceeding anyway..."
    check_node_version || true

    local copy=false
    if [ ! -f ".env" ]; then
        print_info "No .env found. Copying .env.example to .env..."
        cp .env.example .env
        copy=true
    elif [ "$CI_MODE" = false ]; then
        while true; do
            read -p "An .env already exists. Copy from .env.example? (y/n) " yn
            case $yn in
                [Yy]*) cp .env.example .env; copy=true; break ;;
                [Nn]*) print_success "Using existing .env"; break ;;
                *) print_warning "Please answer yes or no." ;;
            esac
        done
    else
        print_info "CI mode: using existing .env"
    fi

    if [ "$copy" = true ] && [ "$CI_MODE" = false ]; then
        while true; do
            read -p "Have you configured your database credentials in .env? (y/n) " cond
            case $cond in
                [Yy]*) break ;;
                [Nn]*) print_warning "Please configure .env and re-run this script."; exit 0 ;;
                *) print_warning "Please answer yes or no." ;;
            esac
        done
    fi

    install_composer_dependencies || { print_error "Installation failed at composer step"; exit 1; }

    install_npm_dependencies || print_warning "NPM install failed, continuing..."
    build_frontend_assets    || print_warning "NPM build failed, continuing..."

    print_header "GENERATING APPLICATION KEY"
    php artisan key:generate || { print_error "Key generation failed"; exit 1; }

    run_post_install

    print_header "DATABASE MIGRATION"
    if php artisan migrate:fresh; then
        print_success "Database migrated"
    else
        print_error "Migration failed"; exit 1
    fi

    print_header "DATABASE SEED"
    php artisan db:seed && print_success "Database seeded" || print_warning "Seeding failed, continuing..."

    if [ "$CI_MODE" = false ]; then
        print_header "RUNNING TESTS"
        if [ -f "vendor/bin/phpunit" ]; then
            ./vendor/bin/phpunit && print_success "Tests passed" || print_warning "Some tests failed."
        else
            print_warning "PHPUnit not found. Skipping tests."
        fi
    fi

    print_header "FINAL OPTIMIZATION"
    php artisan optimize:clear
    php artisan route:clear

    echo ""
    print_success "=================================="
    print_success "============== DONE =============="
    print_success "=================================="
    echo ""
    print_info "Useful commands:"
    print_info "  php artisan octane:start          Start app server (recommended)"
    print_info "  php artisan horizon               Start queue worker (Horizon)"
    print_info "  php artisan module list           List installed modules"
    print_info "  php artisan filament:shield:generate --all  Generate Filament Shield permissions"
    echo ""

    if [ "$CI_MODE" = true ]; then
        print_info "CI mode: skipping server start prompt"
        return 0
    fi

    while true; do
        read -p "Start the development server? (y/n) " cond
        case $cond in
            [Yy]*) print_success "Starting server..."; php artisan octane:start 2>/dev/null || php artisan serve; break ;;
            [Nn]*) print_success "Run 'php artisan octane:start' to start the server"; exit 0 ;;
            *) print_warning "Please answer yes or no." ;;
        esac
    done
}

# ---------------------------------------------------------------------------
# Docker installation
# ---------------------------------------------------------------------------
install_docker() {
    print_header "DOCKER INSTALLATION"

    command_exists docker || {
        print_error "Docker not installed. Visit: https://docs.docker.com/get-docker/"
        exit 1
    }
    print_success "Docker is installed"

    docker compose version >/dev/null 2>&1 || command_exists docker-compose || {
        print_error "Docker Compose not available. Visit: https://docs.docker.com/compose/install/"
        exit 1
    }
    print_success "Docker Compose available"

    [ ! -f ".env" ] && { print_info "Copying .env.example to .env"; cp .env.example .env; }

    print_info "Edit .env for your Docker environment, then press Enter."
    read -p "Press Enter to continue..."

    print_info "Building and starting Docker containers..."
    if docker compose version >/dev/null 2>&1; then
        docker compose up -d --build
    else
        docker-compose up -d --build
    fi

    print_success "Docker containers started. Application at http://localhost:8000"
}

# ---------------------------------------------------------------------------
# Kubernetes installation
# ---------------------------------------------------------------------------
install_kubernetes() {
    print_header "KUBERNETES INSTALLATION"

    command_exists kubectl || {
        print_error "kubectl not installed. Visit: https://kubernetes.io/docs/tasks/tools/"
        exit 1
    }
    print_success "kubectl is installed"

    local K8S_DIR=""
    for dir in k8s kubernetes; do
        [ -d "$dir" ] && { K8S_DIR="$dir"; break; }
    done

    [ -z "$K8S_DIR" ] && {
        print_error "No k8s/ or kubernetes/ directory found."
        print_info "Create Kubernetes manifests in a k8s/ directory."
        exit 1
    }

    print_info "Using Kubernetes configs from: $K8S_DIR/"

    if [ ! -f ".env" ]; then
        cp .env.example .env
        print_warning "Edit .env with your configuration, then press Enter."
        read -p "Press Enter to continue..."
    fi

    # Check for existing secrets and warn if not present
    local ns="browser-game"
    if kubectl get secret browser-game-secrets -n "$ns" >/dev/null 2>&1; then
        print_success "Secret 'browser-game-secrets' already exists in namespace '$ns'"
    else
        print_warning "Secret 'browser-game-secrets' not found in namespace '$ns'."
        print_info "Create it before deploying:"
        print_info "  kubectl create namespace $ns"
        print_info "  kubectl create secret generic browser-game-secrets \\"
        print_info "    --from-literal=APP_KEY=\$(php artisan key:generate --show) \\"
        print_info "    --from-literal=DB_PASSWORD=your_db_password \\"
        print_info "    --from-literal=REDIS_PASSWORD=your_redis_password \\"
        print_info "    -n $ns"
        echo ""
        if [ "$CI_MODE" = false ]; then
            read -p "Continue without confirming secrets exist? (y/n) " yn
            [[ ! $yn =~ ^[Yy]$ ]] && { print_info "Aborting. Create secrets and re-run."; exit 0; }
        fi
    fi

    if [ -f "$K8S_DIR/base/kustomization.yaml" ]; then
        print_info "Kustomize detected. Select overlay:"
        echo "  1) Development"
        echo "  2) Production"
        echo "  3) Base only"
        read -p "Enter choice (1-3): " overlay_choice
        case $overlay_choice in
            1) OVERLAY="$K8S_DIR/overlays/development" ;;
            2) OVERLAY="$K8S_DIR/overlays/production" ;;
            *) OVERLAY="$K8S_DIR/base" ;;
        esac

        print_info "Verifying kustomize build for: $OVERLAY"
        if command_exists kustomize; then
            kustomize build "$OVERLAY" > /dev/null \
                && print_success "Kustomize build OK" \
                || { print_error "Kustomize build failed. Check your YAML."; exit 1; }
        elif kubectl kustomize "$OVERLAY" > /dev/null 2>&1; then
            print_success "kubectl kustomize build OK"
        else
            print_warning "Could not verify kustomize build — proceeding anyway."
        fi

        print_info "Applying: $OVERLAY"
        if kubectl apply -k "$OVERLAY"; then
            print_success "Kubernetes resources created"
            print_info ""
            print_info "Check status:   kubectl get pods -n $ns"
            print_info "Watch pods:     kubectl get pods -n $ns -w"
            print_info "View logs:      kubectl logs -l app=browser-game -n $ns"
            print_info ""
            print_info "Health endpoints:"
            print_info "  /health/startup  — app boot readiness"
            print_info "  /health/live     — liveness"
            print_info "  /health/ready    — readiness (includes DB check)"
        else
            print_error "kubectl apply failed"; exit 1
        fi
    else
        print_info "Applying: $K8S_DIR/"
        kubectl apply -f "$K8S_DIR/" \
            && print_success "Kubernetes resources created" \
            || { print_error "kubectl apply failed"; exit 1; }
    fi
}

# ---------------------------------------------------------------------------
# Main menu / argument parsing
# ---------------------------------------------------------------------------
main() {
    # Parse flags
    local mode=""
    for arg in "$@"; do
        case $arg in
            --help|-h) show_help ;;
            --standalone) mode="standalone" ;;
            --docker)     mode="docker" ;;
            --k8s)        mode="k8s" ;;
            --ci)         CI_MODE=true ;;
        esac
    done

    case "$mode" in
        standalone) install_standalone; return ;;
        docker)     install_docker;     return ;;
        k8s)        install_kubernetes; return ;;
    esac

    # Interactive menu
    clear
    print_header "LIBERU BROWSER GAME - INSTALLER"

    echo "Select installation type:"
    echo ""
    echo "  1) Standalone  (local development / production)"
    echo "  2) Docker      (containerised deployment)"
    echo "  3) Kubernetes  (K8s cluster deployment)"
    echo "  4) Exit"
    echo ""
    echo "Tip: run '$0 --help' for non-interactive usage."
    echo ""

    while true; do
        read -p "Enter choice (1-4): " choice
        case $choice in
            1) install_standalone; break ;;
            2) install_docker;     break ;;
            3) install_kubernetes; break ;;
            4) print_info "Installation cancelled"; exit 0 ;;
            *) print_warning "Invalid choice. Enter 1, 2, 3, or 4." ;;
        esac
    done
}

main "$@"
