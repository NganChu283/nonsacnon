<?php require __DIR__.'/includes/db.php'; $pageTitle='Khám phá – Non Sắc Nón';
$b=[['Nón lá','Chiếc nón lá được làm từ lá cọ hoặc lá nón, khung tre vót mảnh và những đường khâu đều tay. Nhẹ, thoáng và gần gũi, nón lá đi cùng người Việt qua nắng mưa.','red','A3'],
['Lịch sử','Từ vật dụng che nắng che mưa của người làm ruộng, nón lá dần trở thành một biểu tượng của vùng quê Việt Nam và xuất hiện trong thơ ca, âm nhạc, trang phục.','leaf','A1'],
['Làng nghề','Mỗi làng nón có cách chằm, cách dằn lá và kiểu dáng riêng. Làng nón Chuông là một trong những nơi giữ nghề được nhắc đến nhiều.','gold','A5'],
['Nghệ nhân','Đằng sau mỗi chiếc nón là người thợ kiên nhẫn, truyền nghề cho con cháu. Dự án muốn lắng nghe và ghi lại chính tiếng nói của họ.','brown','A6']];
include __DIR__.'/includes/header.php'; ?>
<main class="page"><div class="wrap"><h1>Khám phá</h1>
<?php foreach($b as $i=>$x): ?><section class="two" style="padding:56px 0;border-top:1px solid var(--line);margin-top:24px"><div<?= $i%2?' style="order:2"':'' ?>><h2 style="font-size:clamp(40px,6vw,64px)"><?= e($x[0]) ?></h2><p style="margin-top:16px;max-width:460px"><?= e($x[1]) ?></p></div>
<div class="thumb"><?= picture(photo_asset($x[3]),null,'','lazy') ?></div></section><?php endforeach; ?></div></main><?php include __DIR__.'/includes/footer.php'; ?>
