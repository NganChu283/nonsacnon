<?php
require_once __DIR__.'/db.php';
// Phân quyền: admin làm mọi thứ; editor lo nội dung; organizer lo workshop và liên hệ.
const ROLES=['admin'=>'Quản trị viên','editor'=>'Biên tập viên','organizer'=>'Điều phối workshop'];
const PERMS=['admin'=>['*'],'editor'=>['editor','stories','gallery','projects'],'organizer'=>['organizer','workshops','registrations','messages']];
function user(){ return $_SESSION['u'] ?? null; }
function can($p){ $u=user(); if(!$u) return false; if($p==='dash') return true; $r=PERMS[$u['role']]??[]; return in_array('*',$r,true)||in_array($p,$r,true); }
function require_perm($p){
  if(!user()){ header('Location: login.php'); exit; }
  if(!can($p)){ http_response_code(403); exit('Bạn không có quyền truy cập mục này.'); }
}
function post_guard(){ if($_SERVER['REQUEST_METHOD']==='POST' && !hash_equals($_SESSION['t']??'',$_POST['t']??'')){ http_response_code(400); exit('Phiên không hợp lệ, hãy tải lại trang.'); } }
