# CWFramework Studio - Visual Development Platform

## Overview

CWFramework Studio is a revolutionary web-based interface that sets CWFramework apart from all other PHP frameworks. It provides visual tools for:

- ✅ **Feature Management** - Enable/disable framework modules dynamically
- ✅ **Code Generation** - Create controllers, models, APIs through UI
- ✅ **AI Assistant** - Generate code using OpenAI/Claude
- ✅ **Build & Encryption** - Compile and protect your code
- ✅ **Plugin Marketplace** - Extend functionality with plugins
- ✅ **Database Manager** - Visual database management
- ✅ **API Tester** - Test APIs without Postman
- ✅ **Log Viewer** - Monitor application logs in real-time

## What Makes CWFramework Unique

### 1. **Visual Feature Toggle**

Unlike Laravel or CodeIgniter where you need to modify code to enable/disable features, CWFramework lets you:

```
✓ Click a button to enable/disable AI integration
✓ Toggle caching without touching config files
✓ Enable/disable authentication with one click
✓ Hot-reload features without restarting server
```

**Access:** `/studio/features`

### 2. **AI-Powered Code Generation**

**No other PHP framework has this:**

- Generate entire CRUD systems by describing what you want
- AI writes controllers, models, and routes for you
- Validates generated code for security issues
- Suggests best practices automatically

**Example:**
```
User Input: "Create a blog system with posts, categories, and tags"
AI Output: Complete MVC code with relationships, validations, and API endpoints
```

**Access:** `/studio/generator`

### 3. **Code Protection & Encryption**

**Unique to CWFramework:**

Build your application with multiple protection levels:

- **Minification** - Reduce file size by 30-50%
- **Obfuscation** - Rename variables to prevent reverse engineering
- **Encryption** - Base64, ionCube, or SourceGuardian encryption
- **Licensing** - Generate license keys for your applications
- **Domain Locking** - Restrict app to specific domains

**Perfect for:**
- Selling PHP applications
- Client projects
- SaaS products
- Premium plugins

**Access:** `/studio/build`

### 4. **Plugin Marketplace**

Extend your framework like WordPress, but better:

- Install plugins with one click
- Auto-handle dependencies
- Activate/deactivate without code changes
- Create your own plugins with template generator

**Access:** `/studio/plugins`

### 5. **Integrated Development Tools**

All in one interface:

- **Database Manager** - Visual query builder and table editor
- **API Tester** - Test endpoints without external tools
- **Log Viewer** - Real-time log monitoring
- **File Manager** - Edit files in browser (coming soon)

## Getting Started

### 1. Enable Studio

```env
# .env
STUDIO_ENABLED=true
STUDIO_PATH=/studio
STUDIO_USERNAME=admin
STUDIO_PASSWORD=your-secure-password
STUDIO_ALLOWED_IPS=*  # Or specific IPs: 127.0.0.1,192.168.1.1
```

### 2. Register Studio Routes

```php
// config/routes.php

// Studio routes (protected by authentication)
$router->get('/studio', 'StudioController@index');
$router->get('/studio/login', 'StudioController@login');
$router->post('/studio/login', 'StudioController@login');
$router->get('/studio/logout', 'StudioController@logout');

$router->group(['prefix' => '/studio', 'middleware' => ['studio_auth']], function($router) {
    $router->get('/features', 'StudioController@features');
    $router->post('/features', 'StudioController@features');

    $router->get('/generator', 'StudioController@generator');
    $router->post('/generator', 'StudioController@generator');

    $router->get('/ai-assistant', 'StudioController@aiAssistant');
    $router->post('/ai-assistant', 'StudioController@aiAssistant');

    $router->get('/build', 'StudioController@build');
    $router->post('/build', 'StudioController@build');

    $router->get('/plugins', 'StudioController@plugins');
    $router->post('/plugins', 'StudioController@plugins');

    $router->get('/database', 'StudioController@database');
    $router->get('/api-tester', 'StudioController@apiTester');
    $router->post('/api-tester', 'StudioController@apiTester');

    $router->get('/logs', 'StudioController@logs');
    $router->get('/settings', 'StudioController@settings');
});
```

### 3. Access Studio

1. Navigate to `http://your-app.com/studio`
2. Login with credentials from `.env`
3. Start managing your framework!

## Feature Management

### Available Features

