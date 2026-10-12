<?php require_once __DIR__.'/db.php'; $pageTitle = $pageTitle ?? 'Non Sắc Nón'; ?>
<!doctype html><html lang="vi"><head><meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
<title><?= e($pageTitle) ?></title>
<meta name="description" content="Khám phá câu chuyện về nón lá Việt Nam, làng nghề, nghệ nhân và những góc nhìn mới của thế hệ trẻ."/>
<link rel="icon" href="assets/img/non-sac-non-logo.jpg"/>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500&family=Cormorant+Garamond:wght@500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="assets/css/site.css?v=project-excerpt-6"/>
</head><body id="top">
<div id="pg" aria-hidden="true"></div>
<header id="hd"><div class="wrap"><div class="bar">
<a class="logo brand-logo" href="index.php" aria-label="Non Sắc Nón – Trang chủ">
<img src="assets/img/non-sac-non-logo.jpg?v=2" alt="Logo Non Sắc Nón" width="72" height="72"/>
<span>NON SẮC NÓN</span>
</a>
<nav class="d" aria-label="Chính"><a href="index.php#about-home">Về chúng tôi</a><a href="index.php#chuyen-ben-vanh-non">Khám phá</a><a href="index.php#du-an">Dự án</a><a href="#contact-dialog" data-contact-trigger>Liên hệ</a><a class="cta" href="workshop.php">Tham gia Workshop</a></nav>
<button id="burger" aria-label="Mở menu" aria-expanded="false">☰</button></div>
<nav id="m" aria-label="Di động"><a href="index.php#about-home">Về chúng tôi</a><a href="index.php#chuyen-ben-vanh-non">Khám phá</a><a href="index.php#du-an">Dự án</a><a href="#contact-dialog" data-contact-trigger>Liên hệ</a><a class="cta" href="workshop.php">Tham gia Workshop</a></nav></div></header>
<dialog id="contact-dialog" class="contact-dialog" aria-labelledby="contact-dialog-title"><form method="post" action="contact.php" class="form"><button type="button" class="dialog-close" data-contact-close aria-label="Đóng">×</button><p class="eyebrow">Liên hệ</p><h2 id="contact-dialog-title">Kể chúng mình nghe</h2><?php include __DIR__.'/contact-details.php'; ?><input type="hidden" name="t" value="<?= e(csrf()) ?>"/><label>Họ tên<input name="name" required/></label><label>Email<input name="email" type="email" required/></label><label>Nội dung<textarea name="message" rows="4" required></textarea></label><button class="btn" type="submit">Gửi liên hệ <span>→</span></button></form></dialog>
