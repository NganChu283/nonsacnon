<?php require __DIR__.'/_layout.php'; admin_head('Bài viết','stories'); post_guard();
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['del'])){
  $q=$pdo->prepare('SELECT cover_image FROM stories WHERE id=?'); $q->execute([(int)$_POST['del']]); $p=$q->fetchColumn();
  if($p&&strpos($p,'assets/uploads/')===0) @unlink(__DIR__.'/../'.$p);
  $pdo->prepare('DELETE FROM stories WHERE id=?')->execute([(int)$_POST['del']]);
}
$rows=$pdo->query('SELECT id,title,category,status,created_at FROM stories ORDER BY created_at DESC')->fetchAll(); ?>
<p><a class="btn" href="story_edit.php">Viết bài mới</a></p>
<table><tr><th>Tiêu đề</th><th>Chủ đề</th><th>Trạng thái</th><th></th></tr>
<?php foreach($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['category']) ?></td><td><?= $r['status']==='published'?'Đã đăng':'Nháp' ?></td>
<td><a href="story_edit.php?id=<?= $r['id'] ?>">Sửa</a> <form method="post" style="display:inline" onsubmit="return confirm('Xóa bài này?')"><?= csrf_field() ?><button name="del" value="<?= $r['id'] ?>">Xóa</button></form></td></tr><?php endforeach; ?></table>
<?php admin_foot();
