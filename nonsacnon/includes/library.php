<?php $libraryRows=$libraryRows??[]; $libraryProject=$libraryProject??find_project($pdo,1);$projectPhases=project_phases($pdo,$libraryProject['id']); ?>
<div data-library class="journey-library">
<div class="phase-nav" role="group" aria-label="Chọn giai đoạn">
<button type="button" data-phase="all" aria-pressed="true">All</button>
<?php foreach($projectPhases as $phase=>$d): ?><button type="button" data-phase="<?= e($phase) ?>" aria-pressed="false"><?= e($d['title']) ?></button><?php endforeach; ?>
</div>
<div class="phase-intro" aria-live="polite" aria-atomic="true">
<div data-phase-copy="all"><h3><?= e($libraryProject['title']) ?></h3><p>Khám phá các giai đoạn và khoảnh khắc của dự án.</p></div>
<?php foreach($projectPhases as $phase=>$d): ?><div data-phase-copy="<?= e($phase) ?>" hidden><p class="eyebrow">Giai đoạn <?= e($d['position']) ?></p><h3><?= e($d['title']) ?></h3><p><?= e($d['description']) ?></p><?php if(!empty($d['image_path'])): ?><div class="project-phase-photo"><?= media($d['image_path'],'red',$d['title']) ?></div><?php endif; ?><?php if(!empty($d['body'])): ?><div class="prose phase-body"><?= project_rich_text($d['body']) ?></div><?php endif; ?></div><?php endforeach; ?>
</div>
<?php if(isset($libraryTimeline))echo $libraryTimeline; ?>
<h3 class="library-subtitle">Những khoảnh khắc</h3>
<div class="gal" role="group" aria-label="Lọc ảnh theo chủ đề"><?php foreach(array_merge(['Tất cả'],GCATS) as $i=>$category): ?><button type="button" data-category="<?= e($category) ?>" aria-pressed="<?= $i?'false':'true' ?>"><?= e($category) ?></button><?php endforeach; ?></div>
<p class="note library-empty" role="status" hidden>Chưa có ảnh trong mục này. Hãy chọn giai đoạn hoặc chủ đề khác.</p>
<div class="mas"><?php foreach($libraryRows as $i=>$item): ?>
<button type="button" class="gfig" data-category="<?= e($item['category']) ?>" data-phase="<?= e($item['phase']??library_phase_item($item)) ?>" data-large="<?= e(preg_replace('/-1200\.webp$/','-1600.webp',$item['image_path']??'')) ?>" style="--i:<?= $i ?>" aria-label="Xem ảnh: <?= e($item['title']) ?>">
<?= media($item['image_path']??null,$item['tone'],$item['alt']??$item['title']) ?><span class="gallery-caption"><?= e($item['title']) ?> · <?= e($item['category']) ?></span></button><?php endforeach; ?></div>
<dialog class="library-lightbox" aria-label="Xem ảnh thư viện"><div class="lb"></div><p class="lightbox-caption"></p><form method="dialog"><button class="btn">Đóng</button></form></dialog>
</div>
