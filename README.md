# Laravel CRUD Module Generator

[![Laravel](https://img.shields.io/badge/Laravel-9.x%20%7C%2010.x%20%7C%2011.x%20%7C%2012.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/rayhan2001/module-generator.svg)](https://packagist.org/packages/rayhan2001/module-generator)

A **powerful Laravel CRUD module generator** that quickly scaffolds fully functional modules for both **Web (Blade)** and **API** applications. Generate complete CRUD operations with **Model, Repository, Controller, Request, Migration, Routes, and Views** in seconds.

This package is **developer-friendly**, supports **direct repository injection** in controllers, and provides **configurable default type** (web/api).

---

## ✨ Features

- 🚀 **Complete CRUD Generation**: Model, Repository, Controller, Request, Migration
- 🌐 **Dual Module Types**: Web (Blade) and API modules
- 💉 **Direct Repository Injection**: No interfaces needed
- 🛣️ **Auto Route Generation**: Clean group-controller format
- 🎨 **Blade Views**: Auto-generated views for web modules
- ⚙️ **Configurable Defaults**: Set default module type on installation
- 🔄 **Force Overwrite**: Overwrite existing files with `--force` flag
- 📱 **Laravel 9-12 Support**: Compatible with latest Laravel versions

---

## 📦 Installation

### Method 1: Via Packagist (Recommended)

```bash
composer require rayhan2001/module-generator
```

### Method 2: Via GitHub (Development)

Add the package repository to your `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/rayhan2001/module-generator"
    }
]
```

Then install:

```bash
composer require rayhan2001/module-generator:dev-main
```

### Method 3: Local Development

For local development, you can use a path repository:

```bash
# In your Laravel project
composer config repositories.local path /path/to/module-generator
composer require rayhan2001/module-generator:@dev
```

---

## ⚙️ Configuration

After installation, run the setup command:

```bash
php artisan module:install
```

This will:

- Ask for your preferred default module type (`api` or `web`)
- Publish the configuration file to `config/module-generator.php`
- Set up the package for use

**Example output:**

```
⚙️  Module Generator installation

 Default module type? [api]:
  [0] api
  [1] web
 > Config published to config/module-generator.php and default type set to: api
✅ Installation complete. You can now run: php artisan make:module Name --type=web|api
```

---

## 🚀 Usage

### Generate a new module

```bash
# Use default type from config (api/web)
php artisan make:module Category

# Override type explicitly
php artisan make:module Product --type=web
php artisan make:module Tag --type=api

# Force overwrite existing files
php artisan make:module Category --force
```

### Available Commands

```bash
# List all available commands
php artisan list | grep module

# Output:
# make:module               Generate a full CRUD module with Controller, Repository, Requests, Views, Migration & Routes
# module:install            Install and configure Rayhan2001 Module Generator package
```

---

## 📁 Generated Files Structure

For a `Category` module, the following files will be generated:

```
app/
├── Models/Category.php
├── Repositories/CategoryRepository.php
├── Http/Controllers/CategoryController.php
└── Http/Requests/
    ├── CategoryRequest.php
    └── UpdateCategoryRequest.php

database/migrations/
└── YYYY_MM_DD_HHMMSS_create_categories_table.php

routes/
└── web.php OR api.php (routes appended)

resources/views/categories/ (only for web type)
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── form.blade.php
```

---

## 🛣️ Generated Routes

### Web Module Routes

```php
// Module: Category
use App\Http\Controllers\CategoryController;

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

### API Module Routes

```php
// Module: Category
use App\Http\Controllers\CategoryController;

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

## 🎯 Example Usage

### 1. Generate an API Module

```bash
php artisan make:module Product --type=api
```

**Generated Controller:**

```php
<?php

namespace App\Http\Controllers;

use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepository $repository
    ) {}

    public function index(Request $request)
    {
        return response()->json($this->repository->paginateData($request));
    }

    public function store(Request $request)
    {
        $item = $this->repository->store($request->all());
        return response()->json($item, 201);
    }

    public function show($id)
    {
        return response()->json($this->repository->find($id));
    }

    public function update(Request $request, $id)
    {
        $item = $this->repository->update($id, $request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
```

### 2. Generate a Web Module

```bash
php artisan make:module Category --type=web
```

**Generated Controller:**

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepository $repository
    ) {}

    public function index(Request $request)
    {
        $data['title'] = 'All ' . \Illuminate\Support\Str::plural('Category');
        $data['collection'] = $this->repository->paginateData($request);
        return view('categories.index')->with($data);
    }

    public function create()
    {
        $data['title'] = 'Create Category';
        return view('categories.create')->with($data);
    }

    public function store(CategoryRequest $request)
    {
        $this->repository->store($request->validated());
        return redirect()->route('categories.index')->with('success', 'Created successfully');
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Category';
        $data['item'] = $this->repository->find($id);
        return view('categories.edit')->with($data);
    }

    public function update(CategoryRequest $request, $id)
    {
        $this->repository->update($id, $request->validated());
        return redirect()->route('categories.index')->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return redirect()->route('categories.index')->with('success', 'Deleted successfully');
    }
}
```

---

## 🌐 Access Your Modules

### Web Modules

- **Index**: `http://your-app.test/categories/`
- **Create**: `http://your-app.test/categories/create`
- **Edit**: `http://your-app.test/categories/edit/1`

