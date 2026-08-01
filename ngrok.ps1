# =====================================================================
#  Túnel público con ngrok para Aerolínea El Trompillo
# =====================================================================
#  Expone el sistema (nginx en el puerto 8080 de Docker) a Internet
#  para poder mostrarlo al docente / probarlo desde el celular.
#
#  Uso:
#    .\ngrok.ps1                 -> usa el dominio estático fijo
#    .\ngrok.ps1 -Random         -> usa una URL aleatoria de ngrok
#    .\ngrok.ps1 -Domain "mi-dominio.ngrok-free.dev"
#
#  Nota: la cuenta gratuita de ngrok permite UN solo dominio estático
#  y UN túnel a la vez. Si tenés el túnel de "granja" corriendo, cerralo
#  antes (Ctrl+C) porque comparten el mismo dominio.
# =====================================================================

param(
    [string]$Domain = "obedient-poplar-posted.ngrok-free.dev",
    [int]$Port = 8080,
    [switch]$Random
)

$ErrorActionPreference = "Stop"

# --- Localizar ngrok.exe -------------------------------------------------
$ngrok = $null
$candidatos = @(
    "ngrok",                                   # en el PATH
    (Join-Path $PSScriptRoot "ngrok.exe"),     # dentro de este proyecto
    (Join-Path $PSScriptRoot "..\granja\ngrok.exe")  # el que ya usábamos en granja
)
foreach ($c in $candidatos) {
    $cmd = Get-Command $c -ErrorAction SilentlyContinue
    if ($cmd) { $ngrok = $cmd.Source; break }
    if (Test-Path $c) { $ngrok = (Resolve-Path $c).Path; break }
}
if (-not $ngrok) {
    Write-Host "[ERROR] No se encontró ngrok.exe." -ForegroundColor Red
    Write-Host "        Descargalo de https://ngrok.com/download o copialo a esta carpeta." -ForegroundColor Yellow
    exit 1
}

# --- Verificar que Docker/nginx esté escuchando en el puerto -------------
$enUso = Test-NetConnection -ComputerName "localhost" -Port $Port -WarningAction SilentlyContinue
if (-not $enUso.TcpTestSucceeded) {
    Write-Host "[AVISO] Nada responde en http://localhost:$Port" -ForegroundColor Yellow
    Write-Host "        ¿Levantaste los contenedores?  ->  docker compose up -d" -ForegroundColor Yellow
    Write-Host ""
}

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  Aerolínea El Trompillo  ·  túnel ngrok" -ForegroundColor Cyan
Write-Host "  Local : http://localhost:$Port" -ForegroundColor Cyan
if ($Random) {
    Write-Host "  Público: URL aleatoria (se muestra abajo)" -ForegroundColor Cyan
    Write-Host "==========================================================" -ForegroundColor Cyan
    & $ngrok http $Port
} else {
    Write-Host "  Público: https://$Domain" -ForegroundColor Green
    Write-Host "==========================================================" -ForegroundColor Cyan
    & $ngrok http $Port --url="https://$Domain"
}
