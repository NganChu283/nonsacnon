<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/_icons.php';
require_once __DIR__.'/_errors.php';
function admin_head($title,$perm){ require_perm($perm); $u=user(); $t=csrf();
  $nav=[['index.php','Tổng quan','dash'],['admin.php','Quản trị hệ thống','admin'],['editor.php','Biên tập nội dung','editor'],['coordinator.php','Điều phối','organizer'],['stories.php','Bài viết','stories'],['projects.php','Dự án','projects'],['gallery.php','Thư viện ảnh','gallery'],['workshops.php','Workshop','workshops'],['messages.php','Tin nhắn','messages'],['users.php','Người dùng','users']]; ?>
<!doctype html><html lang="vi"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/><meta name="robots" content="noindex"/>
<title><?= e($title) ?> – Quản trị</title><link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Cormorant+Garamond:wght@500;600&display=swap" rel="stylesheet"/><link rel="stylesheet" href="../assets/css/admin.css?v=compact-1"/><script src="../assets/js/admin.js?v=compact-1" defer></script></head><body class="admin-body">
<a class="admin-skip" href="#admin-content">Đến nội dung chính</a>
<div class="adm"><aside id="admin-sidebar" aria-label="Menu quản trị"><div class="admin-sidebar-head"><a class="logo admin-logo" href="../index.php" aria-label="Non Sắc Nón – Trang chủ"><img src="../assets/img/non-sac-non-logo.jpg?v=2" alt="Logo Non Sắc Nón" width="88" height="88"/></a><span class="admin-brand-copy">NON SẮC NÓN<small>Không gian quản trị</small></span></div><p class="who"><span class="admin-avatar" aria-hidden="true"><?= e(mb_substr($u['name'],0,1)) ?></span><span><?= e($u['name']) ?><small><?= e(ROLES[$u['role']]) ?></small></span></p>
<nav aria-label="Các mục quản trị"><?php
  $current = basename($_SERVER['SCRIPT_NAME']);
  if ($current === 'story_edit.php') $current = 'stories.php';
  $lastGroup='';
  foreach($nav as $i=>$n) if(can($n[2])) {
    $group=$i<4?'Không gian':($i<7?'Nội dung':'Cộng đồng');
    if($group!==$lastGroup){echo '<p class="admin-nav-label">'.e($group).'</p>';$lastGroup=$group;}
    echo '<a href="'.$n[0].'"'.($current === $n[0] ? ' aria-current="page"' : '').'>'.admin_icon(basename($n[0],'.php')).'<span>'.$n[1].'</span></a>';
  }
?><a class="home-link" href="../index.php"><?= admin_icon('index') ?><span>Về trang chủ</span></a><a class="admin-logout" href="logout.php"><?= admin_icon('logout') ?><span>Đăng xuất</span></a></nav></aside>
<div class="admin-workspace"><div class="admin-topbar"><div class="admin-menu-toolbar"><button type="button" data-admin-menu-toggle aria-controls="admin-sidebar" aria-expanded="true" aria-label="Ẩn menu" title="Ẩn menu"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button></div><p class="admin-breadcrumb">Non Sắc Nón <span>/</span> <?= e($title) ?></p><a class="admin-website-link" href="../index.php" target="_blank" rel="noopener noreferrer">Xem website ↗</a></div>
<main id="admin-content" tabindex="-1"><div class="admin-page-heading"><p class="admin-eyebrow">Không gian quản trị</p><h1><?= e($title) ?></h1></div>
<?php } function admin_foot(){ echo '</main></div></div></body></html>'; }
function csrf_field(){ return '<input type="hidden" name="t" value="'.e(csrf()).'"/>'; }
