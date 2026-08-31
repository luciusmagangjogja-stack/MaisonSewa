@echo off
cd /d "%~dp0"
if not exist node_modules npm install
node broadcast.js
pause
