<?php require __DIR__.'/includes/db.php'; $pageTitle='Chuyện bên vành nón – Non Sắc Nón';
$c=$_GET['c']??''; if(!in_array($c,CATS,true)) $c='';
$q=$pdo->prepare("SELECT * FROM stories WHERE status='published' AND (?='' OR category=?) ORDER BY created_at DESC"); $q->execute([$c,$c]); $rows=$q->fetchAll();
include __DIR__.'/includes/header.php'; ?>
<main class="page"><div class="wrap"><p class="eyebrow">Khám phá / Blog</p><h1>Chuyện bên vành nón</h1>
<nav class="gal" aria-label="Lọc theo chủ đề"><a class="chip" href="stories.php"<?= $c===''?' aria-current="true"':'' ?>>Tất cả</a>
<?php foreach(CATS as $k) echo '<a class="chip" href="?c='.urlencode($k).'"'.($k===$c?' aria-current="true"':'').'>'.e($k).'</a>'; ?></nav>
<?php if(!$rows) echo '<p class="note">Chưa có bài viết trong chủ đề này.</p>'; else { $f=array_shift($rows); ?>
<a class="feat" href="story.php?slug=<?= e($f['slug']) ?>"><div class="thumb" style="aspect-ratio:16/10"><?= media($f['cover_image'],$f['tone'],$f['title']) ?></div>
<div><p class="meta"><?= e($f['category']) ?> · <?= e(date('d/m/Y',strtotime($f['created_at']))) ?></p><h2><?= e($f['title']) ?></h2><p><?= e($f['excerpt']) ?></p><span class="story-read">Đọc bài viết <span aria-hidden="true">→</span></span></div></a>
<div class="sgrid"><?php foreach($rows as $r): ?><a href="story.php?slug=<?= e($r['slug']) ?>" style="text-decoration:none"><div class="thumb"><?= media($r['cover_image'],$r['tone'],$r['title']) ?></div>
<p class="meta" style="margin-top:14px"><?= e($r['category']) ?> · <?= e(date('d/m/Y',strtotime($r['created_at']))) ?></p><h2><?= e($r['title']) ?></h2><p><?= e($r['excerpt']) ?></p><span class="story-read">Đọc bài viết <span aria-hidden="true">→</span></span></a><?php endforeach; ?></div><?php } ?>
</div></main><?php include __DIR__.'/includes/footer.php'; ?>
