# CWFramework 3.0 - Modern PHP 8.5 Framework

A powerful, lightweight PHP framework optimized for both AI-powered and traditional web applications.

## 🚀 What's New in Version 3.0

### PHP 8.5 Features
- **Property Hooks** - Modern property accessors with lazy initialization
- **Strict Types** - Full type safety across the framework
- **Readonly Classes** - Immutable data structures
- **Match Expressions** - Cleaner conditional logic
- **Named Arguments** - Better API design

### Core Improvements
- **Dependency Injection Container** - PSR-11 compliant with auto-wiring
- **Middleware Pipeline** - Request/response processing chain
- **Modern Router** - RESTful routing with parameter binding
- **Query Builder** - Fluent database interface
- **Multi-Driver Cache** - File, Redis, and in-memory support

### AI Integration
- **OpenAI Support** - GPT-4, embeddings, function calling
- **Anthropic (Claude)** - Latest Claude models with tool use
- **Streaming** - Real-time AI responses
- **Vision Models** - Image analysis capabilities
- **Vector Database** - Ready for RAG applications

## 📋 Requirements

- PHP >= 8.5
- Composer
- MySQL/PostgreSQL/SQLite
- Redis (optional, for caching)

## 🔧 Installation

### 1. Install Dependencies

```bash
composer install
```

### 2. Environment Configuration

```bash
cp .env.example .env
```

Edit `.env` and configure your settings:

```env
# Database
DB_HOST=127.0.0.1
DB_DATABASE=cwframework
DB_USERNAME=root
DB_PASSWORD=

# AI Providers
OPENAI_API_KEY=your-key-here
ANTHROPIC_API_KEY=your-key-here
```

### 3. Directory Permissions

```bash
chmod -R 775 storage/
```

## 🏗️ Architecture

### Modern MVC Structure

```
cwframework/
├── core/                    # Framework core
│   ├── Application.php      # Application bootstrap
│   ├── Container/          # DI container
│   ├── Http/               # HTTP layer (Router, Request, Response)
│   ├── Database/           # Database manager & query builder
│   ├── AI/                 # AI integration layer
│   ├── Cache/              # Caching system
│   ├── Logging/            # Logging system
│   └── helpers/            # Helper functions
├── http/
│   ├── controllers/        # Application controllers
│   ├── models/            # Application models
│   └── middleware/        # Custom middleware
├── config/                # Configuration files
├── resources/             # Views and assets
└── storage/               # Logs, cache, uploads
```

## 💡 Quick Start Examples

### 1. Define Routes

```php
// config/routes.php
$router = app()->router;

// Simple route
$router->get('/', function() {
    return view('home');
});

// Route with parameters
$router->get('/user/{id}', function($request, $id) {
    $user = db('users')->find($id);
    return json($user);
});

// Controller route
$router->post('/api/posts', 'PostController@store');

// Route with middleware
$router->get('/admin/dashboard', 'AdminController@index')
    ->middleware(['auth', 'admin']);

// Route groups
$router->group(['prefix' => '/api', 'middleware' => ['cors']], function($router) {
    $router->get('/users', 'UserController@index');
    $router->post('/users', 'UserController@store');
});

// RESTful resource
$router->resource('posts', 'PostController');
```

### 2. Database Queries

```php
// Query builder
$users = db('users')
    ->where('status', 'active')
    ->where('age', '>', 18)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// Insert
$id = db('users')->insert([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('secret')
]);

// Update
db('users')
    ->where('id', $id)
    ->update(['status' => 'verified']);

// Delete
db('users')->where('id', $id)->delete();

// Joins
$posts = db('posts')
    ->join('users', 'posts.user_id', '=', 'users.id')
    ->select(['posts.*', 'users.name as author'])
    ->get();

// Transactions
db()->transaction(function($db) {
    $userId = $db->table('users')->insert(['name' => 'John']);
    $db->table('profiles')->insert(['user_id' => $userId]);
});
```

### 3. AI Integration

```php
// OpenAI Chat
$response = ai('openai')->chat([
    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
    ['role' => 'user', 'content' => 'Explain PHP 8.5 property hooks']
]);
echo $response->getContent();

// Anthropic (Claude)
$response = ai('anthropic')->chat([
    ['role' => 'user', 'content' => 'Write a PHP function to validate email']
]);
echo $response->getContent();

// Streaming responses
ai()->streamChat($messages, function($chunk) {
    echo $chunk;
    flush();
});

// Generate embeddings
$embeddings = ai()->embeddings('Text to embed');

// Function calling
$functions = [
    [
        'name' => 'get_weather',
        'description' => 'Get weather for a location',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'location' => ['type' => 'string']
            ]
        ]
    ]
];

$response = ai()->functions($messages, $functions);
if ($response->hasFunctionCall()) {
    $call = $response->getFunctionCall();
    // Execute function: $call['name'], $call['arguments']
}

// Vision/Image analysis
$response = ai()->vision(
    'https://example.com/image.jpg',
    'What is in this image?'
);
```

