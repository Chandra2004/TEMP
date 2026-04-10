<?php

namespace TheFramework\Helpers;

use DateTime;
use DateTimeZone;
use TheFramework\App\Config;
use TheFramework\App\Database;

class Helper
{
    /**
     * Ensure session is started.
     * Delegates to SessionManager for consistent session handling.
     */
    private static function ensureSession()
    {
        \TheFramework\App\SessionManager::startSecureSession();
    }

    public static function url($path = '')
    {
        // Fix: Jika path adalah URL external/absolute, jangan tambahkan base url
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }

        $baseUrl = Config::get('BASE_URL') ?: '/';
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    public static function redirect($url, $status = null, $message = null, $duration = 10)
    {
        if ($status && $message) {
            $flashData = [
                'redirect' => $url,
                'status' => $status,
                'message' => $message
            ];

            if ($duration > 0) {
                $flashData['expires_at'] = time() + $duration;
                $flashData['duration'] = $duration * 1000;
            }

            self::set_flash('notification', $flashData);
        }

        if (self::is_ajax()) {
            self::json_redirect($url);
        } else {
            header("Location: " . self::url($url));
            exit();
        }
    }

    public static function redirectToNotFound()
    {
        header("Location: " . self::url('/404'));
        exit();
    }

    public static function request($key = null, $default = null)
    {
        $requestData = array_merge($_GET, $_POST);
        return new class ($requestData) {
            private $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function get($key = null, $default = null)
            {
                return $key === null ? $this->data : ($this->data[$key] ?? $default);
            }

            public function is($pattern)
            {
                $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $pattern = str_replace('*', '.*', $pattern);
                return preg_match("#^/?" . ltrim($pattern, '/') . "$#", $currentPath);
            }

            public function ip()
            {
                return Helper::get_client_ip();
            }
        };
    }

    public static function set_flash($key, $message)
    {
        self::ensureSession();
        $_SESSION[$key] = $message;
    }

    public static function get_flash($key)
    {
        self::ensureSession();

        if (!isset($_SESSION[$key])) {
            return null;
        }

        $data = $_SESSION[$key];

        if (isset($data['expires_at']) && time() > $data['expires_at']) {
            unset($_SESSION[$key]);
            return null;
        }

        unset($_SESSION[$key]);
        return $data;
    }

