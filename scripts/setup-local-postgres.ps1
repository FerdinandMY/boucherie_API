# Configuration locale PostgreSQL + migrations Laravel
# Usage (depuis la racine API) :
#   .\scripts\setup-local-postgres.ps1
#   .\scripts\setup-local-postgres.ps1 -Password "votre_mdp" -Seed

param(
    [string]$Password = "",
    [string]$Database = "butcher_db",
    [string]$DbHost = "127.0.0.1",
    [int]$Port = 5432,
    [string]$Username = "postgres",
    [switch]$Seed,
    [switch]$Fresh
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
Set-Location $Root

function Find-Executable([string]$Name) {
    $cmd = Get-Command $Name -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Source }
    return $null
}

$php = Find-Executable "php"
if (-not $php) {
    Write-Error @"
PHP introuvable dans le PATH.
Installez PHP 8.x (+ extension pdo_pgsql), ajoutez-le au PATH, puis relancez ce script.
"@
}

$psql = Find-Executable "psql"
if ($psql) {
    $env:PGPASSWORD = $Password
    $exists = & $psql -h $DbHost -p $Port -U $Username -d postgres -tAc "SELECT 1 FROM pg_database WHERE datname='$Database'" 2>$null
    if ($exists -ne "1") {
        Write-Host "Création de la base $Database..."
        & $psql -h $DbHost -p $Port -U $Username -d postgres -c "CREATE DATABASE $Database ENCODING 'UTF8';"
    } else {
        Write-Host "Base $Database déjà présente."
    }
} else {
    Write-Warning "psql non trouvé : créez la base '$Database' manuellement si besoin."
}

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    & $php artisan key:generate --force
}

if ($Password) {
    (Get-Content ".env") -replace '^DB_PASSWORD=.*', "DB_PASSWORD=$Password" | Set-Content ".env" -Encoding UTF8
}

if (-not (Test-Path "vendor\autoload.php")) {
    $composer = Find-Executable "composer"
    if (-not $composer) {
        Write-Error "Composer introuvable. Exécutez : composer install"
    }
    Write-Host "composer install..."
    & $composer install --no-interaction
}

Write-Host "Migrations..."
if ($Fresh) {
    if ($Seed) {
        & $php artisan migrate:fresh --seed --force
    } else {
        & $php artisan migrate:fresh --force
    }
} elseif ($Seed) {
    & $php artisan migrate --seed --force
} else {
    & $php artisan migrate --force
}

& $php artisan migrate:status
Write-Host "Terminé. Lancez l'API : php artisan serve"
