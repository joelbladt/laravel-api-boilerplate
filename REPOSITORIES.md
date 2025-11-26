# Project Repositories

_Last updated: 2025-11-26_

This document describes all code repositories that are considered part of the
**Laravel API Boilerplate** project and may be compiled into a release or used
as official companion codebases.

It is intended to satisfy the requirement that project documentation lists all
subprojects or additional repositories, including their status and intent.

---

## 1. Primary repository

**Name:** `laravel-api-boilerplate`  
**Location:** https://github.com/joelbladt/laravel-api-boilerplate  
**Role / Intent:**  
Boilerplate for building RESTful APIs with Laravel. This repository contains all
application code, configuration, tests and documentation required to build and
run the boilerplate.

**Relation to releases:**  
All tagged releases (e.g. `2.0.x`) are built from this repository only.

**Status:** `active`

---

## 2. Subprojects and additional repositories

At the moment, the Laravel API Boilerplate project is maintained as a single
repository. There are no additional project-owned codebases that are required
to build or run a release artifact.

| Repository name | Location (URL) | Role / Intent | Relation to releases | Status |
|-----------------|----------------|---------------|----------------------|--------|
| _none currently_ | – | This project is currently maintained as a single repository only. | – | n/a |

---

## 3. External dependencies (Composer packages)

The Laravel API Boilerplate relies on a set of third-party packages managed via
Composer. These are **not** project-owned repositories, but external
dependencies used at runtime or during development.

### 3.1 Runtime dependencies (`require`)

| Package                  | Type    | Description / Role                                                                 |
|--------------------------|---------|------------------------------------------------------------------------------------|
| `laravel/framework`      | runtime | Core Laravel framework; provides the application kernel, routing, Eloquent, etc.  |
| `laravel/tinker`         | runtime | REPL for interacting with the application from the command line.                  |
| `darkaonline/l5-swagger` | runtime | Integrates OpenAPI/Swagger generation and UI into the Laravel application.        |

### 3.2 Development dependencies (`require-dev`)

| Package                    | Type       | Description / Role                                                                                                    |
|----------------------------|------------|------------------------------------------------------------------------------------------------------------------------|
| `phpunit/phpunit`          | dev / test | Main test framework for unit and feature tests.                                                                       |
| `brianium/paratest`        | dev / test | Runs PHPUnit tests in parallel to speed up the test suite.                                                            |
| `mockery/mockery`          | dev / test | Mocking framework used within tests.                                                                                  |
| `fakerphp/faker`           | dev / test | Generates fake data for tests and seeders.                                                                            |
| `phpstan/phpstan`          | dev / qa   | Static analysis tool for PHP.                                                                                         |
| `larastan/larastan`        | dev / qa   | PHPStan extension with Laravel-specific rules and type information.                                                   |
| `laravel/pint`             | dev / qa   | Opinionated code style fixer for PHP (PSR-12 / Laravel presets).                                                      |
| `laravel/sail`             | dev / env  | Docker-based local development environment for Laravel.                                                               |
| `laravel/pail`             | dev / ops  | Developer-oriented log viewer / tailing tool for Laravel applications.                                               |
| `nunomaduro/collision`     | dev / dx   | Improves CLI error output for Artisan commands and tests.                                                             |
| `roave/security-advisories`| dev / sec  | Composer meta-package that prevents installation of dependencies with known security vulnerabilities.                 |

These dependencies are managed via `composer.json` and may change over time.
For the authoritative list of packages and versions, refer to that file.