### 4. Caching

```php
// Store in cache
cache()->set('user:1', $userData, 3600);

// Retrieve from cache
$user = cache()->get('user:1');

// Remember (get or set)
$users = cache()->remember('users:all', 3600, function() {
    return db('users')->get();
});

// Increment/Decrement
cache()->increment('page:views');
cache()->decrement('stock:count', 5);

// Different drivers
cache('redis')->set('key', 'value');
cache('file')->get('key');

// Delete
cache()->delete('user:1');

// Clear all
cache()->clear();
```

### 5. Controller Example

```php
namespace App\Controllers;

use CWF\Http\Request;
use CWF\Http\Response;

class PostController
{
    public function index(Request $request): Response
    {
        $posts = db('posts')
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get();

        return json($posts);
    }

    public function store(Request $request): Response
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $id = db('posts')->insert($validated);

        return json(['id' => $id], 201);
    }

    public function show(Request $request, $id): Response
    {
        $post = db('posts')->find($id);

        if (!$post) {
            return Response::error('Post not found', 404);
        }

        return json($post);
    }
}
```

### 6. Middleware Example

```php
namespace App\Middleware;

use CWF\Http\Request;
use CWF\Http\Response;
use Closure;

class LogRequestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Before request
        logger()->info('Request: ' . $request->uri());
        $start = microtime(true);

        // Process request
        $response = $next($request);

        // After request
        $duration = microtime(true) - $start;
        logger()->info('Response time: ' . $duration . 's');

        return $response;
    }
}
```

## 🤖 AI Application Examples

### RAG (Retrieval-Augmented Generation)

```php
// Store document embeddings
$text = "CWFramework is a PHP framework...";
$embedding = ai()->embeddings($text);

db('embeddings')->insert([
    'content' => $text,
    'embedding' => json_encode($embedding[0]),
    'created_at' => now()
]);

// Query similar documents (with vector extension)
$query = "Tell me about CWFramework";
$queryEmbedding = ai()->embeddings($query)[0];

// Find similar content (pseudo-code, needs vector extension)
$similar = db()->raw("
    SELECT content
    FROM embeddings
    ORDER BY embedding <-> ?
    LIMIT 5
", [json_encode($queryEmbedding)]);

// Generate response with context
$response = ai()->chat([
    ['role' => 'system', 'content' => 'Answer based on: ' . implode("\n", $similar)],
    ['role' => 'user', 'content' => $query]
]);
```

### AI-Powered API

```php
// API endpoint for AI chat
$router->post('/api/ai/chat', function(Request $request) {
    $messages = $request->input('messages');
    $provider = $request->input('provider', 'openai');

    $response = ai($provider)->chat($messages);

    return json([
        'content' => $response->getContent(),
        'usage' => $response->getUsage(),
        'model' => $response->getModel()
    ]);
});

// Streaming endpoint
$router->post('/api/ai/stream', function(Request $request) {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    ai()->streamChat($request->input('messages'), function($chunk) {
        echo "data: " . json_encode(['chunk' => $chunk]) . "\n\n";
        flush();
    });
});
```

## 📚 Helper Functions

```php
app()              // Get application instance
env()              // Get environment variable
config()           // Get configuration value
base_url()         // Get base URL
url()              // Generate URL
redirect()         // Create redirect response
json()             // Create JSON response
view()             // Create view response
cache()            // Get cache manager
db()               // Get database manager
logger()           // Get logger
ai()               // Get AI manager
dd()               // Dump and die
session()          // Get/set session value
csrf_token()       // Generate CSRF token
bcrypt()           // Hash password
```

## 🔒 Security Features

- CSRF protection
- SQL injection prevention (prepared statements)
- XSS protection in views
- Password hashing with bcrypt
- JWT authentication
- Rate limiting
- CORS middleware

## 📖 Migration from 2.x to 3.0

See [MIGRATION.md](MIGRATION.md) for detailed migration guide.

## 📄 License

MIT License - see LICENSE file

## 👤 Author

**Dhananjay Vaidya**
- Website: https://dhananjayvaidya.com
- Email: dhananjay@zitasoftinfotech.com

## 🙏 Credits

- OpenAI PHP Client
- Anthropic SDK
- Predis
- Monolog
- PHPMailer
- Symfony Console

---

**CWFramework 3.0** - Modern PHP for Modern Applications
