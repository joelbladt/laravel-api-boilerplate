<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/joelbladt/laravel-api-boilerplate/actions"><img src="https://github.com/joelbladt/laravel-api-boilerplate/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://github.com/joelbladt/laravel-api-boilerplate/blob/main"><img src="https://img.shields.io/coverallsCoverage/github/joelbladt/laravel-api-boilerplate" alt="Coveralls"></a>
<a href="https://github.com/joelbladt/laravel-api-boilerplate/blob/main/LICENSE.md"><img src="https://img.shields.io/github/license/joelbladt/laravel-api-boilerplate" alt="License"></a>

</p>

## About Laravel API Boilerplate

This project is a boilerplate setup for building an API with Laravel. It includes various packages and configurations designed to accelerate and standardize development.

## Requirements

- PHP: Version >= 8.2
- Composer: For managing PHP dependencies
- Laravel: 11.x

## Installation Guide

**1. Clone the repository:**
```bash
git clone https://github.com/joelbladt/laravel-api-boilerplate.git
cd ./laravel-api-boilerplate
```

**2. Install dependencies:**
```bash
composer install
```

**3. Setting Up Git Hooks:** To set up Git hooks for this project, follow these steps:

**3.1. Copy Hooks:** Copy all custom hooks from the .hooks directory to the .git/hooks directory by running:
```bash
cp -r .hooks/* .git/hooks/
```

**3.2. Make Hooks Executable:** Ensure all hook scripts are executable:
```bash
chmod +x .git/hooks/*
```
That's it! Your Git hooks are now set up and will run automatically on relevant Git actions (like commits or pushes).

**4. Configure environment file:** Create a .env file in the root directory based on .env.example, then adjust environment variables (database, API keys, etc.).

**5. Generate application key:**
```bash
php artisan key:generate
```

**6. Run migrations and seeders (if needed):**
```bash
php artisan migrate --seed
```

## Installed Packages

This project uses additional Laravel packages to enhance the API functionality. Here’s a list of the key packages and how to configure them.

### 1. darkaonline/l5-swagger
**Description:** `l5-swagger` provides an automatically generated Swagger UI, which offers developers a documented overview of all API endpoints. This is especially useful for frontend developers to better understand and test available endpoints and their requirements.

**Installation:** The package is already installed, but you may need to publish the configurations to customize the documentation.
```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

**Accessing Swagger UI:** Once installed and configured, the documentation is typically accessible at the following URL: [http://localhost/](http://localhost/)

**Configuration:**

- In `config/l5-swagger.php`, you can adjust various settings like API version, documentation title, and security configurations.
- `l5-swagger` integrates with the OpenAPI Specification for auto-generating API documentation.

**Usage:**

- Use the Swagger interface to document and test endpoints.
- Make changes in your controller file comments to reflect them in the Swagger documentation.


## Testing

To ensure all features work as expected, you can run unit tests. By default, tests are located in the tests/Feature and tests/Unit directories.

```bash
php artisan test
```

## API Documentation

Once configured, l5-swagger provides API documentation through Swagger, detailing all routes, parameters, and response examples.

## Working with the Boilerplate

This project follows the Repository Pattern to separate data access logic from business logic, providing a cleaner and more modular code structure. For full instructions on using the Repository Pattern in this boilerplate, please refer to the [Developer Guide](GUIDE.md).

## License
This project is licensed under the [MIT license](https://opensource.org/licenses/MIT). For more information, see the LICENSE file.
