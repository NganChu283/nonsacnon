<?php
require __DIR__.'/includes/db.php';
$pageTitle='Liên hệ – Non Sắc Nón'; $errors=[]; $done=false;
$v=['name'=>'','email'=>'','phone'=>'','message'=>''];
if ($_SERVER['REQUEST_METHOD']==='POST') {
  check_post($errors);
  foreach ($v as $k=>$_) $v[$k]=trim($_POST[$k] ?? '');
  if ($v['name']==='') $errors[]='Vui lòng nhập họ tên.';
  if (!filter_var($v['email'],FILTER_VALIDATE_EMAIL)) $errors[]='Email chưa đúng định dạng.';
  if ($v['phone']!=='' && !preg_match('/^[0-9 +().-]{8,20}$/',$v['phone'])) $errors[]='Số điện thoại chưa đúng.';
  if (mb_strlen($v['message'])<5) $errors[]='Nội dung quá ngắn.';
  if (!$errors) {
    $pdo->prepare('INSERT INTO contact_messages(name,email,phone,message) VALUES(?,?,?,?)')
        ->execute([mb_substr($v['name'],0,120),$v['email'],$v['phone'],mb_substr($v['message'],0,3000)]);
    $_SESSION['last']=time(); $done=true;
  }
}
include __DIR__.'/includes/header.php'; ?>
<main class="page"><div class="wrap"><h1>Liên hệ</h1>
<?php include __DIR__.'/includes/contact-details.php'; ?>

<?php if ($done): ?><p class="note" role="status">Cảm ơn bạn, chúng mình đã nhận được tin nhắn và sẽ phản hồi sớm.</p>
<?php else: ?>
<?php if ($errors): ?><div class="note err" role="alert"><?php foreach ($errors as $m) echo '<p>'.e($m).'</p>'; ?></div><?php endif; ?>
<form class="form" method="post" novalidate>
<input type="hidden" name="t" value="<?= e(csrf()) ?>"/><input class="hp" name="website" tabindex="-1" autocomplete="off" aria-hidden="true"/>
<label>Họ tên<input name="name" required value="<?= e($v['name']) ?>"/></label>
<label>Email<input name="email" type="email" required value="<?= e($v['email']) ?>"/></label>
<label>Số điện thoại<input name="phone" type="tel" value="<?= e($v['phone']) ?>"/></label>
<label>Nội dung<textarea name="message" rows="5" required><?= e($v['message']) ?></textarea></label>
<button class="btn" type="submit">Gửi liên hệ <span>→</span></button></form><?php endif; ?>
</div></main>
<?php include __DIR__.'/includes/footer.php'; ?>
