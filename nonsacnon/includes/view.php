<?php
const CATS=['Văn hóa','Làng nghề','Nghệ nhân','Thế hệ trẻ'];
const GCATS=['Làng nghề','Nghệ nhân','Nón lá','Workshop','Tác phẩm'];
const TONES=['red'=>'#9E2F25','leaf'=>'#687052','gold'=>'#C58A45','brown'=>'#30251F'];
const PHOTO_ASSETS=[
  'A1'=>['base'=>'non-lua-hoa-dao','alt'=>'Chiếc nón lụa trắng vẽ cô gái áo dài dưới cành hoa đào đỏ, được giơ lên trước mặt','color'=>'#d6b5a1'],
  'A2'=>['base'=>'non-xanh-sen','alt'=>'Chiếc nón màu xanh navy vẽ sen trắng và lá sen xanh, đính đá lấp lánh','color'=>'#53637f'],
  'A3'=>['base'=>'non-la-moc','alt'=>'Mặt trong chiếc nón lá mộc với những đường đan nan tỏa ra từ đỉnh','color'=>'#b7a17f'],
  'A4'=>['base'=>'non-lua-xanh-sen-hong','alt'=>'Chiếc nón lụa xanh lá thêu hoa văn và vẽ sen hồng','color'=>'#91a65e'],
  'A5'=>['base'=>'goc-trung-bay-non','alt'=>'Góc trưng bày nón lá, đèn tre, quạt và tranh dân gian treo trên vách tre','color'=>'#b76d2f'],
  'A6'=>['base'=>'nghe-nhan-khau-non','alt'=>'Người thợ lớn tuổi đang khâu những vòng chỉ trên chiếc nón lụa vàng vẽ chuồn chuồn','color'=>'#b96d3d']
];
function photo_asset($key,$width=1200){
  $a=PHOTO_ASSETS[$key]??null; if(!$a) return null;
  return ['src'=>'assets/uploads/non-sac-non/'.$a['base'].'-'.$width.'.webp','srcset'=>implode(', ',array_map(function($w)use($a){return 'assets/uploads/non-sac-non/'.$a['base'].'-'.$w.'.webp '.$w.'w';},[800,1200,1600])),'thumb'=>'assets/uploads/non-sac-non/'.$a['base'].'-thumb.webp','alt'=>$a['alt'],'color'=>$a['color']];
}
function picture($asset,$label=null,$class='',$loading='lazy',$position='50% 50%',$sizes='(min-width: 900px) 50vw, 100vw'){
  if(!$asset) return '';
  $alt=$label?:$asset['alt']; $priority=$loading==='eager'?' fetchpriority="high"':'';
  return '<picture class="'.e($class).'" style="background:'.e($asset['color']).';background-position:'.e($position).';display:block"><source type="image/webp" srcset="'.e($asset['srcset']).'" sizes="'.e($sizes).'"><img src="'.e($asset['src']).'" srcset="'.e($asset['srcset']).'" sizes="'.e($sizes).'" alt="'.e($alt).'" width="1200" height="800" loading="'.e($loading).'"'.$priority.' style="width:100%;height:100%;object-fit:cover;object-position:'.e($position).';display:block"></picture>';
}
function asset_from_path($img){
  if(!$img) return null;
  $path=preg_replace('/-(?:800|1200|1600)\.webp$/','',$img);
  if($path===$img) return ['src'=>$img,'srcset'=>$img,'alt'=>'','color'=>'#EADFC4'];
  return ['src'=>$img,'srcset'=>preg_replace('/-1200\.webp$/','-800.webp 800w, '.$path.'-1200.webp 1200w, '.$path.'-1600.webp 1600w',$img),'alt'=>'','color'=>'#EADFC4'];
}
function story_asset($title,$cover=null){
  if($cover) return asset_from_path($cover);
  if(stripos($title,'Chuông')!==false || stripos($title,'làng nghề')!==false) return photo_asset('A5');
  if(stripos($title,'nón lá')!==false || stripos($title,'lớp lá')!==false) return photo_asset('A3');
  if(stripos($title,'workshop')!==false || stripos($title,'xưa họa nay')!==false) return photo_asset('A4');
  if(stripos($title,'hiện đại')!==false) return photo_asset('A2');
  if(stripos($title,'nghề')!==false) return photo_asset('A5');
  if(stripos($title,'thợ')!==false) return photo_asset('A6');
  return photo_asset('A3');
}
function orient_gd($im,$orientation){
  if($orientation===3) return imagerotate($im,180,0);
  if($orientation===6) return imagerotate($im,270,0);
  if($orientation===8) return imagerotate($im,90,0);
  if($orientation===2 && function_exists('imageflip')) imageflip($im,IMG_FLIP_HORIZONTAL);
  if($orientation===4 && function_exists('imageflip')) imageflip($im,IMG_FLIP_VERTICAL);
  if($orientation===5 && function_exists('imageflip')){imageflip($im,IMG_FLIP_HORIZONTAL);return imagerotate($im,270,0);}
  if($orientation===7 && function_exists('imageflip')){imageflip($im,IMG_FLIP_HORIZONTAL);return imagerotate($im,90,0);}
  return $im;
}
function media($img,$tone,$label,$h=300,$loading='lazy'){
  $asset=$img?asset_from_path($img):story_asset($label);
  if($asset) return picture($asset,$label,'media-picture',$loading);
  return picture(photo_asset('A3'),$label,'media-picture',$loading);
}
function slugify($s){
  if(class_exists('Normalizer')) $s=preg_replace('/\p{Mn}/u','',Normalizer::normalize($s,Normalizer::FORM_D));
  $s=str_replace(['đ','Đ'],'d',$s); $s=strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/','-',$s),'-'));
  return $s?:bin2hex(random_bytes(3));
}
function store_original_upload(array $file,$ext){
  if(!in_array($ext,['jpg','png','webp'],true)||!@getimagesize($file['tmp_name']))throw new RuntimeException('Tệp ảnh không hợp lệ.');
  $dir=__DIR__.'/../assets/uploads';
  if(!is_dir($dir)&&!@mkdir($dir,0775,true))throw new RuntimeException('Không thể tạo thư mục lưu ảnh.');
  if(!is_writable($dir))throw new RuntimeException('Thư mục assets/uploads chưa có quyền ghi trên host.');
  $path='assets/uploads/'.bin2hex(random_bytes(16)).'.'.$ext;
  if(!@move_uploaded_file($file['tmp_name'],__DIR__.'/../'.$path))throw new RuntimeException('Không thể lưu ảnh gốc trên host. Kiểm tra quyền ghi assets/uploads.');
  return $path;
}
function save_upload($f){ // Xử lý ảnh bằng Imagick hoặc GD, tối đa 10MB
  if(empty($_FILES[$f])||$_FILES[$f]['error']===UPLOAD_ERR_NO_FILE) return null;
  $x=$_FILES[$f];
  if($x['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Tải ảnh lên thất bại, vui lòng thử lại.');
  if($x['size']>10*1024*1024) throw new RuntimeException('Ảnh gốc không được lớn hơn 10MB.');
  $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][(new finfo(FILEINFO_MIME_TYPE))->file($x['tmp_name'])]??null;
  if(!$ext) throw new RuntimeException('Chỉ chấp nhận ảnh JPG, PNG hoặc WebP.');
  $dir=__DIR__.'/../assets/uploads';
  if(!is_dir($dir) && !mkdir($dir,0775,true)) throw new RuntimeException('Không thể tạo thư mục lưu ảnh.');
  $info=@getimagesize($x['tmp_name']); if(!$info) throw new RuntimeException('Tệp ảnh không hợp lệ.');
  $stem=bin2hex(random_bytes(8)); $widths=[800,1200,1600];
  if(class_exists('Imagick')) {
    try {
    $source=new Imagick($x['tmp_name']); $source->autoOrient(); $source->setImageFormat('webp'); $source->stripImage();
    foreach($widths as $w){$im=clone $source; $im->resizeImage($w,0,Imagick::FILTER_LANCZOS,1,true); $im->setImageCompressionQuality(80); if(!$im->writeImage("$dir/$stem-$w.webp"))throw new ImagickException('Cannot write WebP'); $im->clear();$im->destroy();}
    $thumb=clone $source; $thumb->cropThumbnailImage(480,480); $thumb->setImageCompressionQuality(80); if(!$thumb->writeImage("$dir/$stem-thumb.webp"))throw new ImagickException('Cannot write thumbnail'); $thumb->clear();$thumb->destroy(); $source->clear();$source->destroy();
    return 'assets/uploads/'.$stem.'-1200.webp';
    }catch(ImagickException $ex){
      error_log('[NonSacNon upload] Imagick processing failed; using original image: '.$ex->getMessage());
      foreach(['800','1200','1600','thumb'] as $suffix){$partial="$dir/$stem-$suffix.webp";if(is_file($partial))@unlink($partial);}
      unset($source,$im,$thumb);
      return store_original_upload($x,$ext);
    }
  }
  if(function_exists('imagecreatefromjpeg') && function_exists('imagewebp')) {
    $decoder=['jpg'=>'imagecreatefromjpeg','png'=>'imagecreatefrompng','webp'=>'imagecreatefromwebp'][$ext];
    if(!function_exists($decoder))return store_original_upload($x,$ext);
    $create=['jpg'=>'imagecreatefromjpeg','png'=>'imagecreatefrompng','webp'=>'imagecreatefromwebp'][$ext]; $source=$create($x['tmp_name']); if(!$source) throw new RuntimeException('Không thể đọc ảnh trên máy chủ.');
    $exif=$ext==='jpg'&&function_exists('exif_read_data')?@exif_read_data($x['tmp_name']):[]; $source=orient_gd($source,(int)($exif['Orientation']??1));
    $ow=imagesx($source);$oh=imagesy($source);
    foreach($widths as $w){$nh=(int)round($oh*$w/$ow);$im=imagecreatetruecolor($w,$nh);imagecopyresampled($im,$source,0,0,0,0,$w,$nh,$ow,$oh);imagewebp($im,"$dir/$stem-$w.webp",80);imagedestroy($im);}
    $side=min($ow,$oh);$thumb=imagecreatetruecolor(480,480);imagecopyresampled($thumb,$source,0,0,(int)(($ow-$side)/2),(int)(($oh-$side)/2),480,480,$side,$side);imagewebp($thumb,"$dir/$stem-thumb.webp",80);imagedestroy($thumb);imagedestroy($source);
  } else return store_original_upload($x,$ext);
  return 'assets/uploads/'.$stem.'-1200.webp';
}
function sanitize_rich_text($html){
  $html=str_replace('../assets/uploads/','assets/uploads/',$html);
  $html=strip_tags((string)$html,'<p><br><b><i><strong><em><u><s><h2><h3><ul><ol><li><blockquote><a><img><div><span>');
  $html=preg_replace('/\s+on\w+\s*=\s*(["\']).*?\1/iu','',$html);
  $html=preg_replace('/javascript\s*:/iu','',$html);
  $html=preg_replace_callback('/<([a-z0-9]+)([^>]*)>/i',function($m){
    $tag=strtolower($m[1]); if(in_array($tag,['br','img'],true)) $attrs=$m[2]; else $attrs=$m[2];
    preg_match_all('/\s+(href|src|alt|title|target|rel|style)\s*=\s*(["\'])(.*?)\2/iu',$attrs,$matches,PREG_SET_ORDER);
    $allowed='';
    foreach($matches as $a){
      $name=strtolower($a[1]); $value=$a[3];
      if($name==='href' && !preg_match('#^(https?://|mailto:)#i',$value)) continue;
      if($name==='src' && !preg_match('#^(assets/uploads/|https?://)#i',$value)) continue;
      if($name==='style'){
        $styles=[];
        foreach(explode(';',html_entity_decode($value,ENT_QUOTES|ENT_HTML5,'UTF-8')) as $declaration){
          if(preg_match('/^\s*(text-align|font-weight|font-style|text-decoration(?:-line)?)\s*:\s*(.*?)\s*$/i',$declaration,$css)){
            $property=strtolower($css[1]); $setting=strtolower($css[2]);
            $values=['text-align'=>['left','center','right','justify'],'font-weight'=>['normal','bold','400','700'],'font-style'=>['normal','italic'],'text-decoration'=>['none','underline','line-through','underline line-through'],'text-decoration-line'=>['none','underline','line-through','underline line-through']];
            if(in_array($setting,$values[$property],true)) $styles[]=$property.': '.$setting;
          }
        }
        $value=implode('; ',$styles); if($value==='') continue;
      }
      $allowed.=' '.$name.'="'.e($value).'"';
    }
    return '<'.$tag.$allowed.'>';
  },$html);
  return trim($html);
}

