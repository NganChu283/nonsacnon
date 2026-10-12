<?php
require __DIR__.'/../includes/auth.php';
require_perm('dash');
header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: no-store');
echo "NON SAC NON - KIEM TRA HOST (chi doc, khong them du lieu)\n";
echo 'PHP: '.PHP_VERSION."\n";
foreach(['finfo','Imagick'] as $name)echo $name.': '.(class_exists($name)?'OK':'khong co')."\n";
foreach(['mb_strlen','exif_read_data','imagecreatefromjpeg','imagecreatefrompng','imagecreatefromwebp','imagewebp'] as $name)echo $name.': '.(function_exists($name)?'OK':'khong co')."\n";
foreach(['upload_max_filesize','post_max_size','memory_limit','max_execution_time'] as $name)echo $name.': '.ini_get($name)."\n";
echo 'assets/uploads ghi duoc: '.(is_writable(__DIR__.'/../assets/uploads')?'CO':'KHONG')."\n";
echo 'includes ghi duoc: '.(is_writable(__DIR__.'/../includes')?'CO':'KHONG')."\n";
echo 'Ket noi MySQL: '.json_encode($pdo->query('SELECT @@character_set_client AS client,@@character_set_connection AS connection')->fetch())."\n";
$schema=['contact_messages'=>['id','name','email','phone','message','created_at','is_read'],'gallery_items'=>['id','title','category','tone','image_path','created_at','phase_id'],'project_phases'=>['id','code','title','description','body','updated_at','project_id','sort_order','image_path'],'projects'=>['id','title','introduction','cover_image','created_at','updated_at'],'registrations'=>['id','workshop_id','name','email','phone','created_at'],'stories'=>['id','slug','title','category','excerpt','body','cover_image','tone','status','author_id','created_at'],'users'=>['id','name','email','password_hash','role','is_active','created_at'],'workshops'=>['id','title','event_date','location','capacity','is_open','google_form_url']];
$columnQuery=$pdo->prepare('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?');
foreach($schema as $table=>$columns){
 $columnQuery->execute([$table]);$actual=$columnQuery->fetchAll(PDO::FETCH_COLUMN);
 if(!$actual){echo $table.": THIEU BANG\n";continue;}
 $missing=array_diff($columns,$actual);
 echo $table.': '.($missing?'THIEU COT: '.implode(', ',$missing):'OK')."\n";
}
if(can('admin')){
 $file=__DIR__.'/../includes/admin-errors.log';
 if(is_file($file)){
  $handle=fopen($file,'rb');$size=filesize($file);if($size>8192)fseek($handle,-8192,SEEK_END);
  $lines=explode("\n",trim(stream_get_contents($handle)));fclose($handle);
  echo "\nLOI LUU GAN DAY:\n".implode("\n",array_slice($lines,-5))."\n";
 }
}
