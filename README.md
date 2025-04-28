<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<h1 align="center">Laravel Auth with OTP Verification</h1>

<p align="center">
  A secure and modern authentication system featuring OTP password reset, built with Laravel and Docker
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#installation">Installation</a> •
  <a href="#usage">Usage</a> •
  <a href="#tech-stack">Tech Stack</a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

---

## ✨ Features

- **User Registration** with email verification
- **Secure Login** with session management
- **OTP-based Password Reset** flow
- **Responsive Design** works on all devices
- **Dockerized** for easy development and deployment
- **Laravel Sail** for local development
- **Blade Templates** with clean UI

---

## 🚀 Installation

Get started in just a few simple steps:

```bash
# Clone the repository
git clone https://github.com/yourusername/laravel-auth-otp.git

# Navigate to project directory
cd laravel-auth-otp

# Install PHP dependencies
composer install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Start the application (using Laravel Sail)
./vendor/bin/sail up

# Or if you have sail alias configured
sail up

# Alternative: Use standard Docker commands
docker-compose up -d