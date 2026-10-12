<?php require __DIR__.'/_layout.php'; require_perm('workshops'); post_guard();
$formNotice='';
if($_SERVER['REQUEST_METHOD']==='POST'&&($_POST['action']??'')==='delete'){
  $deleteId=filter_var($_POST['id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
  if(!$deleteId)$formNotice='Workshop không hợp lệ.';
  else{
    try{
      $pdo->beginTransaction();
      $find=$pdo->prepare('SELECT id FROM workshops WHERE id=? FOR UPDATE');$find->execute([$deleteId]);
      if(!$find->fetchColumn()){$pdo->rollBack();$formNotice='Workshop không còn tồn tại.';}
      else{
        $count=$pdo->prepare('SELECT COUNT(*) FROM registrations WHERE workshop_id=?');$count->execute([$deleteId]);
        if((int)$count->fetchColumn()>0){$pdo->rollBack();$formNotice='Workshop đã có người đăng ký. Hãy bỏ chọn Đang mở đăng ký và bấm Lưu để ẩn buổi này, giữ nguyên thông tin người tham gia.';}
        else{$pdo->prepare('DELETE FROM workshops WHERE id=?')->execute([$deleteId]);$pdo->commit();$formNotice='Đã xóa workshop.';}
      }
    }catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();$formNotice=admin_save_error($ex);}
  }
}
if($_SERVER['REQUEST_METHOD']==='POST'&&!in_array($_POST['action']??'',['google_form','delete'],true)){
  $submittedUrl=$_POST['google_form_url']??'';
  $formUrl=is_string($submittedUrl)?trim($submittedUrl):'';
  if(!is_string($submittedUrl)||($formUrl!==''&&!valid_workshop_form_url($formUrl)))$formNotice='Vui lòng nhập link HTTPS dành cho người trả lời của Google Form (forms.gle hoặc docs.google.com/forms), tối đa 2048 ký tự.';
  else{
    $d=[trim($_POST['title']??''),($_POST['event_date']??'')?:null,trim($_POST['location']??'')?:'Sẽ cập nhật',max(1,(int)($_POST['capacity']??30)),isset($_POST['is_open'])?1:0,$formUrl?:null];
    if($d[0]!==''){
      $id=(int)($_POST['id']??0);
      try{
        if($id)$pdo->prepare('UPDATE workshops SET title=?,event_date=?,location=?,capacity=?,is_open=?,google_form_url=? WHERE id=?')->execute([...$d,$id]);
        else $pdo->prepare('INSERT INTO workshops(title,event_date,location,capacity,is_open,google_form_url) VALUES(?,?,?,?,?,?)')->execute($d);
        $formNotice='Đã lưu workshop và biểu mẫu đăng ký riêng.';
      }catch(Throwable $ex){$formNotice=admin_save_error($ex);}
    }else $formNotice='Vui lòng nhập tên workshop.';
  }
}
$rows=$pdo->query('SELECT * FROM workshops ORDER BY id DESC')->fetchAll();
$rows[]=['id'=>0,'title'=>'','event_date'=>null,'location'=>'','capacity'=>30,'is_open'=>1,'google_form_url'=>null];
admin_head('Workshop','workshops');
echo '<p>Trang chủ và trang Workshop hiển thị tất cả buổi đang mở đăng ký, từ mới đến cũ.</p>';
?>
<?php if($formNotice): ?><p class="note" role="status"><?= e($formNotice) ?></p><?php endif; ?>

<div class="quick-actions"><button class="btn" type="button" data-workshop-toggle aria-controls="new-workshop-form" aria-expanded="false">+ Thêm mới workshop</button></div>
<?php foreach($rows as $r): ?>
<form class="form box"<?= !$r['id'] ? ' id="new-workshop-form" hidden' : '' ?> method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"/>
<strong><?= $r['id']?e($r['title']):'Thêm buổi mới' ?></strong>
<label>Tên<input name="title" value="<?= e($r['title']) ?>"/></label>
<label>Thời gian<input type="datetime-local" name="event_date" value="<?= $r['event_date']?e(date('Y-m-d\TH:i',strtotime($r['event_date']))):'' ?>"/></label>
<label>Địa điểm<input name="location" value="<?= e($r['location']) ?>"/></label>
<label>Số chỗ<input type="number" min="1" name="capacity" value="<?= (int)$r['capacity'] ?>"/></label>
<label>Google Form của workshop này<input name="google_form_url" type="url" maxlength="2048" placeholder="https://forms.gle/..." value="<?= e($r['google_form_url']??'') ?>"/><small>Để trống nếu chưa có biểu mẫu đăng ký. Mỗi workshop sử dụng link riêng.</small></label>
<label><span><input type="checkbox" name="is_open" <?= $r['is_open']?'checked':'' ?>/> Đang mở đăng ký</span></label>
<div class="workshop-form-actions"><button class="btn" type="submit"><?= $r['id'] ? 'Lưu' : 'Thêm workshop' ?></button><?php if($r['id']): ?><button class="btn admin-btn-outline" type="submit" name="action" value="delete" formnovalidate onclick="return confirm('Xóa workshop này? Thao tác không thể hoàn tác.');">Xóa workshop</button><?php else: ?><button class="btn admin-btn-outline" type="button" data-workshop-cancel>Đóng</button><?php endif; ?></div></form>
<?php endforeach; ?>

<?php admin_foot();