| Feature | Description | Dependencies |
|---------|-------------|--------------|
| **AI Integration** | OpenAI & Anthropic APIs | - |
| **Caching** | Multi-driver cache system | - |
| **Database** | Query builder & ORM | Required |
| **Authentication** | JWT & session auth | - |
| **API Rate Limiting** | Request throttling | Cache |
| **CORS** | Cross-origin support | - |
| **File Upload** | File management | - |
| **Email** | PHPMailer integration | - |
| **Logging** | Monolog logging | - |
| **Queue** | Background jobs | Cache |
| **Real-time** | WebSocket/SSE | - |
| **GraphQL** | GraphQL API | - |
| **Multi-tenancy** | Multi-tenant support | Database |
| **Admin Panel** | Built-in admin UI | Authentication |
| **API Docs** | Auto-generated docs | - |

### Enable/Disable Features

**Via UI:**
1. Go to `/studio/features`
2. Click toggle button next to feature
3. System automatically handles dependencies

**Via Code:**
```php
$features = app('studio')->features();

// Enable feature
$features->enable('queue');

// Disable feature
$features->disable('queue');

// Check if enabled
if ($features->isEnabled('ai')) {
    // Use AI features
}
```

## Code Generator

### Generate Controller

**Via UI:**
1. Go to `/studio/generator`
2. Select "Controller"
3. Fill in details:
   - Name: `ProductController`
   - Methods: `index`, `show`, `store`, `update`, `destroy`
   - Use AI: Yes/No
4. Click "Generate"

**Via API:**
```php
$generator = app('studio')->generator();

$controller = $generator->generateController([
    'name' => 'ProductController',
    'namespace' => 'App\\Controllers',
    'methods' => ['index', 'show', 'store'],
    'use_ai' => false,
]);
```

### Generate Model

```php
$model = $generator->generateModel([
    'name' => 'Product',
    'table' => 'products',
    'fields' => [
        'id' => 'int',
        'name' => 'string',
        'price' => 'float',
        'stock' => 'int',
    ],
]);
```

### Generate Complete CRUD

```php
$crud = $generator->generateCrud([
    'name' => 'Product',
    'fields' => [
        'name' => 'string',
        'price' => 'decimal',
        'description' => 'text',
    ],
]);

// Creates:
// - ProductModel.php
// - ProductController.php
// - Routes in config/routes.php
```

### AI-Powered Generation

```php
$code = $generator->generateWithAI([
    'type' => 'controller',
    'description' => 'Create a REST API for managing blog posts with categories and tags. Include pagination and search functionality.'
]);

// AI generates complete, production-ready code!
```

## Build System

### Create Production Build

**Via UI:**
1. Go to `/studio/build`
2. Configure build options:
   - ✓ Minify code
   - ✓ Obfuscate variables
   - ✓ Encrypt with Base64/ionCube
   - ✓ Generate license
3. Click "Build"
4. Download ZIP file

**Via Code:**
```php
$builder = app('studio')->builder();

$build = $builder->build([
    'minify' => true,
    'obfuscate' => true,
    'encrypt' => true,
    'encryption_method' => 'base64', // or 'ioncube'
    'license' => true,
    'license_key' => 'XXXX-XXXX-XXXX-XXXX',
    'domain' => 'example.com',
    'expiry_date' => '2025-12-31',
]);

// Returns:
// [
//     'build_id' => 'build_abc123',
//     'archive' => '/path/to/build.zip',
//     'steps' => [...]
// ]
```

### Encryption Methods

#### 1. Base64 (Built-in)
- ✓ No external dependencies
- ✓ Works everywhere
- ⚠️ Can be decoded (obfuscation, not security)

#### 2. ionCube (Recommended)
- ✓ Industry standard
- ✓ Cannot be decoded
- ✓ License management
- ⚠️ Requires ionCube encoder

#### 3. SourceGuardian
- ✓ Commercial solution
- ✓ Advanced protection
- ⚠️ Paid license required

### License Management

Generated licenses include:

```json
{
    "license_key": "ABCD-EFGH-IJKL-MNOP",
    "product": "My Application",
    "version": "3.0.0",
    "issued_at": "2024-01-01 00:00:00",
    "expires_at": "2025-12-31 23:59:59",
    "domain": "example.com",
    "signature": "sha256_hash"
}
```

Automatic license checking:
- Validates on every request
- Checks expiry date
- Verifies domain restriction
- Validates signature

## Plugin System

### Install Plugin

**Via UI:**
1. Go to `/studio/plugins`
2. Browse marketplace
3. Click "Install" on desired plugin
4. Click "Activate"

**Via Code:**
```php
$plugins = app('studio')->plugins();

// Install from marketplace
$plugins->install('cwf-payment');

// Activate
$plugins->activate('cwf-payment');

// Check if active
if ($plugins->get('cwf-payment')['active']) {
    // Use plugin
}
```

