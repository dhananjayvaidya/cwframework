# Migration Guide: CWFramework 2.x → 3.0

## Overview

CWFramework 3.0 is a major upgrade bringing PHP 8.5 features, modern architecture, and AI capabilities. This guide helps you migrate from version 2.x.

## Breaking Changes

### 1. PHP Version Requirement

**Before (2.x):**
```
PHP >= 5.6
```

**After (3.0):**
```
PHP >= 8.5
```

### 2. Routing Syntax

**Before (2.x):**
```php
// config/routes.php
$App->get("/", "home->first");
$App->post("/login", "account->login");
$App->get("/product/get/{}", "products->getById");
```

**After (3.0):**
```php
// config/routes.php
$router = app()->router;

$router->get('/', 'home@first');
$router->post('/login', 'account@login');
$router->get('/product/{id}', 'products@show');

// Or use closures
$router->get('/', function() {
    return view('home');
});
```

### 3. Database Layer

**Before (2.x):**
```php
class UserModel extends DB {
    public $TableName = "users";
    public $Schema = [...];
}

$model = new UserModel();
$users = $model->getAll();
$user = $model->getById(1);
$model->add($data);
```

**After (3.0):**
```php
// No need for model classes for simple queries
$users = db('users')->get();
$user = db('users')->find(1);
$id = db('users')->insert($data);

// Query builder
$users = db('users')
    ->where('status', 'active')
    ->orderBy('created_at', 'DESC')
    ->get();
```

### 4. Controllers

**Before (2.x):**
```php
class home extends Controller {
    function first() {
        $this->view->render("front/home");
    }
}
```

**After (3.0):**
```php
namespace App\Controllers;

use CWF\Http\Request;
use CWF\Http\Response;

class HomeController
{
    public function index(Request $request): Response
    {
        return view('front/home');
    }
}
```

### 5. Views

**Before (2.x):**
```php
$this->view->render("front/home");
```

**After (3.0):**
```php
return view('front/home');
return view('front/home', ['user' => $user]);

// JSON responses
return json(['status' => 'success']);

// Redirects
return redirect('/dashboard');
```

### 6. Configuration

**Before (2.x):**
```php
// config/app.php
define("_PATH_", base_url());
define("_COMPANY_NAME_", "CWFramework");
```

**After (3.0):**
```env
# .env file
APP_NAME=CWFramework
APP_URL=http://localhost
DB_HOST=127.0.0.1
DB_DATABASE=cwframework
```

```php
// Access config
$name = env('APP_NAME');
$dbHost = env('DB_HOST');
```

## Step-by-Step Migration

### Step 1: Update PHP Version

Ensure your server runs PHP 8.5 or higher:

```bash
php -v
```

### Step 2: Update Composer Dependencies

```bash
composer install
```

### Step 3: Create .env File

```bash
cp .env.example .env
```

Edit `.env` and migrate your configuration from `config/app.php` and `config/database.php`:

```env
APP_NAME=YourAppName
DB_HOST=your-db-host
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

### Step 4: Update Routes

**Old routes.php:**
```php
$App->get("/", "home->first");
$App->post("/account/login", "account->login");
$App->crud("/admin/user", "user_admin");
```

**New routes.php:**
```php
$router = app()->router;

$router->get('/', 'HomeController@index');
$router->post('/account/login', 'AccountController@login');

// CRUD routes
$router->resource('admin/users', 'UserAdminController');
// Or legacy CRUD
$router->crud('/admin/user', 'UserAdminController');
```

### Step 5: Update Controllers

**Before:**
```php
// http/controllers/home.php
class home extends Controller {
    function first() {
        $data = $this->setting->get();
        $this->view->render("front/home");
    }
}
```

**After:**
```php
// http/controllers/HomeController.php
namespace App\Controllers;

use CWF\Http\Request;
use CWF\Http\Response;

class HomeController
{
    public function index(Request $request): Response
    {
        $settings = db('settings')->get();
        return view('front/home', compact('settings'));
    }
}
```

### Step 6: Update Models

**Before:**
```php
class UserModel extends DB {
    public $TableName = "users";
    public $Schema = [
        "id" => [
            "dataType" => "int",
            "dataSize" => "11",
            "not_null" => true,
            "primary_key" => true,
            "auto_increment" => true
        ],
        "name" => [
            "dataType" => "string",
            "dataSize" => "100",
            "not_null" => true
        ]
    ];
}

