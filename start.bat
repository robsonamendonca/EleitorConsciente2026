@echo off
cd /d "%~dp0"
echo Starting PHP + MySQL environment...
docker compose up -d
echo Environment is up! PHP: 8080, MySQL: 3306.
pause