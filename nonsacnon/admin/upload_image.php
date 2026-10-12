<?php
require __DIR__.'/../includes/auth.php';
$projectUpload=($_POST['context']??'')==='project';
require_perm($projectUpload?'projects':'stories');
header('Content-Type: application/json; charset=utf-8');
if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); echo json_encode(['error'=>'Phương thức không hợp lệ.']); exit; }
if(!hash_equals($_SESSION['t']??'',$_POST['t']??'')){ http_response_code(400); echo json_encode(['error'=>'Phiên làm việc hết hạn.']); exit; }
try {
  $path=$projectUpload?save_project_upload('image'):save_upload('image');
  if(!$path) throw new RuntimeException('Chưa chọn ảnh.');
  echo json_encode(['url'=>'../'.$path],JSON_UNESCAPED_SLASHES);
} catch(RuntimeException $ex) {
  http_response_code(422); echo json_encode(['error'=>$ex->getMessage()],JSON_UNESCAPED_UNICODE);
}
