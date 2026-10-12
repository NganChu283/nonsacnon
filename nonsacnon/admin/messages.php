<?php require __DIR__.'/_layout.php'; admin_head('Tin nhắn liên hệ','messages'); post_guard();
if($_SERVER['REQUEST_METHOD']==='POST'){ $i=(int)($_POST['id']??0);
  if(($_POST['a']??'')==='del') $pdo->prepare('DELETE FROM contact_messages WHERE id=?')->execute([$i]);
  else $pdo->prepare('UPDATE contact_messages SET is_read=1-is_read WHERE id=?')->execute([$i]); }
$rows=$pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
foreach($rows as $r): ?>
<article class="box<?= $r['is_read']?'':' unread' ?>"><strong><?= e($r['name']) ?></strong> · <?= e($r['email']) ?> · <?= e($r['phone']) ?> · <small><?= e($r['created_at']) ?></small>
<p><?= nl2br(e($r['message'])) ?></p>
<form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"/><button><?= $r['is_read']?'Đánh dấu chưa đọc':'Đánh dấu đã đọc' ?></button>
<button name="a" value="del" onclick="return confirm('Xóa tin nhắn này?')">Xóa</button></form></article>
<?php endforeach; if(!$rows) echo '<p>Chưa có tin nhắn.</p>'; admin_foot();
