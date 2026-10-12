<?php $projects=$projects??all_projects($pdo); ?>
<div class="project-cards"><?php foreach($projects as $project): ?><article class="project-card">
<a href="project.php?id=<?= (int)$project['id'] ?>" aria-label="Khám phá dự án: <?= e($project['title']) ?>"><div class="project-card-image"><?= media($project['cover_image'],'red',$project['title']) ?></div></a>
<div class="project-card-copy"><p class="eyebrow"><?= (int)$project['phase_count'] ?> giai đoạn</p><h3><?= e($project['title']) ?></h3><p class="project-card-excerpt"><?= e($project['introduction']) ?></p><a class="lk" href="project.php?id=<?= (int)$project['id'] ?>">Khám phá dự án →</a></div></article><?php endforeach; ?></div>
<?php if(!$projects): ?><p class="note">Các dự án sẽ được cập nhật sớm.</p><?php endif; ?>