### Create Custom Plugin

```php
$plugins->createTemplate('My Plugin');

// Creates:
// plugins/my-plugin/
//   ├── plugin.json
//   ├── src/Plugin.php
//   ├── hooks/activate.php
//   └── hooks/deactivate.php
```

**Plugin Structure:**

```json
// plugin.json
{
    "name": "Payment Gateway",
    "slug": "cwf-payment",
    "version": "1.0.0",
    "description": "Stripe and PayPal integration",
    "author": "Your Name",
    "dependencies": ["authentication"]
}
```

```php
// src/Plugin.php
namespace CWF\Plugins\PaymentGateway;

class Plugin {
    public function init() {
        // Register routes
        app()->router->post('/payment/charge', 'PaymentController@charge');

        // Register middleware
        // Add hooks
    }
}
```

## Comparison with Other Frameworks

| Feature | CWFramework | Laravel | CodeIgniter | Symfony |
|---------|-------------|---------|-------------|---------|
| **Visual Studio** | ✅ Yes | ❌ No | ❌ No | ❌ No |
| **Feature Toggle UI** | ✅ Yes | ❌ No | ❌ No | ❌ No |
| **AI Code Generator** | ✅ Yes | ❌ No | ❌ No | ❌ No |
| **Built-in Encryption** | ✅ Yes | ❌ No | ❌ No | ❌ No |
| **Plugin Marketplace** | ✅ Yes | ❌ No | ❌ No | ❌ No |
| **Visual API Tester** | ✅ Yes | ❌ No | ❌ No | ❌ No |
| **One-Click Builds** | ✅ Yes | ❌ No | ❌ No | ❌ No |
| **License Generator** | ✅ Yes | ❌ No | ❌ No | ❌ No |

## Best Practices

### Security

**For Production:**
```env
STUDIO_ENABLED=false  # Disable studio in production
# Or restrict to your IP
STUDIO_ALLOWED_IPS=your.ip.address
```

**Strong Passwords:**
```bash
# Generate secure password
php -r "echo password_hash('my-super-secret-password', PASSWORD_BCRYPT);"
```

### Performance

- Disable studio in production
- Use encrypted builds for distribution
- Enable feature caching
- Minify all production builds

### Development Workflow

1. **Development:**
   - Use Studio for rapid development
   - Generate code with AI
   - Test features visually

2. **Testing:**
   - Use API tester
   - Monitor logs
   - Test with feature toggles

3. **Production:**
   - Create encrypted build
   - Generate license (if selling)
   - Disable studio
   - Deploy ZIP file

## API Reference

### StudioManager

```php
$studio = app('studio');

// Features
$studio->features()->enable('queue');
$studio->features()->disable('queue');
$studio->features()->isEnabled('queue');

// Generator
$studio->generator()->generateController($config);
$studio->generator()->generateModel($config);
$studio->generator()->generateCrud($config);

// Builder
$studio->builder()->build($config);

// Plugins
$studio->plugins()->install('plugin-slug');
$studio->plugins()->activate('plugin-slug');
$studio->plugins()->deactivate('plugin-slug');

// Statistics
$stats = $studio->getStatistics();
$activity = $studio->getRecentActivity(10);

// Config
$export = $studio->exportConfig();
$studio->importConfig($json);
```

## Troubleshooting

### Studio Not Loading

**Check:**
1. `STUDIO_ENABLED=true` in `.env`
2. Routes are registered
3. IP is whitelisted
4. Permissions on `storage/studio/`

### Code Generation Fails

**Check:**
1. Write permissions on target directories
2. Valid configuration
3. No naming conflicts
4. PHP syntax validator working

### Build Fails

**Check:**
1. Composer installed
2. Write permissions on `storage/builds/`
3. ZIP extension enabled
4. Enough disk space

## Roadmap

**Coming Soon:**

- [ ] Visual database designer
- [ ] Real-time collaboration
- [ ] Visual workflow builder
- [ ] Mobile app for monitoring
- [ ] Cloud deployment integration
- [ ] Automated testing suite
- [ ] Performance profiler
- [ ] Security scanner

---

## Summary

CWFramework Studio makes you **10x more productive** by:

✅ Eliminating repetitive coding tasks
✅ Leveraging AI to write code for you
✅ Managing features without touching code
✅ Building encrypted, professional releases
✅ Extending functionality with plugins
✅ All through a beautiful web interface

**No other PHP framework offers this level of visual development experience.**

Ready to build faster? Start with `/studio` today!
