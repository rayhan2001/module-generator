
# Laravel CRUD Module Generator

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.x-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A **custom Laravel CRUD module generator** to quickly scaffold fully functional modules for both **Web (Blade)** and **API**, including **Model, Repository, Controller, Request, Migration, Routes, and Views**.  

This package is fully **developer-friendly**, supports **direct repository injection** in controllers, and provides **configurable default type** (web/api).

---

## Features

- Generate **Model, Repository, Controller, Request, Migration** automatically
- Supports both **Web (Blade)** and **API** modules
- Uses **direct Repository injection** in Controllers (no interfaces)
- Auto-generates **CRUD routes** in a clean group-controller format
- Optional **Blade views** generation for web modules (`index`, `create`, `edit`, `_form`)
- Configurable default type (`web` or `api`) on installation
- Fully compatible with Laravel 10+

---

## Installation

### Step 1: Add package to your project

Add the package repository to your `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/your-username/module-generator"
    }
]
```

Then install via composer:

```bash
composer require rayhan2001/module-generator:dev-main
```

---

### Step 2: Publish Config

```bash
php artisan module:install
```

- Interactive prompt will ask: `Default module type? [api, web]`
- Select your preferred default type
- This will publish `config/module-generator.php`

---

## Usage

### Generate a new module

```bash
# Default type from config (api/web)
php artisan make:module Category

# Override type
php artisan make:module Product --type=web
php artisan make:module Tag --type=api

# Force overwrite existing files
php artisan make:module Category --force
```

### Generated Files

For `Category` module:

```
app/Models/Category.php
app/Repositories/CategoryRepository.php
app/Http/Controllers/CategoryController.php
app/Http/Requests/CategoryRequest.php
database/migrations/*_create_categories_table.php
routes/web.php OR routes/api.php (routes appended)
resources/views/categories/ (if web)
  ├─ index.blade.php
  ├─ create.blade.php
  ├─ edit.blade.php
  └─ _form.blade.php
```

---

### Routes

**Web module format:**
```php
Route::controller(CategoryController::class)
    ->prefix('categories')
    ->as('categories.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
    });
```

**API module format:**
```php
Route::controller(CategoryController::class)
    ->prefix('categories')
    ->as('categories.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::put('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
    });
```

---

### Access in browser / API

- Web module: `http://your-app.test/categories/`
- API module: `http://your-app.test/api/categories/`

---

## Notes

- **Direct Repository Injection**: Controller directly injects Repository (`no interface`)  
- **Request Validation**: Generated `{Model}Request` handles store/update validation  
- **Blade Views**: Only generated for `web` type modules  
- **Configurable Default Type**: Change in `config/module-generator.php`  
- **Overwrite Files**: Use `--force` flag  

---

## Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/your-feature`
3. Commit your changes: `git commit -m 'Add some feature'`
4. Push to branch: `git push origin feature/your-feature`
5. Submit a pull request

---

## License

This package is open-sourced under the **MIT license**.
