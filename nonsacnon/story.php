<?php require __DIR__.'/includes/db.php';
$q=$pdo->prepare("SELECT * FROM stories WHERE slug=? AND status='published'"); $q->execute([$_GET['slug']??'']); $s=$q->fetch();
if(!$s){ http_response_code(404); $pageTitle='Không tìm thấy'; include __DIR__.'/includes/header.php'; echo '<main class="page"><div class="wrap"><h1>Không tìm thấy bài viết</h1><p><a href="stories.php">Về Chuyện bên vành nón</a></p></div></main>'; include __DIR__.'/includes/footer.php'; exit; }
$pageTitle=$s['title'].' – Non Sắc Nón'; include __DIR__.'/includes/header.php'; ?>
<main class="page"><div class="wrap"><p class="meta"><a href="stories.php?c=<?= urlencode($s['category']) ?>" style="text-decoration:none"><?= e($s['category']) ?></a> · <?= e(date('d/m/Y',strtotime($s['created_at']))) ?></p>
<h1 class="sm"><?= e($s['title']) ?></h1>
<div class="thumb" style="aspect-ratio:16/8;margin:36px 0"><?= media($s['cover_image'],$s['tone'],$s['title']) ?></div>
<div class="prose"><?php if(strpos($s['body'],'<')!==false) echo sanitize_rich_text($s['body']); else foreach(preg_split('/\R{2,}/',$s['body']) as $p) echo '<p>'.nl2br(e(trim($p))).'</p>'; ?></div>
<p style="margin-top:48px"><a class="lk" href="stories.php">← Chuyện bên vành nón</a></p></div></main><?php include __DIR__.'/includes/footer.php'; ?>
