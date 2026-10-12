<div class="project-detail" data-project-id="<?= (int)$project['id'] ?>">
<div class="project-cover"><?= media($project['cover_image'],'red',$project['title']) ?></div>
<div class="project-detail-inner"><p class="eyebrow">Dự án / Non Sắc Nón</p><h1><?= e($project['title']) ?></h1><p class="project-introduction"><?= nl2br(e($project['introduction'])) ?></p>
<?php $libraryProject=$project;$libraryRows=project_gallery($pdo,$project['id']);unset($libraryTimeline);
include __DIR__.'/library.php'; ?>
</div></div>
