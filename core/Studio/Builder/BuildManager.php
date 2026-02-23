<?php

declare(strict_types=1);

namespace CWF\Studio\Builder;

use CWF\Application;

/**
 * Build and Encryption Manager
 *
 * Compile, minify, obfuscate, and encrypt PHP code for distribution
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class BuildManager
{
    private int $buildsCreated = 0;

    public function __construct(
        private readonly Application $app
    ) {
    }

    /**
     * Create a production build
     */
    public function build(array $config = []): array
    {
        $buildId = uniqid('build_');
        $buildDir = $this->app->storagePath("builds/{$buildId}");

        if (!is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
        }

        $steps = [];

        // Step 1: Copy files
        $steps['copy'] = $this->copyFiles($buildDir, $config);

        // Step 2: Optimize autoloader
        $steps['optimize'] = $this->optimizeAutoloader($buildDir);

        // Step 3: Minify code (optional)
        if ($config['minify'] ?? true) {
            $steps['minify'] = $this->minifyCode($buildDir);
        }

        // Step 4: Obfuscate code (optional)
        if ($config['obfuscate'] ?? false) {
            $steps['obfuscate'] = $this->obfuscateCode($buildDir);
        }

        // Step 5: Encrypt code (optional)
        if ($config['encrypt'] ?? false) {
            $steps['encrypt'] = $this->encryptCode($buildDir, $config);
        }

        // Step 6: Generate license file
        if ($config['license'] ?? false) {
            $steps['license'] = $this->generateLicense($buildDir, $config);
        }

        // Step 7: Create archive
        $steps['archive'] = $this->createArchive($buildDir, $buildId);

        $this->buildsCreated++;

        app('studio')->logActivity('build_created', [
            'build_id' => $buildId,
            'steps' => array_keys($steps)
        ]);

        return [
            'build_id' => $buildId,
            'build_path' => $buildDir,
            'archive' => $steps['archive'],
            'steps' => $steps,
            'timestamp' => time(),
        ];
    }

    /**
     * Copy application files to build directory
     */
    private function copyFiles(string $buildDir, array $config): array
    {
        $excludeDirs = $config['exclude'] ?? [
            'storage/logs',
            'storage/cache',
            'storage/builds',
            'storage/studio',
            '.git',
            'node_modules',
            'tests',
        ];

        $sourceDir = $this->app->basePath;
        $copied = 0;

        $this->recursiveCopy($sourceDir, $buildDir, $excludeDirs, $copied);

        return [
            'files_copied' => $copied,
            'build_dir' => $buildDir
        ];
    }

    /**
     * Recursively copy files
     */
    private function recursiveCopy(string $src, string $dst, array $exclude, int &$count): void
    {
        $dir = opendir($src);

        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;

            // Check if excluded
            $isExcluded = false;
            foreach ($exclude as $excludePath) {
                if (str_contains($srcPath, $excludePath)) {
                    $isExcluded = true;
                    break;
                }
            }

            if ($isExcluded) {
                continue;
            }

            if (is_dir($srcPath)) {
                $this->recursiveCopy($srcPath, $dstPath, $exclude, $count);
            } else {
                copy($srcPath, $dstPath);
                $count++;
            }
        }

        closedir($dir);
    }

    /**
     * Optimize Composer autoloader
     */
    private function optimizeAutoloader(string $buildDir): array
    {
        $command = "cd {$buildDir} && composer install --no-dev --optimize-autoloader 2>&1";
        exec($command, $output, $return);

        return [
            'success' => $return === 0,
            'output' => implode("\n", $output)
        ];
    }

    /**
     * Minify PHP code
     */
    private function minifyCode(string $buildDir): array
    {
        $files = $this->getPhpFiles($buildDir);
        $minified = 0;
        $savedBytes = 0;

        foreach ($files as $file) {
            $originalSize = filesize($file);
            $code = file_get_contents($file);

            // Remove comments and extra whitespace
            $minifiedCode = $this->minify($code);

            file_put_contents($file, $minifiedCode);
            $newSize = filesize($file);

            $savedBytes += ($originalSize - $newSize);
            $minified++;
        }

        return [
            'files_minified' => $minified,
            'bytes_saved' => $savedBytes,
            'saved_kb' => round($savedBytes / 1024, 2)
        ];
    }

    /**
     * Minify single PHP file
     */
    private function minify(string $code): string
    {
        // Remove comments
        $code = preg_replace('!/\*.*?\*/!s', '', $code);
        $code = preg_replace('/\n\s*\n/', "\n", $code);
        $code = preg_replace('/^[\s]+/m', '', $code);

        // Remove line comments but preserve URLs
        $lines = explode("\n", $code);
        $cleaned = [];

        foreach ($lines as $line) {
            // Skip if line is just a comment
            if (preg_match('/^\s*\/\//', $line)) {
                continue;
            }
            $cleaned[] = $line;
        }

        return implode("\n", $cleaned);
    }

    /**
     * Obfuscate PHP code (basic obfuscation)
     */
    private function obfuscateCode(string $buildDir): array
    {
        $files = $this->getPhpFiles($buildDir);
        $obfuscated = 0;

        foreach ($files as $file) {
            $code = file_get_contents($file);

            // Don't obfuscate framework core files
            if (str_contains($file, '/core/')) {
                continue;
            }

            $obfuscatedCode = $this->obfuscate($code);
            file_put_contents($file, $obfuscatedCode);
            $obfuscated++;
        }

        return [
            'files_obfuscated' => $obfuscated
        ];
    }

    /**
     * Basic obfuscation (rename variables)
     */
    private function obfuscate(string $code): string
    {
        // This is a simple example. For production, use proper obfuscation tools
        // like yakpro-po or commercial solutions

        // Generate variable name map
        static $varMap = [];

        // Find all variables
        preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)/', $code, $matches);
        $variables = array_unique($matches[1]);

        // Exclude common variables
        $exclude = ['this', 'app', 'request', 'response', '_GET', '_POST', '_SERVER', '_SESSION'];

        foreach ($variables as $var) {
            if (in_array($var, $exclude)) {
                continue;
            }

            if (!isset($varMap[$var])) {
                $varMap[$var] = 'v' . bin2hex(random_bytes(4));
            }

            $code = preg_replace('/\$' . $var . '\b/', '$' . $varMap[$var], $code);
        }

        return $code;
    }

    /**
     * Encrypt PHP code using ionCube or similar
     */
    private function encryptCode(string $buildDir, array $config): array
    {
        $method = $config['encryption_method'] ?? 'base64'; // base64, ioncube, sourceguardian

        switch ($method) {
            case 'ioncube':
                return $this->encryptWithIonCube($buildDir);

            case 'sourceguardian':
                return $this->encryptWithSourceGuardian($buildDir);

            case 'base64':
            default:
                return $this->encryptWithBase64($buildDir);
        }
    }

    /**
     * Simple Base64 encryption with eval wrapper
     */
    private function encryptWithBase64(string $buildDir): array
    {
        $files = $this->getPhpFiles($buildDir);
        $encrypted = 0;

        foreach ($files as $file) {
            // Skip index.php and bootstrap files
            if (basename($file) === 'index.php' || basename($file) === 'bootstrap.php') {
                continue;
            }

            $code = file_get_contents($file);

            // Remove PHP tags
            $code = str_replace(['<?php', '?>'], '', $code);

            // Encode
            $encoded = base64_encode(gzcompress($code, 9));

            // Create wrapper
            $wrapper = "<?php\n";
            $wrapper .= "// CWFramework Protected File\n";
            $wrapper .= "// Build Date: " . date('Y-m-d H:i:s') . "\n";
            $wrapper .= "eval(gzuncompress(base64_decode('{$encoded}')));\n";

            file_put_contents($file, $wrapper);
            $encrypted++;
        }

        return [
            'method' => 'base64',
            'files_encrypted' => $encrypted,
            'note' => 'Basic encryption. For production, use ionCube or SourceGuardian'
        ];
    }

    /**
     * Encrypt with ionCube (requires ionCube encoder installed)
     */
    private function encryptWithIonCube(string $buildDir): array
    {
        if (!$this->isIonCubeAvailable()) {
            return [
                'error' => 'ionCube encoder not found. Install from https://www.ioncube.com/'
            ];
        }

        $command = "ioncube_encoder {$buildDir} -o {$buildDir}_encrypted";
        exec($command, $output, $return);

        return [
            'method' => 'ioncube',
            'success' => $return === 0,
            'output' => implode("\n", $output)
        ];
    }

    /**
     * Encrypt with SourceGuardian
     */
    private function encryptWithSourceGuardian(string $buildDir): array
    {
        return [
            'error' => 'SourceGuardian integration coming soon'
        ];
    }

    /**
     * Generate license file
     */
    private function generateLicense(string $buildDir, array $config): array
    {
        $licenseKey = $config['license_key'] ?? $this->generateLicenseKey();
        $expiryDate = $config['expiry_date'] ?? null;
        $domain = $config['domain'] ?? null;

        $license = [
            'license_key' => $licenseKey,
            'product' => 'CWFramework Application',
            'version' => '3.0.0',
            'issued_at' => date('Y-m-d H:i:s'),
            'expires_at' => $expiryDate,
            'domain' => $domain,
            'signature' => hash('sha256', $licenseKey . ($domain ?? '') . 'cwf_secret'),
        ];

        file_put_contents(
            $buildDir . '/LICENSE.json',
            json_encode($license, JSON_PRETTY_PRINT)
        );

        // Create license checker file
        $checker = $this->getLicenseCheckerCode();
        file_put_contents($buildDir . '/license_check.php', $checker);

        return $license;
    }

    /**
     * Generate unique license key
     */
    private function generateLicenseKey(): string
    {
        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $segments[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return implode('-', $segments);
    }

    /**
     * Get license checker code
     */
    private function getLicenseCheckerCode(): string
    {
        return <<<'PHP'
<?php

function checkLicense() {
    $licenseFile = __DIR__ . '/LICENSE.json';

    if (!file_exists($licenseFile)) {
        die('License file not found');
    }

    $license = json_decode(file_get_contents($licenseFile), true);

    // Check expiry
    if ($license['expires_at'] && strtotime($license['expires_at']) < time()) {
        die('License expired');
    }

    // Check domain (if specified)
    if ($license['domain']) {
        $currentDomain = $_SERVER['HTTP_HOST'] ?? '';
        if ($currentDomain !== $license['domain']) {
            die('License domain mismatch');
        }
    }

    // Verify signature
    $expectedSignature = hash('sha256', $license['license_key'] . ($license['domain'] ?? '') . 'cwf_secret');
    if ($license['signature'] !== $expectedSignature) {
        die('License signature invalid');
    }

    return true;
}

checkLicense();
PHP;
    }

    /**
     * Create archive of build
     */
    private function createArchive(string $buildDir, string $buildId): string
    {
        $archivePath = $this->app->storagePath("builds/{$buildId}.zip");

        $zip = new \ZipArchive();
        if ($zip->open($archivePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create archive');
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($buildDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($buildDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return $archivePath;
    }

    /**
     * Get all PHP files in directory
     */
    private function getPhpFiles(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Check if ionCube is available
     */
    private function isIonCubeAvailable(): bool
    {
        exec('which ioncube_encoder', $output, $return);
        return $return === 0;
    }

    /**
     * Get build statistics
     */
    public function getStats(): array
    {
        $buildsDir = $this->app->storagePath('builds');

        if (!is_dir($buildsDir)) {
            return ['total_builds' => 0];
        }

        $builds = glob($buildsDir . '/*.zip');

        return [
            'total_builds' => count($builds),
            'total_size_mb' => array_sum(array_map('filesize', $builds)) / 1024 / 1024,
            'latest_build' => !empty($builds) ? basename($builds[0], '.zip') : null,
        ];
    }
}
