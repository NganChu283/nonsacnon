<?php require __DIR__.'/_layout.php'; admin_head('Biên tập nội dung','editor');
$count=fn($q)=>(int)$pdo->query($q)->fetchColumn();
$latest=$pdo->query('SELECT id,title,status,created_at FROM stories ORDER BY created_at DESC LIMIT 5')->fetchAll();
?>
<p class="lead">Không gian dành cho biên tập viên quản lý câu chuyện, bài viết và hình ảnh văn hóa.</p>
<div class="stats">
<div><b><?= $count('SELECT COUNT(*) FROM stories') ?></b>Tổng bài viết</div>
<div><b><?= $count("SELECT COUNT(*) FROM stories WHERE status='published'") ?></b>Đã xuất bản</div>
<div><b><?= $count("SELECT COUNT(*) FROM stories WHERE status='draft'") ?></b>Bản nháp</div>
<div><b><?= $count('SELECT COUNT(*) FROM gallery_items') ?></b>Ảnh trong thư viện</div>
</div>
<div class="quick-actions"><a class="btn" href="story_edit.php">Viết bài mới <span>→</span></a><a class="btn" href="gallery.php">Mở thư viện ảnh <span>→</span></a></div>
<div class="box"><h2>Bài viết gần đây</h2><table><tr><th>Tiêu đề</th><th>Trạng thái</th><th></th></tr>
<?php foreach($latest as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= $r['status']==='published'?'Đã đăng':'Nháp' ?></td><td><a href="story_edit.php?id=<?= (int)$r['id'] ?>">Sửa</a></td></tr><?php endforeach; ?>
</table></div>
<?php admin_foot();
