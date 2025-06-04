# Personal Portfolio Website

A modern PHP-based portfolio website with a custom MVC architecture and admin dashboard.

## Overview

This portfolio website showcases projects and provides a contact form for visitors. It includes a secure admin dashboard for managing projects and contact messages.

## Features

- **Responsive Design**: Mobile-friendly interface
- **Project Showcase**: Display your work with images and descriptions
- **Contact Form**: Allow visitors to send messages
- **Admin Dashboard**: Secure area to manage content
  - Message management with read/unread status
  - Project management
- **Security**:
  - CSRF protection
  - Rate limiting for form submissions
  - Secure authentication

## Technical Details

This project is built with:

- PHP 8.1+
- Custom MVC architecture (no framework)
- SQLite database
- Composer for autoloading

### Directory Structure

```
portfolio/
├── public/           # Web root
│   ├── index.php     # Entry point
│   └── css/          # Stylesheets
├── src/              # Application code
│   ├── Controllers/  # Request handlers
│   ├── Models/       # Data models
│   ├── Repositories/ # Database interaction
│   ├── Http/         # Request/Response
│   ├── Security/     # Security features
│   ├── Router.php    # URL routing
│   └── Template.php  # View rendering
├── views/            # Templates
└── vendor/           # Dependencies
```

## Getting Started

### Prerequisites

- [Laravel Herd](https://herd.laravel.com/) (Works on both Windows and Mac)
- Git for cloning the repository

### Installation

1. Install Laravel Herd on your system if you haven't already
2. Fork this repository on GitHub
3. Clone your fork to your local machine:
   ```
   git clone https://github.com/YOUR-USERNAME/2sem-codetrack-01-portfolio.git portfolio
   cd portfolio
   ```
4. Install dependencies:
   ```
   composer install
   ```
5. Create a `.env` file from the example:
   ```
   cp .env.example .env
   ```
   Then edit it with your preferred settings

6. Create and initialize the SQLite database:
   ```
   touch database.sqlite
   php bin/migrate.php
   ```
   
7. Start the site using Laravel Herd
   - Create a new site in Herd pointing to the `public` folder
   - Or use Herd's CLI: `herd link ./public portfolio.test`

## Key Components

### Router

The custom router handles dynamic URL segments like `/admin/messages/{id}/toggle-read` and maps them to controller methods.

### Controllers

Controllers handle incoming requests and return appropriate responses. The admin controllers implement authentication checks.

### Repositories

Repository classes provide a clean interface to database operations, abstracting the underlying storage mechanism.

### Templates

A simple templating engine allows views to be composed with layouts and partial includes.

## Admin Features

- Dashboard with message statistics
- Message management (mark as read/unread, delete)
- Visual indicators for unread messages

## Extending The Project

The following guides provide detailed instructions for extending the portfolio project:

### Documentation

For detailed information about working with this project, check out these guides:

- [**Creating New Pages**](docs/creating-new-pages.md) - Complete guide to adding new pages
- [**Working with Database and Repositories**](docs/database-repositories.md) - Learn how to work with data
- [**Using the Template System**](docs/template-system.md) - Master the view templating system  
- [**CSS and Styling Guide**](docs/css-styling.md) - Organization and usage of CSS

### Quick Reference

#### Adding a New Page

1. Add a route in `public/index.php`
2. Create a controller method
3. Create a view file

[See detailed guide →](docs/creating-new-pages.md)

#### Working with the Database

1. Create migration files for schema changes
2. Create or update model classes
3. Create repository classes for database operations

[See detailed guide →](docs/database-repositories.md)

#### Styling Your Pages

1. Understand the CSS organization
2. Use CSS variables for consistent styling
3. Create page-specific CSS files as needed

[See detailed guide →](docs/css-styling.md)
