<?php

use TheFramework\Helpers\Helper;

if (!function_exists('url')) {
    function url($path = '')
    {
        return Helper::url($path);
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and Die - Premium Version
     */
    function dd(...$vars)
    {
        if (php_sapi_name() === 'cli') {
            foreach ($vars as $v)
                var_dump($v);
            die();
        }

        echo '<div style="background:#111; color:#0f0; padding:20px; font-family:monospace; font-size:13px; border-left:5px solid #ff4444; margin:10px; border-radius:4px; box-shadow:0 10px 30px rgba(0,0,0,0.5); overflow:auto; max-height:800px;">';
        echo '<div style="color:#666; margin-bottom:10px; font-size:10px;">THE FRAMEWORK v5.0 BUG HUNTER</div>';
        foreach ($vars as $var) {
            echo '<pre style="white-space:pre-wrap;">';
            var_dump($var);
            echo '</pre>';
            echo '<hr style="border:0; border-top:1px solid #333; margin:10px 0;">';
        }
        $trace = debug_backtrace()[0];
        echo '<div style="color:#fff; background:#333; display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px;">Called in: ' . $trace['file'] . ' on line ' . $trace['line'] . '</div>';
        echo '</div>';
        die();
    }
}


if (!function_exists('redirect')) {
    function redirect($url, $status = null, $message = null)
    {
        Helper::redirect($url, $status, $message);
    }
}

if (!function_exists('request')) {
    function request($key = null, $default = null)
    {
        return Helper::request($key, $default);
    }
}

if (!function_exists('set_flash')) {
    function set_flash($key, $message)
    {
        Helper::set_flash($key, $message);
    }
}

if (!function_exists('get_flash')) {
    function get_flash($key)
    {
        return Helper::get_flash($key);
    }
}

if (!function_exists('uuid')) {
    function uuid(int $length = 36)
    {
        return Helper::uuid($length);
    }
}

if (!function_exists('updateAt')) {
    function updateAt()
    {
        return Helper::updateAt();
    }
}

if (!function_exists('rupiah')) {
    function rupiah($number)
    {
        return Helper::rupiah($number);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        return Helper::generateCsrfToken();
    }
}

if (!function_exists('e')) {
    function e($value)
    {
        return Helper::e($value);
    }
}

if (!function_exists('old')) {
    function old($field, $default = null)
    {
        return Helper::old($field, $default);
    }
}

if (!function_exists('error')) {
    function error($field)
    {
        return Helper::validation_errors($field);
    }
}

if (!function_exists('has_error')) {
    function has_error($field)
    {
        return Helper::has_error($field);
    }
}

if (!function_exists('dispatch')) {
    function dispatch($job, $queue = 'default')
    {
        return \TheFramework\App\Queue::push($job, [], $queue);
    }
}

if (!function_exists('__')) {
    function __($key, $replace = [], $locale = null)
    {
        return \TheFramework\App\Lang::get($key, $replace, $locale);
    }
}

if (!function_exists('trans')) {
    function trans($key, $replace = [], $locale = null)
    {
        return \TheFramework\App\Lang::get($key, $replace, $locale);
    }
}
if (!function_exists('base_path')) {
    function base_path($path = '')
    {
        return ROOT_DIR . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }
}

if (!function_exists('storage_path')) {
    function storage_path($path = '')
    {
        return base_path('storage' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (!function_exists('current_url')) {
    function current_url()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and Die
     * Format output yang rapi dan bergaya untuk debugging.
     */
    function dd(...$vars)
    {
        if (php_sapi_name() !== 'cli') {
            echo '<style>
                .dd-container { background-color: #18171B; color: #FF8400; padding: 20px; font-family: "Fira Code", Consolas, monospace; font-size: 14px; border-radius: 8px; margin: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); line-height: 1.5; overflow: auto; max-height: 90vh; }
                .dd-header { border-bottom: 1px solid #333; margin-bottom: 15px; padding-bottom: 10px; color: #fff; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
                .dd-tag { background: #FF8400; color: #000; padding: 2px 8px; border-radius: 4px; font-size: 11px; text-transform: uppercase; }
                pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }
                .dd-footer { font-size: 11px; color: #666; margin-top: 15px; text-align: right; }
            </style>';
            echo '<div class="dd-container">';
            echo '<div class="dd-header">DEBUG OUTPUT <span class="dd-tag">TheFramework v5.0.1</span></div>';
            foreach ($vars as $var) {
                echo '<pre>';
                if (is_array($var) || is_object($var)) {
                    echo htmlspecialchars(print_r($var, true));
                } elseif (is_bool($var)) {
                    echo $var ? '<em>(bool)</em> true' : '<em>(bool)</em> false';
                } elseif (is_null($var)) {
                    echo '<em>null</em>';
                } else {
                    var_dump($var);
                }
                echo '</pre><hr style="border: 0; border-top: 1px solid #333; margin: 15px 0;">';
            }
            echo '<div class="dd-footer">Location: ' . debug_backtrace()[0]['file'] . ':' . debug_backtrace()[0]['line'] . '</div>';
            echo '</div>';
        } else {
            foreach ($vars as $var) {
                var_dump($var);
            }
        }
        die();
    }

    if (!function_exists('previous')) {
        /**
         * Get the URL of the previous request or a fallback URL.
         *
         * @param string $fallback The fallback path if the referer is not set.
         * @return string
         */
        function previous(string $fallback = '/')
        {
            return Helper::previous($fallback);
        }
    }
    
}
