# Raccourci : .\scripts\artisan.ps1 migrate --seed
Set-Location (Split-Path -Parent $PSScriptRoot)
& "$PSScriptRoot\php.ps1" artisan @args
