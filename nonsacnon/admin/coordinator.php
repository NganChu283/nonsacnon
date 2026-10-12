<?php require __DIR__.'/_layout.php'; admin_head('Điều phối','organizer');
$count=fn($q)=>(int)$pdo->query($q)->fetchColumn();
$upcoming=$pdo->query('SELECT w.id,w.title,w.event_date,w.capacity FROM workshops w ORDER BY w.event_date IS NULL,w.event_date LIMIT 5')->fetchAll();
?>
<p class="lead">Theo dõi workshop và các yêu cầu liên hệ từ cộng đồng.</p>
<div class="stats">
<div><b><?= $count('SELECT COUNT(*) FROM workshops') ?></b>Workshop</div>
<div><b><?= $count('SELECT COUNT(*) FROM workshops WHERE is_open=1') ?></b>Đang mở</div>
<div><b><?= $count('SELECT COUNT(*) FROM contact_messages WHERE is_read=0') ?></b>Tin chưa đọc</div>
</div>
<div class="quick-actions"><a class="btn" href="workshops.php">Quản lý workshop <span>→</span></a><a class="btn" href="messages.php">Xem tin nhắn <span>→</span></a></div>
<div class="box"><h2>Workshop sắp tới</h2><table><tr><th>Tên</th><th>Thời gian</th></tr>
<?php foreach($upcoming as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= $r['event_date']?e(date('d/m/Y H:i',strtotime($r['event_date']))):'Chưa cập nhật' ?></td></tr><?php endforeach; ?>
</table></div>
<?php admin_foot();
