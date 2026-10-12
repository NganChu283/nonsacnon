<?php require __DIR__.'/_layout.php'; admin_head('Người dùng','users'); post_guard(); $err=''; $me=user()['id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(isset($_POST['new'])){
    $n=trim($_POST['name']??''); $m=trim($_POST['email']??''); $p=$_POST['password']??''; $r=array_key_exists($_POST['role']??'',ROLES)?$_POST['role']:'editor';
    if($n===''||!filter_var($m,FILTER_VALIDATE_EMAIL)||strlen($p)<8) $err='Cần họ tên, email hợp lệ và mật khẩu từ 8 ký tự.';
    else try{ $pdo->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)')->execute([$n,$m,password_hash($p,PASSWORD_DEFAULT),$r]); }catch(PDOException $x){ $err='Email này đã có tài khoản.'; }
  } else { $i=(int)($_POST['id']??0);
    if($i===$me) $err='Bạn không thể đổi quyền hoặc khóa chính mình.';
    elseif(isset($_POST['role'])&&array_key_exists($_POST['role'],ROLES)) $pdo->prepare('UPDATE users SET role=? WHERE id=?')->execute([$_POST['role'],$i]);
    elseif(isset($_POST['toggle'])) $pdo->prepare('UPDATE users SET is_active=1-is_active WHERE id=?')->execute([$i]); }
}
$rows=$pdo->query('SELECT * FROM users ORDER BY id')->fetchAll();
if($err) echo '<p class="note err">'.e($err).'</p>'; ?>
<table><tr><th>Tên</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th></tr>
<?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td>
<td><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"/><select name="role" onchange="this.form.submit()"><?php foreach(ROLES as $k=>$v) echo '<option value="'.$k.'"'.($k===$r['role']?' selected':'').'>'.e($v).'</option>'; ?></select></form></td>
<td><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"/><button name="toggle" value="1"><?= $r['is_active']?'Đang hoạt động (khóa)':'Đã khóa (mở lại)' ?></button></form></td></tr><?php endforeach; ?></table>
<h2 style="font-size:32px;margin-top:40px">Thêm người dùng</h2>
<form class="form" method="post"><?= csrf_field() ?><input type="hidden" name="new" value="1"/>
<label>Họ tên<input name="name" required/></label><label>Email<input name="email" type="email" required/></label><label>Mật khẩu (từ 8 ký tự)<input name="password" type="password" required/></label>
<label>Vai trò<select name="role"><?php foreach(ROLES as $k=>$v) echo '<option value="'.$k.'">'.e($v).'</option>'; ?></select></label>
<button class="btn" type="submit">Thêm</button></form>
<?php admin_foot();
