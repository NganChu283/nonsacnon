<?php require __DIR__.'/_layout.php'; require_perm('stories'); post_guard();
$id=(int)($_GET['id']??0); $s=['title'=>'','category'=>CATS[0],'excerpt'=>'','body'=>'','status'=>'draft','tone'=>'red'];
if($id){ $q=$pdo->prepare('SELECT * FROM stories WHERE id=?'); $q->execute([$id]); $s=$q->fetch() ?: exit('Không tìm thấy bài.'); }
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  foreach(['title','excerpt'] as $k) $s[$k]=trim($_POST[$k]??'');
  $s['body']=sanitize_rich_text($_POST['body']??'');
  $s['category']=in_array($_POST['category']??'',CATS,true)?$_POST['category']:CATS[0];
  $s['status']=($_POST['status']??'')==='published'?'published':'draft';
  $s['tone']=array_key_exists($_POST['tone']??'',TONES)?$_POST['tone']:'red';
  if($s['title']===''||$s['body']==='') $err='Cần có tiêu đề và nội dung.';
  else {
    try {
      $base=slugify($s['title']); $slug=$base; $n=1;
      while(true){ $q=$pdo->prepare('SELECT COUNT(*) FROM stories WHERE slug=? AND id<>?'); $q->execute([$slug,$id]); if(!$q->fetchColumn()) break; $slug=$base.'-'.(++$n); }
      $img=save_upload('cover');
      if($id) $pdo->prepare('UPDATE stories SET slug=?,title=?,category=?,excerpt=?,body=?,status=?,tone=?,cover_image=COALESCE(?,cover_image) WHERE id=?')->execute([$slug,$s['title'],$s['category'],$s['excerpt'],$s['body'],$s['status'],$s['tone'],$img,$id]);
      else $pdo->prepare('INSERT INTO stories(slug,title,category,excerpt,body,status,tone,cover_image,author_id) VALUES(?,?,?,?,?,?,?,?,?)')->execute([$slug,$s['title'],$s['category'],$s['excerpt'],$s['body'],$s['status'],$s['tone'],$img,user()['id']]);
      header('Location: stories.php?saved=1',true,303); exit;
    } catch(Throwable $ex) { $err=$ex instanceof RuntimeException && !($ex instanceof PDOException) ? $ex->getMessage() : admin_save_error($ex); }
  }
}
admin_head('Bài viết','stories'); ?>
<?php if($err) echo '<p class="note err">'.e($err).'</p>'; ?>
<form class="form" method="post" enctype="multipart/form-data"><?= csrf_field() ?>
<label>Tiêu đề<input name="title" required value="<?= e($s['title']) ?>"/></label>
<label>Chủ đề<select name="category"><?php foreach(CATS as $c) echo '<option'.($c===$s['category']?' selected':'').'>'.e($c).'</option>'; ?></select></label>
<label>Tóm tắt<textarea name="excerpt" rows="2"><?= e($s['excerpt']) ?></textarea></label>
<label for="storyBody">Nội dung</label>
<div class="editor-toolbar" role="toolbar" aria-label="Công cụ định dạng bài viết">
  <select data-command="formatBlock" aria-label="Kiểu đoạn"><option value="p">Đoạn văn</option><option value="h2">Tiêu đề 2</option><option value="h3">Tiêu đề 3</option><option value="blockquote">Trích dẫn</option></select>
  <button type="button" data-command="bold" aria-label="In đậm"><strong>B</strong></button><button type="button" data-command="italic" aria-label="In nghiêng"><em>I</em></button><button type="button" data-command="underline" aria-label="Gạch chân"><u>U</u></button>
  <button type="button" data-command="justifyLeft" aria-label="Căn trái">≡</button><button type="button" data-command="justifyCenter" aria-label="Căn giữa">≡</button><button type="button" data-command="justifyRight" aria-label="Căn phải">≡</button>
  <button type="button" data-command="insertUnorderedList" aria-label="Danh sách">• List</button><button type="button" data-command="insertOrderedList" aria-label="Danh sách số">1. List</button><button type="button" data-command="createLink" aria-label="Chèn liên kết">Link</button><button type="button" data-command="insertImage" aria-label="Chèn ảnh">Ảnh</button>
</div>
<div id="storyBody" class="rich-editor" contenteditable="true" role="textbox" aria-multiline="true" aria-label="Nội dung bài viết"><?= $s['body'] ?></div>
<textarea name="body" id="bodySource" hidden required><?= e($s['body']) ?></textarea>
<label>Ảnh bìa gốc (jpg, png, webp, tối đa 10MB)<input type="file" name="cover" accept="image/jpeg,image/png,image/webp"/></label>
<label>Màu hình minh họa khi chưa có ảnh<select name="tone"><?php foreach(array_keys(TONES) as $t) echo '<option'.($t===$s['tone']?' selected':'').'>'.$t.'</option>'; ?></select></label>
<label>Trạng thái<select name="status"><option value="draft">Nháp</option><option value="published"<?= $s['status']==='published'?' selected':'' ?>>Đăng công khai</option></select></label>
<button class="btn" type="submit">Lưu bài</button></form>
<script>window.editorCsrf=<?= json_encode(csrf()) ?>;</script>
<?php admin_foot();
