<?php // Chỉ chạy được khi chưa có tài khoản nào: tạo quản trị viên đầu tiên.
require __DIR__.'/../includes/auth.php';
if((int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn()>0){ http_response_code(404); exit('Đã thiết lập.'); }
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  post_guard(); $n=trim($_POST['name']??''); $m=trim($_POST['email']??''); $p=$_POST['password']??'';
  if($n===''||!filter_var($m,FILTER_VALIDATE_EMAIL)||strlen($p)<8) $err='Cần đủ họ tên, email hợp lệ và mật khẩu từ 8 ký tự.';
  else { $pdo->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?, 'admin')")->execute([$n,$m,password_hash($p,PASSWORD_DEFAULT)]); header('Location: login.php'); exit; }
} ?>
<!doctype html><html lang="vi"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/><title>Thiết lập</title><link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Cormorant+Garamond:wght@500;600&display=swap" rel="stylesheet"/><link rel="stylesheet" href="../assets/css/base.css?v=compact-1"/></head><body>
<main class="page"><div class="wrap"><h1>Tạo quản trị viên</h1><?php if($err) echo '<p class="note err">'.e($err).'</p>'; ?>
<form class="form" method="post"><input type="hidden" name="t" value="<?= e(csrf()) ?>"/>
<label>Họ tên<input name="name" required/></label><label>Email<input name="email" type="email" required/></label><label>Mật khẩu (từ 8 ký tự)<input name="password" type="password" required/></label>
<button class="btn" type="submit">Tạo tài khoản <span>→</span></button></form></div></main></body></html>
