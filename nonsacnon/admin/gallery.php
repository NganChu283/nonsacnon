<?php
require __DIR__.'/_layout.php';require_perm('gallery');post_guard();
$err='';$notice='';$phaseRows=all_project_phases($pdo);$phases=[];foreach($phaseRows as $item)$phases[$item['id']]=$item;$projects=all_projects($pdo);$projectId=filter_var($_GET['project']??0,FILTER_VALIDATE_INT)?:0;
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(isset($_POST['del'])){
    $q=$pdo->prepare('SELECT image_path FROM gallery_items WHERE id=?');$q->execute([(int)$_POST['del']]);$path=$q->fetchColumn();
    if($path&&strpos($path,'assets/uploads/')===0)@unlink(__DIR__.'/../'.$path);
    $pdo->prepare('DELETE FROM gallery_items WHERE id=?')->execute([(int)$_POST['del']]);
  }else{
    $phaseId=filter_var($_POST['phase_id']??null,FILTER_VALIDATE_INT);
    if(!$phaseId||!isset($phases[$phaseId]))$err='Vui lòng chọn giai đoạn hợp lệ.';
    elseif(($_POST['action']??'')==='phase'){
      $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
      if(!$id)$err='Ảnh không hợp lệ.';
      else{$q=$pdo->prepare('UPDATE gallery_items SET phase_id=? WHERE id=?');$q->execute([$phaseId,$id]);$notice='Đã lưu giai đoạn của ảnh.';}
    }else{
      $chosenProject=filter_var($_POST['project_id']??null,FILTER_VALIDATE_INT);
      if(!$chosenProject||(int)$phases[$phaseId]['project_id']!==$chosenProject){$err='Giai đoạn không thuộc dự án đã chọn.';}
      $title=$_POST['title']??'';
      if($err===''&&(!is_string($title)||trim($title)===''||mb_strlen(trim($title))>190))$err='Tên ảnh cần từ 1 đến 190 ký tự.';
      if($err===''){
        $cat=in_array($_POST['category']??'',GCATS,true)?$_POST['category']:GCATS[0];$tone=array_key_exists($_POST['tone']??'',TONES)?$_POST['tone']:'red';
        try{$image=save_project_upload('img');$pdo->prepare('INSERT INTO gallery_items(title,category,tone,image_path,phase_id) VALUES(?,?,?,?,?)')->execute([trim($title),$cat,$tone,$image,$phaseId]);$notice='Đã thêm ảnh.';}
        catch(Throwable $ex){$err=($ex instanceof RuntimeException&&!($ex instanceof PDOException))?$ex->getMessage():admin_save_error($ex);}
      }
    }
  }
}
if($projectId){$q=$pdo->prepare('SELECT g.* FROM gallery_items g JOIN project_phases ph ON ph.id=g.phase_id WHERE ph.project_id=? ORDER BY g.id DESC');$q->execute([$projectId]);$rows=$q->fetchAll();}else $rows=$pdo->query('SELECT * FROM gallery_items ORDER BY id DESC')->fetchAll();
admin_head('Thư viện ảnh','gallery');
?>
<?php if($err): ?><p class="note err" role="alert"><?= e($err) ?></p><?php endif; ?>
<?php if($notice): ?><p class="note" role="status"><?= e($notice) ?></p><?php endif; ?>
<p>Chọn giai đoạn cho từng ảnh. Quản lý tên và nội dung giai đoạn trong <a href="projects.php">Dự án</a>.</p>
<form class="form project-gallery-form" method="post" enctype="multipart/form-data"><?= csrf_field() ?>
<label>Tên ảnh<input name="title" maxlength="190" required/></label>
<label>Danh mục<select name="category"><?php foreach(GCATS as $c) echo '<option>'.e($c).'</option>'; ?></select></label>
<label>Dự án<select name="project_id" required><?php foreach($projects as $item): ?><option value="<?= (int)$item['id'] ?>"<?= (int)$item['id']===$projectId?' selected':'' ?>><?= e($item['title']) ?></option><?php endforeach; ?></select></label>
<label>Giai đoạn<select name="phase_id" required><?php foreach($phases as $phase): ?><option data-project="<?= (int)$phase['project_id'] ?>" value="<?= (int)$phase['id'] ?>"><?= e($phase['project_title'].' — '.$phase['title']) ?></option><?php endforeach; ?></select></label>
<label>Ảnh gốc (jpg, png, webp, tối đa 10MB)<input type="file" name="img" accept="image/jpeg,image/png,image/webp"/></label>
<label>Màu minh họa nếu chưa có ảnh<select name="tone"><?php foreach(array_keys(TONES) as $tone) echo '<option>'.e($tone).'</option>'; ?></select></label>
<button class="btn" type="submit">Thêm ảnh</button></form>
<div class="gallery-table"><table><tr><th>Ảnh</th><th>Tên</th><th>Danh mục</th><th>Giai đoạn</th><th></th></tr>
<?php foreach($rows as $row): ?><tr><td><?php if($row['image_path']): ?><img class="admin-thumb" src="../<?= e($row['image_path']) ?>" alt="<?= e($row['title']) ?>" loading="lazy"/><?php else: ?>Minh họa<?php endif; ?></td><td><?= e($row['title']) ?></td><td><?= e($row['category']) ?></td>
<td><form class="gallery-phase-actions" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="phase"/><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"/>
<select name="phase_id" aria-label="Giai đoạn của <?= e($row['title']) ?>"><?php foreach($phases as $phase): ?><option value="<?= (int)$phase['id'] ?>"<?= (int)($row['phase_id']??0)===(int)$phase['id']?' selected':'' ?>><?= e($phase['project_title'].' — '.$phase['title']) ?></option><?php endforeach; ?></select><button class="admin-action-save" type="submit">Lưu</button></form></td>
<td><form method="post" onsubmit="return confirm('Xóa ảnh này?')"><?= csrf_field() ?><button class="admin-action-delete" name="del" value="<?= (int)$row['id'] ?>">Xóa</button></form></td></tr><?php endforeach; ?></table></div>
<?php admin_foot();