$user = new UserModel();
$users = $user->getAll();
```

**After:**
```php
// Option 1: Use query builder directly
$users = db('users')->get();
$user = db('users')->find(1);

// Option 2: Create a model class (optional)
namespace App\Models;

class User
{
    protected string $table = 'users';

    public function all(): array
    {
        return db($this->table)->get();
    }

    public function find(int $id): ?array
    {
        return db($this->table)->find($id);
    }
}
```

### Step 7: Update Database Queries

**Before:**
```php
$model = new UserModel();
$users = $model->getAll();
$user = $model->getById($id);
$model->add(['name' => 'John']);
$model->update(['name' => 'Jane'], $id);
$model->remove($id);
```

**After:**
```php
$users = db('users')->get();
$user = db('users')->find($id);
db('users')->insert(['name' => 'John']);
db('users')->where('id', $id)->update(['name' => 'Jane']);
db('users')->where('id', $id)->delete();

// Advanced queries
$activeUsers = db('users')
    ->where('status', 'active')
    ->where('age', '>', 18)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();
```

### Step 8: Update Views

Views remain mostly compatible, but update any dynamic data access:

**Before:**
```php
<!-- resources/views/front/home.php -->
<?php echo $data['title']; ?>
```

**After:**
```php
<!-- resources/views/front/home.php -->
<?php echo $title; ?>
<?php echo htmlspecialchars($userInput); ?> <!-- XSS protection -->
```

### Step 9: Add Middleware (New Feature)

```php
// config/routes.php
$router->group(['middleware' => ['auth']], function($router) {
    $router->get('/dashboard', 'DashboardController@index');
});

// Create middleware
// http/middleware/AuthMiddleware.php
namespace App\Middleware;

use CWF\Http\Request;
use CWF\Http\Response;
use Closure;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('user_id')) {
            return redirect('/login');
        }
        return $next($request);
    }
}
```

## New Features to Adopt

### 1. AI Integration

```php
// Chat with AI
$response = ai('openai')->chat([
    ['role' => 'user', 'content' => 'Hello!']
]);

// Generate embeddings
$embeddings = ai()->embeddings('Your text here');
```

### 2. Caching

```php
// Cache data
cache()->set('users', $users, 3600);
$users = cache()->get('users');

// Remember pattern
$users = cache()->remember('users', 3600, function() {
    return db('users')->get();
});
```

### 3. Modern Request/Response

```php
public function store(Request $request): Response
{
    $data = $request->input('name');
    $json = $request->json();
    $file = $request->file('upload');

    return json(['success' => true]);
    return redirect('/success');
    return view('success', $data);
}
```

### 4. Logging

```php
logger()->info('User logged in', ['user_id' => $userId]);
logger()->error('Failed to process', ['error' => $e->getMessage()]);
```

## Compatibility Layer

For gradual migration, you can keep using legacy patterns:

```php
// Legacy controller format still works
$router->get('/', 'home->first');

// Legacy CRUD
$router->crud('/admin/user', 'user_admin');
```

## Testing Your Migration

1. **Test routes**: Visit each page and ensure routing works
2. **Test database**: Verify CRUD operations work
3. **Test authentication**: Check login/logout flows
4. **Test API endpoints**: Verify JSON responses
5. **Check logs**: Review `storage/logs/` for errors

## Common Issues

### Issue: "Class not found"

**Solution:** Ensure autoloading is working:
```bash
composer dump-autoload
```

### Issue: "Database connection failed"

**Solution:** Check `.env` database credentials:
```env
DB_HOST=127.0.0.1
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

### Issue: "Session not working"

**Solution:** Ensure storage directory is writable:
```bash
chmod -R 775 storage/
```

## Rollback Plan

If you need to rollback:

1. Keep a backup of your 2.x codebase
2. Backup your database before migration
3. Test migration on a staging server first
4. Use version control (git) to track changes

## Support

For migration assistance:
- GitHub Issues: https://github.com/dhananjayvaidya/cwframework/issues
- Email: dhananjay@zitasoftinfotech.com

---

**Note:** Take time to migrate incrementally. You don't need to rewrite everything at once. The framework supports gradual migration.
