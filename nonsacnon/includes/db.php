<?php
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
  || (int)($_SERVER['SERVER_PORT'] ?? 0) === 443;
session_set_cookie_params([
  'httponly' => true,
  'secure' => $isHttps,
  'samesite' => 'Lax'
]);
session_start();
$c = require __DIR__.'/config.php';
try {
  $pdo = new PDO("mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4", $c['user'], $c['pass'],
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
} catch (PDOException $ex) { http_response_code(500); exit('Chưa kết nối được CSDL. Hãy import database.sql bằng phpMyAdmin.'); }
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csrf(){ return $_SESSION['t'] ??= bin2hex(random_bytes(16)); }
function check_post(array &$errors){
  if (!hash_equals($_SESSION['t'] ?? '', $_POST['t'] ?? '')) $errors[] = 'Phiên làm việc hết hạn, hãy tải lại trang.';
  if (!empty($_POST['website'])) exit; // ô bẫy bot
  if (time() - ($_SESSION['last'] ?? 0) < 8) $errors[] = 'Bạn gửi hơi nhanh, hãy thử lại sau vài giây.';
}
require_once __DIR__.'/view.php';
require_once __DIR__.'/projects.php';