### API Modules

- **Index**: `http://your-app.test/api/products/`
- **Store**: `POST http://your-app.test/api/products/store`
- **Show**: `http://your-app.test/api/products/1`
- **Update**: `PUT http://your-app.test/api/products/update/1`
- **Delete**: `DELETE http://your-app.test/api/products/delete/1`

---

## ⚙️ Configuration

The package publishes a configuration file at `config/module-generator.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Module Type
    |--------------------------------------------------------------------------
    |
    | This option controls the default module type when no --type flag
    | is provided. You can set this to 'api' or 'web'.
    |
    */

    'default_type' => 'api',
];
```

---

## 🔧 Customization

### Modify Generated Files

After generation, you can customize the generated files:

1. **Add columns to migration**: Edit the migration file
2. **Add validation rules**: Modify the Request classes
3. **Customize views**: Edit the Blade templates
4. **Add business logic**: Extend the Repository class

### Example: Adding columns to migration

```php
// database/migrations/YYYY_MM_DD_HHMMSS_create_products_table.php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
```

---

## 🐛 Troubleshooting

### Common Issues

1. **Package not found**: Make sure you've added the repository to `composer.json`
2. **Laravel version compatibility**: Ensure you're using Laravel 9-12
3. **Permission issues**: Check file permissions for generated files
4. **Route conflicts**: Ensure route names don't conflict with existing routes

### Getting Help

- Check the [Issues](https://github.com/rayhan2001/module-generator/issues) page
- Create a new issue with detailed information
- Include Laravel version, PHP version, and error messages

---

## 📋 Requirements

- **PHP**: 8.0 or higher
- **Laravel**: 9.x, 10.x, 11.x, or 12.x
- **Composer**: Latest version recommended

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. **Fork** the repository
2. **Create** your feature branch: `git checkout -b feature/amazing-feature`
3. **Commit** your changes: `git commit -m 'Add amazing feature'`
4. **Push** to the branch: `git push origin feature/amazing-feature`
5. **Open** a Pull Request

### Development Setup

```bash
# Clone the repository
git clone https://github.com/rayhan2001/module-generator.git

# Install dependencies
composer install

# Run tests (if available)
composer test
```

---

## 📄 License

This package is open-sourced under the **MIT License**. See the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- Built for the Laravel community
- Inspired by Laravel's built-in generators
- Thanks to all contributors and users

---

## 📊 Stats

![GitHub stars](https://img.shields.io/github/stars/rayhan2001/module-generator?style=social)
![GitHub forks](https://img.shields.io/github/forks/rayhan2001/module-generator?style=social)
![GitHub issues](https://img.shields.io/github/issues/rayhan2001/module-generator)
![GitHub pull requests](https://img.shields.io/github/issues-pr/rayhan2001/module-generator)
