<?php

declare(strict_types=1);

namespace App\Controllers;

use CWF\Http\Request;
use CWF\Http\Response;
use CWF\Studio\StudioManager;

/**
 * Studio Controller - Web interface for framework management
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class StudioController
{
    public function __construct(
        private readonly StudioManager $studio
    ) {
    }

    /**
     * Studio dashboard
     */
    public function index(Request $request): Response
    {
        if (!$this->checkAuth($request)) {
            return redirect('/studio/login');
        }

        $stats = $this->studio->getStatistics();
        $activity = $this->studio->getRecentActivity(10);

        return view('studio/dashboard', compact('stats', 'activity'));
    }

    /**
     * Login page
     */
    public function login(Request $request): Response
    {
        if ($request->method() === 'POST') {
            $username = $request->input('username');
            $password = $request->input('password');

            if ($this->studio->authenticate($username, $password)) {
                session(['studio_authenticated' => true, 'studio_user' => $username]);
                return redirect('/studio');
            }

            return view('studio/login', ['error' => 'Invalid credentials']);
        }

        return view('studio/login');
    }

    /**
     * Logout
     */
    public function logout(Request $request): Response
    {
        session(['studio_authenticated' => false]);
        return redirect('/studio/login');
    }

    /**
     * Features management
     */
    public function features(Request $request): Response
    {
        if (!$this->checkAuth($request)) {
            return redirect('/studio/login');
        }

        $features = $this->studio->features();

        if ($request->method() === 'POST') {
            $action = $request->input('action');
            $feature = $request->input('feature');

            try {
                if ($action === 'enable') {
                    $features->enable($feature);
                } elseif ($action === 'disable') {
                    $features->disable($feature);
                }

                return json(['success' => true]);

            } catch (\Exception $e) {
                return json(['success' => false, 'error' => $e->getMessage()], 400);
            }
        }

        $categories = $features->categories();

        return view('studio/features', compact('categories'));
    }

    /**
     * Code generator
     */
    public function generator(Request $request): Response
    {
        if (!$this->checkAuth($request)) {
            return redirect('/studio/login');
        }

        if ($request->method() === 'POST') {
            $type = $request->input('type');
            $config = $request->all();

            try {
                $generator = $this->studio->generator();

                $result = match($type) {
                    'controller' => $generator->generateController($config),
                    'model' => $generator->generateModel($config),
                    'middleware' => $generator->generateMiddleware($config),
                    'crud' => $generator->generateCrud($config),
                    'api' => $generator->generateApiEndpoint($config),
                    default => throw new \InvalidArgumentException('Invalid type')
                };

                return json(['success' => true, 'result' => $result]);

            } catch (\Exception $e) {
                return json(['success' => false, 'error' => $e->getMessage()], 400);
            }
        }

        return view('studio/generator');
    }

    /**
     * AI assistant
     */
    public function aiAssistant(Request $request): Response
    {
        if (!$this->checkAuth($request)) {
            return json(['error' => 'Unauthorized'], 401);
        }

        if ($request->method() === 'POST') {
            $prompt = $request->input('prompt');
            $type = $request->input('type', 'chat');

            try {
                if ($type === 'generate') {
                    $generator = $this->studio->generator();
                    $code = $generator->generateWithAI([
                        'type' => $request->input('code_type'),
                        'description' => $prompt
                    ]);

                    return json(['success' => true, 'code' => $code]);

                } else {
                    $response = ai()->chat([
                        ['role' => 'system', 'content' => 'You are a CWFramework expert assistant. Help developers with code, architecture, and best practices.'],
                        ['role' => 'user', 'content' => $prompt]
                    ]);

                    return json([
                        'success' => true,
                        'response' => $response->getContent(),
                        'usage' => $response->getUsage()
                    ]);
                }

            } catch (\Exception $e) {
                return json(['success' => false, 'error' => $e->getMessage()], 400);
            }
        }

        return view('studio/ai-assistant');
    }

    /**
     * Build system
     */
    public function build(Request $request): Response
    {
        if (!$this->checkAuth($request)) {
            return redirect('/studio/login');
        }

        if ($request->method() === 'POST') {
            $config = $request->all();

            try {
                $builder = $this->studio->builder();
                $result = $builder->build($config);

                return json(['success' => true, 'build' => $result]);

            } catch (\Exception $e) {
                return json(['success' => false, 'error' => $e->getMessage()], 400);
            }
        }

        $stats = $this->studio->builder()->getStats();

        return view('studio/build', compact('stats'));
    }

    /**
     * Plugin marketplace
     */
    public function plugins(Request $request): Response
    {
        if (!$this->checkAuth($request)) {
            return redirect('/studio/login');
        }

        $plugins = $this->studio->plugins();

        if ($request->method() === 'POST') {
            $action = $request->input('action');
            $slug = $request->input('slug');

            try {
                $result = match($action) {
                    'install' => $plugins->install($slug),
                    'uninstall' => $plugins->uninstall($slug),
                    'activate' => $plugins->activate($slug),
                    'deactivate' => $plugins->deactivate($slug),
                    default => false
                };

                return json(['success' => $result]);

            } catch (\Exception $e) {
                return json(['success' => false, 'error' => $e->getMessage()], 400);
            }
        }

        $installed = $plugins->all();
        $featured = $plugins->featured();

        return view('studio/plugins', compact('installed', 'featured'));
    }

    /**
     * Database manager
     */
    public function database(Request $request): Response
    {
        if (!$this->checkAuth($request)) {
            return redirect('/studio/login');
        }

        // Get tables
        $tables = db()->raw("SHOW TABLES");

        return view('studio/database', compact('tables'));
    }

    /**
     * API tester
     */
    public function apiTester(Request $request): Response
    {
        if (!$this->checkAuth($request)) {
            return redirect('/studio/login');
        }

        if ($request->method() === 'POST') {
            $url = $request->input('url');
            $method = $request->input('method', 'GET');
            $headers = $request->input('headers', []);
            $body = $request->input('body');

            try {
                $client = new \GuzzleHttp\Client();
                $response = $client->request($method, $url, [
                    'headers' => $headers,
                    'body' => $body,
                ]);

                return json([
                    'status' => $response->getStatusCode(),
                    'headers' => $response->getHeaders(),
                    'body' => (string) $response->getBody(),
                ]);

            } catch (\Exception $e) {
                return json(['error' => $e->getMessage()], 500);
            }
        }

        return view('studio/api-tester');
    }

    /**
     * Log viewer
     */
    public function logs(Request $request): Response
    {
        if (!$this->checkAuth($request)) {
            return redirect('/studio/login');
        }

        $logFile = app()->storagePath('logs/app.log');
        $logs = [];

        if (file_exists($logFile)) {
            $lines = file($logFile);
            $logs = array_slice(array_reverse($lines), 0, 100);
        }

        return view('studio/logs', compact('logs'));
    }

    /**
     * Settings
     */
    public function settings(Request $request): Response
    {
        if (!$this->checkAuth($request)) {
            return redirect('/studio/login');
        }

        if ($request->method() === 'POST') {
            $action = $request->input('action');

            if ($action === 'export') {
                $config = $this->studio->exportConfig();
                return Response::download($config, 'studio-config.json');

            } elseif ($action === 'import') {
                $file = $request->file('config');
                $config = file_get_contents($file['tmp_name']);
                $this->studio->importConfig($config);

                return json(['success' => true]);
            }
        }

        return view('studio/settings');
    }

    /**
     * Check authentication
     */
    private function checkAuth(Request $request): bool
    {
        if (!$this->studio->isEnabled) {
            return false;
        }

        if (!$this->studio->isIpAllowed($request->ip())) {
            return false;
        }

        return session('studio_authenticated', false);
    }
}
