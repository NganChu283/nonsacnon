<?php require __DIR__.'/includes/db.php';$pageTitle='Workshop – Non Sắc Nón';$workshops=$pdo->query('SELECT * FROM workshops WHERE is_open=1 ORDER BY id DESC')->fetchAll();include __DIR__.'/includes/header.php'; ?>
<main class="page workshop-page"><section class="red" id="ws"><div class="wrap rv"><p class="eyebrow section-kicker">Cùng trải nghiệm / Workshop</p><h1>Chạm di sản<br/>nhịp sống trẻ</h1><p class="workshop-lead">Không chỉ nhìn. Hãy tự tay tạo nên chiếc nón của riêng bạn.</p>
<?php if($workshops): ?><div class="workshop-list">
<?php foreach($workshops as $w): $formUrl=workshop_registration_url($w); ?><article class="workshop-item" id="workshop-<?= (int)$w['id'] ?>">
<h3><?= e($w['title']) ?></h3>
<dl class="info"><div><dt>Thời gian</dt><dd><?= !empty($w['event_date'])?e(date('d/m/Y H:i',strtotime($w['event_date']))):'Sẽ cập nhật' ?></dd></div><div><dt>Địa điểm</dt><dd><?= !empty($w['location'])?e($w['location']):'Sẽ cập nhật' ?></dd></div></dl>
<?php if($formUrl): ?><a class="btn light" href="<?= e($formUrl) ?>" target="_blank" rel="noopener noreferrer">Tham gia workshop <span aria-hidden="true">↗</span></a><?php else: ?><span class="btn light" role="link" aria-disabled="true" title="Đăng ký hiện chưa mở">Tham gia workshop</span><?php endif; ?>
</article><?php endforeach; ?></div>
<?php else: ?><p class="workshop-empty">Hiện chưa có workshop mở đăng ký.</p><?php endif; ?>
</div></section></main><?php include __DIR__.'/includes/footer.php'; ?>
