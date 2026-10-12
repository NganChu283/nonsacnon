<?php require __DIR__.'/_layout.php'; admin_head('Quản trị hệ thống','admin');
$count=fn($q)=>(int)$pdo->query($q)->fetchColumn();
$users=$pdo->query('SELECT role,COUNT(*) total FROM users GROUP BY role')->fetchAll();
?>
<p class="lead">Quản lý tài khoản, phân quyền và theo dõi toàn bộ hoạt động của website.</p>
<div class="stats">
<div><b><?= $count('SELECT COUNT(*) FROM users') ?></b>Tài khoản</div>
<div><b><?= $count('SELECT COUNT(*) FROM users WHERE is_active=1') ?></b>Đang hoạt động</div>
<div><b><?= $count('SELECT COUNT(*) FROM stories') ?></b>Bài viết</div>
<div><b><?= $count('SELECT COUNT(*) FROM workshops') ?></b>Workshop</div>
</div>
<div class="box"><h2>Phân bổ vai trò</h2><table><tr><th>Vai trò</th><th>Số lượng</th></tr>
<?php foreach($users as $r): ?><tr><td><?= e(ROLES[$r['role']]??$r['role']) ?></td><td><?= (int)$r['total'] ?></td></tr><?php endforeach; ?>
</table></div>
<p><a class="btn" href="users.php">Quản lý người dùng <span>→</span></a></p>
<?php admin_foot();
