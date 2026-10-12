<?php
require __DIR__.'/../includes/auth.php';
if(user()){ header('Location: index.php'); exit; }
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  post_guard();
  if(($_SESSION['fails']??0)>=5 && time()-($_SESSION['failt']??0)<60) $err='Thử sai quá nhiều lần, hãy đợi 1 phút.';
  else {
    $s=$pdo->prepare('SELECT * FROM users WHERE email=? AND is_active=1'); $s->execute([trim($_POST['email']??'')]); $u=$s->fetch();
    if($u && password_verify($_POST['password']??'',$u['password_hash'])){
      session_regenerate_id(true); $_SESSION['u']=['id'=>$u['id'],'name'=>$u['name'],'role'=>$u['role']]; $_SESSION['fails']=0; header('Location: index.php'); exit;
    }
    $_SESSION['fails']=($_SESSION['fails']??0)+1; $_SESSION['failt']=time(); $err='Email hoặc mật khẩu chưa đúng.';
  }
}
$pageTitle='Đăng nhập quản trị'; $demoMode=false; ?>
<!doctype html><html lang="vi"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/><meta name="robots" content="noindex"/><title>Đăng nhập quản trị</title><link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&family=Cormorant+Garamond:wght@600&display=swap" rel="stylesheet"/><link rel="stylesheet" href="../assets/css/login.css?v=compact-1"/></head><body class="login-page" data-demo="<?= $demoMode?'true':'false' ?>">
<main class="login-shell" style="--login-photo:url('../assets/uploads/non-sac-non/hero-non-sac-non-bg.jpg')">
  <section class="login-intro" aria-label="Giới thiệu Non Sắc Nón">
    <a class="login-logo" href="../index.php"><img src="../assets/img/non-sac-non-logo.jpg?v=2" alt="Logo Non Sắc Nón" width="120" height="120"/></a>
    <div class="login-copy">
      <p class="eyebrow">Khu vực quản trị</p>
      <h1><span>Giữ nghề,</span><span>kể chuyện,</span><span>trao di sản.</span></h1>
      <div class="role-pills" aria-label="Các vai trò quản trị"><span><i class="role-admin"></i>Quản trị viên</span><span><i class="role-editor"></i>Biên tập viên</span><span><i class="role-organizer"></i>Điều phối workshop</span></div>
    </div>
    <p class="login-footnote">Nón xưa · Họa nay</p>
  </section>
  <section class="login-panel">
    <a class="home-link" href="../index.php"><span aria-hidden="true">←</span> Về trang chủ</a>
    <div class="login-card">
      <p class="eyebrow">NON SẮC NÓN</p><h2>Đăng nhập</h2><p class="login-subtitle">Chào mừng trở lại.</p>
      <form class="login-form" method="post" id="loginForm"><?= '<input type="hidden" name="t" value="'.e(csrf()).'"/>' ?>
        <label for="email">Địa chỉ email<input id="email" name="email" type="email" autocomplete="username" placeholder="ten@nonsacnon.vn" required autofocus/></label>
        <label for="password">Mật khẩu<div class="password-wrap"><input id="password" name="password" type="password" autocomplete="current-password" placeholder="Nhập mật khẩu" required/><button class="password-toggle" type="button" aria-label="Hiện mật khẩu" aria-pressed="false"><svg class="eye-open" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg><svg class="eye-closed" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6.5 0 10 6 10 6a18 18 0 0 1-3.1 3.8M6.2 6.8C3.4 8.5 2 12 2 12s3.5 6 10 6a10 10 0 0 0 3.4-.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg></button></div></label>
        <div class="form-options"><label class="remember"><input type="checkbox" name="remember"/><span>Ghi nhớ đăng nhập</span></label><button class="forgot-link" type="button" aria-expanded="false">Quên mật khẩu?</button></div>
        <p class="forgot-message" id="forgotMessage" role="status" hidden>Vui lòng liên hệ quản trị viên để đặt lại mật khẩu.</p>
        <?php if($err) echo '<p class="login-error" role="alert" aria-live="assertive">'.e($err).'</p>'; ?>
        <button class="login-submit" type="submit"><span class="submit-label">Đăng nhập</span><span class="submit-arrow" aria-hidden="true">→</span><span class="submit-loading" aria-hidden="true">Đang xử lý...</span></button>
      </form>
      <div class="demo-area" aria-hidden="true">
        <div class="demo-divider"><span>hoặc xem nhanh theo vai trò (bản demo)</span></div>
        <div class="demo-buttons"><button type="button" data-email="admin@nonsacnon.vn" data-role="Quản trị">⌂<span>Quản trị</span></button><button type="button" data-email="editor@nonsacnon.vn" data-role="Biên tập">✎<span>Biên tập</span></button><button type="button" data-email="workshop@nonsacnon.vn" data-role="Điều phối">◌<span>Điều phối</span></button></div>
      </div>
      <p class="login-contact">Chưa có tài khoản? <span>Liên hệ quản trị viên.</span></p><p class="login-demo-note">Bản demo – không có dữ liệu nào được gửi đi.</p>
    </div>
  </section>
</main><script src="../assets/js/login.js" defer></script></body></html>
