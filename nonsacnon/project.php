<?php
require __DIR__.'/includes/db.php';
$id=filter_var($_GET['id']??1,FILTER_VALIDATE_INT);$project=$id?find_project($pdo,$id):false;
$fragment=($_GET['fragment']??'')==='1';
if(!$project){http_response_code(404);if($fragment){echo '<p class="note">Không tìm thấy dự án.</p>';exit;}$pageTitle='Không tìm thấy dự án';include __DIR__.'/includes/header.php';echo '<main class="page"><div class="wrap"><h1>Không tìm thấy dự án</h1><p><a href="projects.php">Về các dự án</a></p></div></main>';include __DIR__.'/includes/footer.php';exit;}
if($fragment){include __DIR__.'/includes/project-detail.php';exit;}
$pageTitle=$project['title'].' – Non Sắc Nón';include __DIR__.'/includes/header.php'; ?>
<main class="page project-page"><div class="wrap"><p class="project-back"><a href="projects.php">← Các dự án</a></p><?php include __DIR__.'/includes/project-detail.php'; ?></div></main>
<?php include __DIR__.'/includes/footer.php'; ?>
