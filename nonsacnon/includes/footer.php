<?php
$footerStories = [];
if (isset($pdo)) {
  $footerQuery = $pdo->query("SELECT slug,title,category,created_at,cover_image,tone FROM stories WHERE status='published' ORDER BY created_at DESC LIMIT 2");
  $footerStories = $footerQuery->fetchAll();
}
?>
<footer class="site-footer" id="site-footer">
  <div class="footer-art" aria-hidden="true">
    <svg viewBox="0 0 420 300" focusable="false"><path d="M210 28 28 240h364L210 28Z"/><path d="M210 28v212M116 240 210 28l94 212M70 240 210 28l140 212M40 240h340M82 202h256M118 158h184M154 112h112"/></svg>
  </div>
  <div class="wrap footer-grid">
    <section class="footer-brand" aria-labelledby="footer-brand-title">
      <h2 id="footer-brand-title" class="footer-title footer-logo"><img src="assets/img/non-sac-non-logo.jpg?v=2" alt="Logo Non Sắc Nón" width="96" height="96"/></h2>
      <span class="footer-rule" aria-hidden="true"></span>
      <p>Chúng tôi kể lại câu chuyện về nón lá Việt Nam bằng ngôn ngữ của thế hệ hôm nay.</p>
      <div class="footer-social" aria-label="Mạng xã hội">
        <a href="https://web.facebook.com/profile.php?id=61594757066646" target="_blank" rel="noopener noreferrer" aria-label="Facebook Non Sắc Nón" title="Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 21v-8h3l.5-4H14V7c0-1 .5-2 2-2h2V2h-3c-3 0-5 2-5 5v2H7v4h3v8"/></svg></a>
        <a href="https://www.tiktok.com/@non.sc.nn?_r=1&amp;_t=ZS-9A1UjAeNZW0" target="_blank" rel="noopener noreferrer" aria-label="TikTok Non Sắc Nón" title="TikTok"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3v12a4 4 0 1 1-4-4H9V7a8 8 0 1 0 9 8V9c1.2.8 2.5 1.2 4 1.2V6a5 5 0 0 1-4-3Z"/></svg></a>
        <a class="footer-social-contact" href="mailto:nonsacnonn@gmail.com" target="_blank" rel="noopener noreferrer" aria-label="Gửi email" aria-describedby="footer-email-tip"><svg viewBox="0 0 24 24" aria-hidden="true" style="fill:none"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/></svg><span class="footer-tooltip" id="footer-email-tip" role="tooltip">nonsacnonn@gmail.com</span></a>
        <a class="footer-social-contact" href="tel:0869003861" target="_blank" rel="noopener noreferrer" aria-label="Gọi Ms Gia Linh" aria-describedby="footer-phone-tip"><svg viewBox="0 0 24 24" aria-hidden="true" style="fill:none"><path d="m7 3 3 5-2 2a15 15 0 0 0 6 6l2-2 5 3-1 3c-.3 1-1.5 1.3-2.5 1C10 19 5 14 3 6.5 2.7 5.5 3 4.3 4 4Z"/></svg><span class="footer-tooltip" id="footer-phone-tip" role="tooltip">0869003861 (Ms Gia Linh)</span></a>
        <span class="social-pending" aria-label="Threads: Sẽ cập nhật" title="sẽ cập nhật"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 7c-1-4-10-5-13-1-3 4-2 13 3 15 5 2 11-1 11-6 0-5-11-6-11-1 0 4 8 3 7-4-.4-3-4-4-7-2"/></svg></span>
      </div>

    </section>

    <section class="footer-stories" aria-labelledby="footer-stories-title">
      <h2 id="footer-stories-title" class="footer-title">Chuyện bên vành nón</h2>
      <span class="footer-rule" aria-hidden="true"></span>
      <?php if (!$footerStories): ?>
        <p class="footer-muted">Chưa có bài viết.</p>
      <?php else: foreach ($footerStories as $story): ?>
        <a class="footer-story" href="story.php?slug=<?= e($story['slug']) ?>">
          <span class="footer-story-image"><?= media($story['cover_image'], $story['tone'], $story['title'], 64) ?></span>
          <span class="footer-story-copy"><strong><?= e($story['title']) ?></strong><small><?= e(date('d/m/Y', strtotime($story['created_at']))) ?> · <?= e($story['category']) ?></small></span>
        </a>
      <?php endforeach; endif; ?>
    </section>

    <nav class="footer-links" aria-labelledby="footer-links-title">
      <h2 id="footer-links-title" class="footer-title">Liên kết nhanh</h2>
      <span class="footer-rule" aria-hidden="true"></span>
      <div class="footer-link-list">
        <a href="index.php#about-home">Về chúng tôi</a><a href="index.php#chuyen-ben-vanh-non">Khám phá</a><a href="index.php#du-an">Dự án</a><a href="#contact-dialog" data-contact-trigger>Liên hệ</a><a class="footer-workshop-cta" href="workshop.php">Tham gia Workshop <span aria-hidden="true">→</span></a>
      </div>
    </nav>



  </div>
  <div class="wrap footer-bottom">
    <a class="footer-admin" href="admin/login.php">Quản trị</a>
    <p class="footer-copyright">© 2026 Non Sắc Nón · Nón xưa họa nay</p>
    <a class="footer-top" href="#top">Lên đầu trang ↑</a>
  </div>
</footer>
<script src="assets/js/site.js?v=compact-1"></script></body></html>