    public static function session_get($key, $default = null)
    {
        self::ensureSession();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Ambil validation errors dari session
     */
    public static function validation_errors(?string $field = null): array|string|null
    {
        self::ensureSession();
        $errors = $_SESSION['validation_errors'] ?? [];

        if ($field === null) {
            return $errors;
        }

        return $errors[$field] ?? null;
    }

    /**
     * Cek apakah ada validation error untuk field tertentu
     */
    public static function has_error(string $field): bool
    {
        return self::validation_errors($field) !== null;
    }

    /**
     * Ambil nilai input lama (old input) untuk form.
     * Digunakan agar user tidak perlu mengetik ulang jika validasi gagal.
     * 
     * @param string $field Nama field input
     * @param mixed $default Nilai default jika tidak ada data lama
     * @return mixed
     */
    public static function old(string $field, $default = null)
    {
        self::ensureSession();

        // Ambil data dari session 'old_input'
        $oldData = $_SESSION['old_input'] ?? [];

        // Kembalikan nilai field spesifik atau default
        return $oldData[$field] ?? $default;
    }

    public static function session_write($key, $value, $overwrite = false)
    {
        self::ensureSession();

        // 1. Secara otomatis mengubah objek menjadi array bersih untuk mencegah error serialisasi.
        if (is_object($value)) {
            $value = json_decode(json_encode($value), true);
        }

        if ($overwrite || !isset($_SESSION[$key])) {
            // 2. Langsung timpa jika diminta (overwrite=true) atau jika session belum ada.
            $_SESSION[$key] = $value;
        } else {
            // 3. Lakukan merge hanya jika nilai yang ada dan nilai yang baru KEDUANYA adalah array.
            if (is_array($_SESSION[$key]) && is_array($value)) {
                $_SESSION[$key] = array_merge($_SESSION[$key], $value);
            } else {
                // 4. Jika tidak, timpa saja untuk mencegah error tipe data.
                $_SESSION[$key] = $value;
            }
        }
    }


    public static function session_destroy_all()
    {
        self::ensureSession();
        session_unset();
        session_destroy();
    }



    public static function e($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public static function hash_password($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verify_password($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }

    public static function is_email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function current_date($format = 'Y-m-d H:i:s')
    {
        return date($format);
    }

    public static function rupiah($angka)
    {
        $angka = $angka ?? 0;
        return "Rp " . number_format((float) $angka, 0, ',', '.');
    }

    public static function random_string($length = 16)
    {
        return bin2hex(random_bytes(ceil($length / 2)));
    }

    public static function is_ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public static function json_redirect($url)
    {
        header('Content-Type: application/json');
        echo json_encode(['redirect' => self::url($url)]);
        exit();
    }

    public static function is_post()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function is_get()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    public static function get_client_ip()
    {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                return filter_var($_SERVER[$key], FILTER_VALIDATE_IP);
            }
        }
        return '0.0.0.0';
    }

    public static function is_csrf()
    {
        $result = false;
        if (self::is_post()) {
            $sessionServer = $_SESSION['csrf_token'];
            $sessionForm = $_POST['_token'] ?? '';

            if ($sessionServer === $sessionForm) {
                $result = true;
            }

            return $result;
        }
    }

    public static function is_submit($name, $decision)
    {
        return isset($_POST[$name]) && ($_POST[$name] === $decision) ? true : false;
    }

    public static function uuid(int $length = 36)
    {
        // Upgrade ke UUID v4 standar (128-bit, tapi potong jika length <128)
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Set version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Set variant
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        return substr($uuid, 0, $length); // Potong jika length lebih pendek
    }

    public static function generateSecureRegNumber(string $prefix = 'KSC')
    {
        $tick = dechex(intval(microtime(true) * 1000) & 0xFFFF);
        $entropy = bin2hex(random_bytes(4));

        return strtoupper($prefix . '-' . $tick . '-' . $entropy);
    }

    public static function updateAt()
    {
        $time = Config::get("DB_TIMEZONE") ?: 'UTC';
        try {
            $dt = new DateTime('now', new DateTimeZone($time));
        } catch (\Exception $e) {
            $dt = new DateTime('now', new DateTimeZone('UTC'));
        }
        return $dt->format('Y-m-d H:i:s');
    }

    public static function json($data = [], $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    // Fungsi baru: Generate CSRF token
    public static function generateCsrfToken()
    {
        self::ensureSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Fungsi baru: Validate CSRF token
    public static function validateCsrfToken($token)
    {
        self::ensureSession();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Fungsi baru: Sanitasi input recursive (untuk array input)
    public static function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        return trim(strip_tags($input));
    }



    // Fungsi baru: Generate pagination links
    public static function paginate($totalItems, $perPage, $currentPage, $baseUrl)
    {
        $totalPages = ceil($totalItems / $perPage);
        $links = [];

        for ($i = 1; $i <= $totalPages; $i++) {
            $links[] = [
                'page' => $i,
                'url' => $baseUrl . '?page=' . $i,
                'active' => $i == $currentPage
            ];
        }

        return $links;
    }



    // Fungsi baru: Generate slug untuk URL (misalnya news title ke slug)
    public static function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text);
    }

    // Fungsi baru: Check user role cepat
    public static function hasRole($role)
    {
        self::ensureSession();
        return isset($_SESSION['user']['role_name']) && $_SESSION['user']['role_name'] === $role;
    }

    public static function setAuthToken($value)
    {
        $_SESSION['auth_token'] = self::generateAuthToken($value);
    }

    public static function getAuthToken()
    {
        return $_SESSION['auth_token'] ?? null;
    }

    public static function generateAuthToken($value)
    {
        return hash('sha256', $value . Config::get('APP_KEY'));
    }

    public static function validateAuthToken($storedToken, $value)
    {
        return hash_equals($storedToken, self::generateAuthToken($value));
    }

    public static function authToken($data)
    {
        self::setAuthToken($data);
    }

    public static function getUriSegment(int $index): ?string
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', trim($path, '/'));
        $arrayIndex = $index - 1;
        return $pathParts[$arrayIndex] ?? null;
    }

    public static function renderToString(string $view, array $data = []): string|false
    {
        $viewPath = str_replace('.', '/', $view) . '.php';
        $fullPath = base_path('resources/views/' . $viewPath);
        if (!file_exists($fullPath)) {
            return false;
        }

        extract($data);
        ob_start();
        include $fullPath;
        return ob_get_clean();
    }

