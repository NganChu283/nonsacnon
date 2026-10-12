<?php require_once __DIR__.'/includes/db.php'; $pageTitle='Non Sắc Nón – Nón Xưa Họa Nay'; $homeStories=$pdo->query("SELECT slug,title,category,created_at,excerpt,cover_image,tone FROM stories WHERE status='published' ORDER BY created_at DESC LIMIT 2")->fetchAll(); $hero=photo_asset('A6'); $intro=photo_asset('A1'); $village=photo_asset('A5'); $traditional=photo_asset('A3'); $modern=photo_asset('A4'); include __DIR__.'/includes/header.php'; ?>
<main id="top">
<section class="hero"><i class="dot d1"></i><i class="dot d2"></i><i class="dot d3"></i><span class="hint" aria-hidden="true"></span><div class="wrap g"><div>
<p class="eyebrow hero-kicker">NON SẮC NÓN / NÓN XƯA HỌA NAY</p>
<h1><span class="ln"><span>Hành trình</span></span><span class="ln"><span>di sản</span></span></h1><p class="hero-lead">Bốn chặng đường đưa nón lá từ làng nghề đến người trẻ hôm nay.</p>
<a class="btn" href="index.php#du-an">Khám phá hành trình <span>→</span></a></div>
<div class="hero-photo-wrap"><?= picture($hero,null,'hero-photo','eager','55% 55%','(min-width: 1024px) 42vw, 90vw') ?><div class="hero-caption">Đôi tay giữ nghề</div></div></div></section>

<section id="gt"><div class="wrap two rv"><div><p class="eyebrow section-kicker">01 / Di sản</p><h2>Nón lá không chỉ là một vật dụng quen thuộc</h2>
<p class="section-copy">Đó là câu chuyện về con người, làng nghề và những giá trị được truyền qua nhiều thế hệ.</p></div>
<div class="section-photo reveal-photo"><?= picture($intro,null,'','lazy','50% 50%','(min-width: 900px) 50vw, 100vw') ?></div></div></section>
<section id="chuyen-ben-vanh-non"><div class="wrap"><p class="eyebrow">Khám phá / Blog</p><h2>Chuyện bên vành nón</h2><div class="sgrid"><?php foreach($homeStories as $story): ?><a href="story.php?slug=<?= e($story['slug']) ?>" style="text-decoration:none"><div class="thumb"><?= media($story['cover_image'],$story['tone'],$story['title']) ?></div><p class="meta" style="margin-top:14px"><?= e($story['category']) ?> · <time datetime="<?= e(date('Y-m-d',strtotime($story['created_at']))) ?>"><?= e(date('d/m/Y',strtotime($story['created_at']))) ?></time></p><h3><?= e($story['title']) ?></h3><p><?= e($story['excerpt']) ?></p></a><?php endforeach; ?></div><?php if(!$homeStories): ?><p class="note">Những câu chuyện mới sẽ được cập nhật sớm.</p><?php endif; ?><p class="stories-more"><a class="btn" href="stories.php">Tìm hiểu thêm <span aria-hidden="true">→</span></a></p></div></section>

<section class="dark"><div class="wrap two v rv"><div class="section-photo village-photo"><?= picture($village,null,'','lazy','40% 40%','(min-width: 900px) 60vw, 100vw') ?></div>
<div><p class="eyebrow section-kicker">02 / Làng nghề</p><p class="serif village-label">Làng nón Chuông</p><h2 class="village-title">Chuyện nghề sắc nón</h2>
<p class="section-copy">Ẩn sau mỗi chiếc nón là đôi bàn tay của người thợ, những kỹ thuật được truyền lại qua nhiều thế hệ.</p>
<a class="lk" href="gallery.php#ph1">Khám phá làng nghề</a></div></div></section>





