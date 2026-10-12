<?php
require __DIR__.'/_layout.php';require_perm('projects');post_guard();
$defaultId=$pdo->query('SELECT id FROM projects ORDER BY id LIMIT 1')->fetchColumn();
$id=filter_var($_GET['id']??$defaultId,FILTER_VALIDATE_INT);$isNew=($_GET['new']??'')==='1'||(!isset($_GET['id'])&&!$defaultId);
$project=$isNew?['id'=>0,'title'=>'','introduction'=>'','cover_image'=>null]:($id?find_project($pdo,$id):false);
if(!$project){http_response_code(404);exit('Không tìm thấy dự án.');}
$phases=$project['id']?project_phases($pdo,$project['id']):[];
$phaseId=filter_var($_GET['phase_id']??0,FILTER_VALIDATE_INT);$newPhase=($_GET['new_phase']??'')==='1';$phase=false;
if(!$phaseId&&is_string($_GET['phase']??null)&&isset($phases[$_GET['phase']]))$phaseId=(int)$phases[$_GET['phase']]['id'];
if($phaseId){foreach($phases as $candidate)if((int)$candidate['id']===$phaseId)$phase=$candidate;if(!$phase){http_response_code(404);exit('Giai đoạn không thuộc dự án này.');}}
elseif(!$newPhase&&$phases)$phase=reset($phases);
if(!$phase)$phase=['id'=>0,'title'=>'','description'=>'','body'=>'','image_path'=>null,'sort_order'=>count($phases)+1];
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $action=$_POST['action']??'';
 try{
  if($action==='delete_project'&&$project['id']){
   $deleteId=filter_var($_POST['project_id']??null,FILTER_VALIDATE_INT);
   if(!$deleteId||$deleteId!==(int)$project['id'])throw new RuntimeException('Dự án cần xóa không hợp lệ.');
   $pdo->beginTransaction();
   $lock=$pdo->prepare('SELECT id FROM projects WHERE id=? FOR UPDATE');$lock->execute([$deleteId]);
   if(!$lock->fetchColumn())throw new RuntimeException('Dự án đã được xóa.');
   $pdo->prepare('DELETE g FROM gallery_items g JOIN project_phases ph ON ph.id=g.phase_id WHERE ph.project_id=?')->execute([$deleteId]);
   $pdo->prepare('DELETE FROM project_phases WHERE project_id=?')->execute([$deleteId]);
   $pdo->prepare('DELETE FROM projects WHERE id=?')->execute([$deleteId]);
   $pdo->commit();
   header('Location: projects.php?deleted=1');exit;
  }elseif($action==='project'){
   $title=$_POST['title']??'';$intro=$_POST['introduction']??'';
   if(!is_string($title)||!is_string($intro)||trim($title)===''||trim($intro)==='')throw new RuntimeException('Nhập tên và giới thiệu dự án.');
   $project['title']=trim($title);$project['introduction']=trim($intro);
   if(mb_strlen($title)>190||mb_strlen($intro)>10000)throw new RuntimeException('Tên tối đa 190 ký tự và giới thiệu tối đa 10.000 ký tự.');
   $cover=save_project_upload('cover');
   if($project['id'])$pdo->prepare('UPDATE projects SET title=?,introduction=?,cover_image=COALESCE(?,cover_image) WHERE id=?')->execute([$project['title'],$project['introduction'],$cover,$project['id']]);
   else{$pdo->prepare('INSERT INTO projects(title,introduction,cover_image) VALUES(?,?,?)')->execute([$project['title'],$project['introduction'],$cover]);$project['id']=(int)$pdo->lastInsertId();}
   header('Location: projects.php?id='.$project['id'].'&saved=project');exit;
  }elseif($action==='phase'&&$project['id']){
   $title=$_POST['title']??'';$description=$_POST['description']??'';$body=$_POST['body']??'';$order=filter_var($_POST['sort_order']??null,FILTER_VALIDATE_INT);
   if(!is_string($title)||!is_string($description)||!is_string($body)||trim($title)===''||trim($description)==='')throw new RuntimeException('Nhập tên và mô tả giai đoạn.');
   $phase['title']=trim($title);$phase['description']=trim($description);$phase['body']=project_rich_text($body);$phase['sort_order']=$order;
   if(mb_strlen($title)>190||mb_strlen($description)>1000||strlen($phase['body'])>2*1024*1024||!$order||$order>10000)throw new RuntimeException('Kiểm tra độ dài nội dung và thứ tự giai đoạn (1–10.000).');
   $image=save_project_upload('photo');
   if($phase['id'])$pdo->prepare('UPDATE project_phases SET title=?,description=?,body=?,image_path=COALESCE(?,image_path),sort_order=? WHERE id=? AND project_id=?')->execute([$phase['title'],$phase['description'],$phase['body'],$image,$order,$phase['id'],$project['id']]);
   else{
    $pdo->beginTransaction();$pdo->prepare('INSERT INTO project_phases(code,title,description,body,project_id,sort_order,image_path) VALUES(?,?,?,?,?,?,?)')->execute([bin2hex(random_bytes(16)),$phase['title'],$phase['description'],$phase['body'],$project['id'],$order,$image]);
    $phase['id']=(int)$pdo->lastInsertId();$pdo->prepare('UPDATE project_phases SET code=? WHERE id=?')->execute(['ph'.$phase['id'],$phase['id']]);$pdo->commit();
   }
   header('Location: projects.php?id='.$project['id'].'&phase_id='.$phase['id'].'&saved=phase');exit;
  }else throw new RuntimeException('Thao tác không hợp lệ.');
 }catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();$err=($ex instanceof RuntimeException&&!($ex instanceof PDOException))?$ex->getMessage():admin_save_error($ex);}
}
$projects=all_projects($pdo);admin_head('Dự án','projects');
?>
<div class="quick-actions project-page-actions"><a class="btn" href="projects.php?new=1">+ Thêm dự án</a><a class="btn admin-btn-outline" href="../projects.php" target="_blank" rel="noopener noreferrer">Xem website →</a></div>
<?php if($err): ?><p class="note err" role="alert"><?= e($err) ?></p><?php endif; ?>
<?php if(isset($_GET['saved'])): ?><p class="note" role="status">Đã lưu. Nội dung đã được cập nhật trên website.</p><?php endif; ?>
<?php if(isset($_GET['deleted'])): ?><p class="note" role="status">Đã xóa dự án cùng các giai đoạn và ảnh trong thư viện của dự án.</p><?php endif; ?>
<div class="project-admin-workspace"><nav class="project-admin-list" aria-label="Chọn dự án"><?php foreach($projects as $item): ?><div class="project-admin-item" data-project-item>
<a href="projects.php?id=<?= (int)$item['id'] ?>"<?= (int)$item['id']===(int)$project['id']?' aria-current="page"':'' ?>><?= e($item['title']) ?><small><?= (int)$item['phase_count'] ?> giai đoạn</small></a>
<form class="project-delete-short" method="post" action="projects.php?id=<?= (int)$item['id'] ?>" data-project-delete data-project-title="<?= e($item['title']) ?>">
<?= csrf_field() ?><input type="hidden" name="action" value="delete_project"/><input type="hidden" name="project_id" value="<?= (int)$item['id'] ?>"/>
<button type="submit" aria-label="Xóa dự án <?= e($item['title']) ?>">Xóa</button></form>
</div><?php endforeach; ?></nav>
<div><form class="form box" method="post" enctype="multipart/form-data" data-project-form><?= csrf_field() ?><input type="hidden" name="action" value="project"/>
<h2><?= $project['id']?'Thông tin dự án':'Dự án mới' ?></h2>
<label>Tên dự án<input name="title" maxlength="190" required value="<?= e($project['title']) ?>"/></label>
<label>Giới thiệu<textarea name="introduction" maxlength="10000" rows="4" required><?= e($project['introduction']) ?></textarea></label>
<label>Ảnh đại diện (JPG, PNG, WebP; tối đa 10MB)<input type="file" name="cover" accept="image/jpeg,image/png,image/webp"/></label>
<?php if($project['cover_image']): ?><img class="project-admin-cover" src="../<?= e($project['cover_image']) ?>" alt="Ảnh đại diện dự án"/><?php endif; ?>
<button class="btn" type="submit">Lưu dự án →</button></form>
<?php if($project['id']): ?><div class="quick-actions"><h2>Các giai đoạn</h2><a class="chip" href="projects.php?id=<?= (int)$project['id'] ?>&new_phase=1">+ Thêm giai đoạn</a><a class="chip" href="gallery.php?project=<?= (int)$project['id'] ?>">Ảnh của dự án</a></div>
<nav class="quick-actions" aria-label="Chọn giai đoạn"><?php foreach($phases as $item): ?><a class="chip" href="projects.php?id=<?= (int)$project['id'] ?>&phase_id=<?= (int)$item['id'] ?>"<?= (int)$phase['id']===(int)$item['id']?' aria-current="page"':'' ?>><?= e($item['position'].'. '.$item['title']) ?></a><?php endforeach; ?></nav>
<form class="form box" method="post" enctype="multipart/form-data" data-phase-form><?= csrf_field() ?><input type="hidden" name="action" value="phase"/>
<h2><?= $phase['id']?'Sửa giai đoạn':'Thêm giai đoạn' ?></h2><label>Tên giai đoạn<input name="title" maxlength="190" required value="<?= e($phase['title']) ?>"/></label>
<label>Mô tả ngắn<textarea name="description" rows="3" maxlength="1000" required><?= e($phase['description']) ?></textarea></label>
<label>Thứ tự hiển thị<input type="number" name="sort_order" min="1" max="10000" required value="<?= (int)$phase['sort_order'] ?>"/></label>
<label for="phaseBody">Nội dung chi tiết</label>
<div class="editor-toolbar" role="toolbar" aria-label="Công cụ định dạng bài viết">
  <select data-command="formatBlock" aria-label="Kiểu đoạn"><option value="p">Đoạn văn</option><option value="h2">Tiêu đề 2</option><option value="h3">Tiêu đề 3</option><option value="blockquote">Trích dẫn</option></select>
  <button type="button" data-command="bold" aria-label="In đậm"><strong>B</strong></button><button type="button" data-command="italic" aria-label="In nghiêng"><em>I</em></button><button type="button" data-command="underline" aria-label="Gạch chân"><u>U</u></button>
  <button type="button" data-command="justifyLeft" aria-label="Căn trái">≡</button><button type="button" data-command="justifyCenter" aria-label="Căn giữa">≡</button><button type="button" data-command="justifyRight" aria-label="Căn phải">≡</button>
  <button type="button" data-command="insertUnorderedList" aria-label="Danh sách">• List</button><button type="button" data-command="insertOrderedList" aria-label="Danh sách số">1. List</button><button type="button" data-command="createLink" aria-label="Chèn liên kết">Link</button><button type="button" data-command="insertImage" aria-label="Chèn ảnh">Ảnh</button>
</div>


<div id="phaseBody" class="rich-editor" contenteditable="true" role="textbox" aria-multiline="true" aria-label="Nội dung giai đoạn"><?= str_replace('src="assets/uploads/','src="../assets/uploads/',project_rich_text($phase['body'])) ?></div>
<textarea name="body" id="phaseSource" hidden><?= e($phase['body']) ?></textarea>
<label>Ảnh của giai đoạn (JPG, PNG, WebP; tối đa 10MB)<input type="file" name="photo" accept="image/jpeg,image/png,image/webp"/></label>
<?php if($phase['image_path']): ?><img class="project-admin-cover" src="../<?= e($phase['image_path']) ?>" alt="Ảnh giai đoạn"/><?php endif; ?>
<button class="btn" type="submit">Lưu giai đoạn →</button><a href="../project.php?id=<?= (int)$project['id'] ?><?= $phase['id']?'#'.e($phase['code']??'ph'.$phase['id']):'' ?>" target="_blank" rel="noopener noreferrer">Xem trên website →</a></form>
<script>window.editorCsrf=<?= json_encode(csrf()) ?>;</script>
<?php endif; ?></div></div><?php admin_foot();
