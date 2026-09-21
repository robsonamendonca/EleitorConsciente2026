@echo off
cd /d "%~dp0"
if "%1"=="clean" (
    echo Stopping and CLEANING PHP + MySQL environment...
    docker compose down -v
) else (
    echo Stopping PHP + MySQL environment...
    docker compose down
)
echo Done.
pause