<section id="nx"><div class="wrap"><p class="eyebrow">Góc trải nghiệm</p><h2>Nón xưa<br/>họa nay</h2>
<p class="slider-lead">Kéo thanh trượt để so sánh chiếc nón truyền thống với bản vẽ đương đại.</p>
<div class="sl" id="sl"><div class="slider-photo traditional-photo" id="s1"><?= picture($traditional,null,'','eager','52% 50%','100vw') ?></div>
<div class="slider-photo modern-photo" id="s2" style="clip-path:inset(0 0 0 50%)"><?= picture($modern,'Nón hiện đại – họa tiết sen hồng','','eager','50% 50%','100vw') ?></div>
<div class="bar2" id="bar2" role="separator" aria-label="Vị trí so sánh" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50" style="left:50%"></div>
<input id="rg" type="range" min="0" max="100" step="1" value="50" aria-label="Kéo để chuyển từ truyền thống sang hiện đại"/>
<em style="left:14px">Truyền thống</em><em style="right:14px">Hiện đại</em></div></div></section>
<section id="tu-xua-den-nay"><div class="wrap"><?php include __DIR__.'/includes/project-history.php'; ?></div></section>
<section id="about-home"><div class="wrap"><p class="eyebrow">Về chúng tôi</p><h2>Non Sắc Nón</h2><p class="library-intro">Chúng tôi kể lại câu chuyện về nón lá Việt Nam bằng ngôn ngữ của thế hệ hôm nay.</p><div class="two about-columns"><div><h3>Sứ mệnh</h3><p>Đưa nón lá, làng nghề và người giữ nghề đến gần hơn với người trẻ qua câu chuyện, workshop và nghệ thuật.</p></div><div><h3>Tầm nhìn</h3><p>Một nhịp cầu văn hóa nơi truyền thống được hiểu, được yêu và được tiếp nối bằng cách của hôm nay.</p></div></div></div></section>

<section class="projects-section" id="du-an"><div class="wrap"><p class="eyebrow">Dự án</p><h2>Mỗi dự án, một hành trình</h2><p class="library-intro">Những câu chuyện riêng, cùng tiếp nối giá trị của chiếc nón Việt.</p><?php include __DIR__.'/includes/project-cards.php'; ?></div></section>
<?php $homeWorkshops=$pdo->query('SELECT * FROM workshops WHERE is_open=1 ORDER BY id DESC')->fetchAll(); ?>
<section class="red" id="ws"><div class="wrap rv"><p class="eyebrow section-kicker">Cùng trải nghiệm / Workshop</p><h2>Chạm di sản<br/>nhịp sống trẻ</h2><p class="workshop-lead">Không chỉ nhìn. Hãy tự tay tạo nên chiếc nón của riêng bạn.</p>
<?php if($homeWorkshops): ?><div class="workshop-list">
<?php foreach($homeWorkshops as $homeWorkshop): $homeWorkshopFormUrl=workshop_registration_url($homeWorkshop); ?><article class="workshop-item" id="home-workshop-<?= (int)$homeWorkshop['id'] ?>">
<h3><?= e($homeWorkshop['title']) ?></h3>
<dl class="info"><div><dt>Thời gian</dt><dd><?= !empty($homeWorkshop['event_date'])?e(date('d/m/Y H:i',strtotime($homeWorkshop['event_date']))):'Sẽ cập nhật' ?></dd></div><div><dt>Địa điểm</dt><dd><?= !empty($homeWorkshop['location'])?e($homeWorkshop['location']):'Sẽ cập nhật' ?></dd></div></dl>
<?php if($homeWorkshopFormUrl): ?><a class="btn light" href="<?= e($homeWorkshopFormUrl) ?>" target="_blank" rel="noopener noreferrer">Tham gia workshop <span aria-hidden="true">↗</span></a><?php else: ?><span class="btn light" role="link" aria-disabled="true" title="Đăng ký hiện chưa mở">Liên hệ đăng ký</span><?php endif; ?>
</article><?php endforeach; ?></div>
<?php else: ?><p class="workshop-empty">Hiện chưa có workshop mở đăng ký.</p><?php endif; ?>
</div></section>
</main>
<?php include __DIR__.'/includes/footer.php'; ?>