    /**
     * Get the URL of the previous request (the referer).
     * Provides a fallback if the referer is not available.
     *
     * @param string $fallback The fallback path if the referer is not set. Defaults to '/'.
     * @return string The full referer URL or the full fallback URL constructed from the base URL.
     */
    public static function previous(string $fallback = '/'): string
    {
        // If referer exists and is not empty, return it.
        if (!empty($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        }

        // Otherwise, construct the full fallback URL.
        return self::url($fallback);
    }
}

/**
 * Premium Debugging Utility
 * Dump and Die with elegance.
 */
if (!function_exists('dd')) {
    function dd(...$args)
    {
        if (PHP_SAPI === 'cli') {
            foreach ($args as $arg) {
                var_dump($arg);
            }
            die(1);
        }

        echo '
        <style>
            .tf-debug-container {
                background: #0f172a;
                color: #cbd5e1;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                font-size: 14px;
                padding: 24px;
                border-radius: 12px;
                margin: 20px;
                box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.5);
                border: 1px solid #1e293b;
                line-height: 1.6;
                overflow: hidden;
            }
            .tf-debug-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 20px;
                padding-bottom: 12px;
                border-bottom: 1px solid #1e293b;
            }
            .tf-debug-badge {
                background: #1e3a8a;
                color: #60a5fa;
                padding: 4px 12px;
                border-radius: 9999px;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .tf-debug-trace {
                font-size: 12px;
                color: #64748b;
            }
            .tf-debug-content {
                overflow-x: auto;
            }
            .tf-type-string { color: #38bdf8; }
            .tf-type-int { color: #f59e0b; }
            .tf-type-bool { color: #f472b6; }
            .tf-type-object { color: #818cf8; font-weight: bold; }
            .tf-type-array { color: #94a3b8; }
            .tf-key { color: #94a3b8; }
            .tf-arrow { color: #475569; margin: 0 8px; }
            pre { margin: 0; white-space: pre-wrap; word-break: break-all; }
            .tf-collapsed-toggle { 
                cursor: pointer; 
                user-select: none;
                display: inline-flex;
                align-items: center;
            }
            .tf-collapsed-toggle:hover { color: #f8fafc; }
        </style>
        ';

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $file = $trace['file'] ?? 'Unknown';
        $line = $trace['line'] ?? '0';

        echo '<div class="tf-debug-container">';
        echo '<div class="tf-debug-header">';
        echo '<span class="tf-debug-badge">THE FRAMEWORK v5.0 BUG HUNTER</span>';
        echo '<span class="tf-debug-trace">' . basename($file) . ':' . $line . '</span>';
        echo '</div>';
        echo '<div class="tf-debug-content">';

        foreach ($args as $arg) {
            echo '<pre>';
            tf_dump_recursive($arg);
            echo '</pre>';
        }

        echo '</div></div>';
        die(1);
    }
}

if (!function_exists('tf_dump_recursive')) {
    function tf_dump_recursive($var, $indent = 0)
    {
        $spacing = str_repeat('    ', $indent);
        
        if (is_null($var)) {
            echo '<span class="tf-type-bool">NULL</span>';
        } elseif (is_bool($var)) {
            echo '<span class="tf-type-bool">' . ($var ? 'true' : 'false') . '</span>';
        } elseif (is_int($var) || is_float($var)) {
            echo '<span class="tf-type-int">' . $var . '</span>';
        } elseif (is_string($var)) {
            echo '<span class="tf-type-string">"' . htmlspecialchars($var) . '"</span> <span style="font-size: 10px; color: #475569;">(' . strlen($var) . ')</span>';
        } elseif (is_array($var)) {
            $count = count($var);
            echo '<span class="tf-type-array">array:' . $count . '</span> [';
            if ($count > 0) {
                echo "\n";
                foreach ($var as $key => $val) {
                    echo $spacing . '    <span class="tf-key">' . (is_string($key) ? '"' . $key . '"' : $key) . '</span><span class="tf-arrow">=></span>';
                    tf_dump_recursive($val, $indent + 1);
                    echo "\n";
                }
                echo $spacing;
            }
            echo ']';
        } elseif (is_object($var)) {
            $class = get_class($var);
            echo '<span class="tf-type-object">' . $class . '</span> {';
            $props = (array)$var;
            if (!empty($props)) {
                echo "\n";
                foreach ($props as $key => $val) {
                    // Clean up private/protected property names
                    $key = str_replace("\0*\0", '(protected) ', $key);
                    $key = str_replace("\0" . $class . "\0", '(private) ', $key);
                    
                    echo $spacing . '    <span class="tf-key">' . $key . '</span><span class="tf-arrow">=></span>';
                    tf_dump_recursive($val, $indent + 1);
                    echo "\n";
                }
                echo $spacing;
            }
            echo '}';
        } else {
            var_dump($var);
        }
    }
}
