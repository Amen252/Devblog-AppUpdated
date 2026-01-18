<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/devblog', '', $uri);

if ($uri === '/' || $uri === '') {
    require_once __DIR__ . '/../views/index.php';
    exit;
}

if ($uri === '/admin/dashboard') {
    require_once __DIR__ . '/../views/admin/dashboard.php';
    exit;
}

http_response_code(404);
echo "404 Page Not Found";