const LIBRARY_PHASES=[
'ph1'=>['title'=>'Chuyện nghề sắc nón','description'=>'Khám phá làng nghề, nghệ nhân và những đôi tay gìn giữ nghề làm nón lá.'],
'ph2'=>['title'=>'Nón xưa - Họa nay','description'=>'Nón lá truyền thống gặp hội họa, màu sắc và góc nhìn sáng tạo của người trẻ.'],
'ph3'=>['title'=>'Chạm Di sản','description'=>'Tự tay trải nghiệm làm nón và sáng tạo trong những buổi workshop chạm vào di sản.'],
'ph4'=>['title'=>'Nhịp cầu văn hóa','description'=>'Kết nối các thế hệ và cộng đồng qua câu chuyện nón lá, tiếp nối những giá trị văn hóa.']];
function library_phase($category){return ['Nón lá'=>'ph2','Làng nghề'=>'ph1','Nghệ nhân'=>'ph1','Tác phẩm'=>'ph4','Workshop'=>'ph3'][$category]??'ph4';}
function library_phase_item($item){
  if(isset($item['phase_id'])&&in_array((int)$item['phase_id'],[1,2,3,4],true))return 'ph'.(int)$item['phase_id'];
  $path=strtolower((string)($item['image_path']??''));
  if(strpos($path,'goc-trung-bay-non')!==false||strpos($path,'nghe-nhan-khau-non')!==false)return 'ph1';
  if(strpos($path,'non-la-moc')!==false||strpos($path,'non-lua-xanh-sen-hong')!==false)return 'ph2';
  if(strpos($path,'non-lua-hoa-dao')!==false)return 'ph3';
  if(strpos($path,'non-xanh-sen')!==false)return 'ph4';
  return library_phase($item['category']??'');
}
function valid_workshop_form_url($url){
if(!is_string($url)||strlen($url)>2048||!filter_var($url,FILTER_VALIDATE_URL))return false;
$p=parse_url($url);if(($p['scheme']??'')!=='https'||isset($p['user'])||isset($p['pass'])||isset($p['port']))return false;
$h=strtolower($p['host']??'');$path=$p['path']??'';
return ($h==='forms.gle'&&strlen(trim($path,'/'))>0)||($h==='docs.google.com'&&preg_match('#^/forms/(?:u/\d+/)?d/(?:e/)?[^/]+/viewform(?:/|$)#',$path));
}

function workshop_registration_url(array $workshop): string {
$url=trim((string)($workshop['google_form_url']??''));
return valid_workshop_form_url($url)?$url:'';
}
