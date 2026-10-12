<?php
function all_projects(PDO $pdo){
  return $pdo->query('SELECT p.*,COUNT(ph.id) AS phase_count FROM projects p LEFT JOIN project_phases ph ON ph.project_id=p.id GROUP BY p.id ORDER BY p.id')->fetchAll();
}
function find_project(PDO $pdo,$id){$q=$pdo->prepare('SELECT * FROM projects WHERE id=?');$q->execute([(int)$id]);return $q->fetch();}
function project_phases(PDO $pdo,$projectId=1){
  $q=$pdo->prepare('SELECT * FROM project_phases WHERE project_id=? ORDER BY sort_order,id');$q->execute([(int)$projectId]);$phases=[];
  foreach($q->fetchAll() as $i=>$phase){$phase['position']=$i+1;$phases[$phase['code']]=$phase;}return $phases;
}
function all_project_phases(PDO $pdo){return $pdo->query('SELECT ph.*,p.title AS project_title FROM project_phases ph JOIN projects p ON p.id=ph.project_id ORDER BY p.id,ph.sort_order,ph.id')->fetchAll();}
function project_gallery(PDO $pdo,$projectId){
  $q=$pdo->prepare('SELECT g.*,ph.code AS phase FROM gallery_items g JOIN project_phases ph ON ph.id=g.phase_id WHERE ph.project_id=? ORDER BY g.id DESC');$q->execute([(int)$projectId]);$rows=$q->fetchAll();
  if((int)$projectId===1){
    $featured=[];
    foreach(['A6'=>['Đôi tay giữ nghề','Nghệ nhân','red','ph1'],'A5'=>['Không gian làng nghề','Làng nghề','gold','ph1'],'A1'=>['Nón lụa hoa đào','Tác phẩm','red','ph3'],'A2'=>['Nón xanh sắc sen','Tác phẩm','leaf','ph4'],'A3'=>['Nón lá mộc','Nón lá','brown','ph2'],'A4'=>['Nón lụa sen hồng','Tác phẩm','leaf','ph2']] as $key=>$item){$asset=photo_asset($key);$featured[]=['title'=>$item[0],'category'=>$item[1],'tone'=>$item[2],'phase'=>$item[3],'image_path'=>$asset['src'],'alt'=>$asset['alt']];}
    $rows=array_merge($featured,$rows);
  }
  return $rows;
}
function project_rich_text($html){
  $html=str_replace('../assets/uploads/','assets/uploads/',(string)$html);
  $doc=new DOMDocument('1.0','UTF-8');
  $previous=libxml_use_internal_errors(true);
  $doc->loadHTML('<?xml encoding="UTF-8"><div id="phase-root">'.$html.'</div>',LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD|LIBXML_NONET);
  libxml_clear_errors();libxml_use_internal_errors($previous);
  $root=$doc->getElementById('phase-root');if(!$root)return '';
  $clean=function($node)use(&$clean,$doc){
    foreach(iterator_to_array($node->childNodes) as $child){
      if($child->nodeType===XML_COMMENT_NODE){$node->removeChild($child);continue;}
      if($child->nodeType!==XML_ELEMENT_NODE)continue;
      $tag=strtolower($child->nodeName);
      if(in_array($tag,['script','style','iframe','object','embed','svg','math','form','input','button','textarea','select'],true)){$node->removeChild($child);continue;}
      $clean($child);
      if(!in_array($tag,['p','br','strong','b','em','i','u','s','h2','h3','ul','ol','li','blockquote','a','img','div','span'],true)){
        while($child->firstChild)$node->insertBefore($child->firstChild,$child);$node->removeChild($child);continue;
      }
      $attributes=[];foreach(iterator_to_array($child->attributes) as $attr)$attributes[$attr->name]=$attr->value;
      foreach(array_keys($attributes) as $attr)$child->removeAttribute($attr);
      if(in_array($tag,['p','div','h2','h3','blockquote'],true)&&preg_match('/(?:^|;)\s*text-align\s*:\s*(left|center|right|justify)\s*(?:;|$)/i',$attributes['style']??'',$align))$child->setAttribute('style','text-align:'.strtolower($align[1]));
      if($tag==='a'){
        $href=trim($attributes['href']??'');
        if(preg_match('#^(https?://|mailto:)#i',$href)&&filter_var($href,FILTER_VALIDATE_URL))$child->setAttribute('href',$href);
        if(strpos($href,'mailto:')===0&&filter_var(substr($href,7),FILTER_VALIDATE_EMAIL))$child->setAttribute('href',$href);
        if($child->hasAttribute('href')){$child->setAttribute('target','_blank');$child->setAttribute('rel','noopener noreferrer');}
      }
      if($tag==='img'){
        $src=trim($attributes['src']??'');
        if(!preg_match('#^assets/uploads/[a-zA-Z0-9_./-]+\.(?:jpg|jpeg|png|webp)$#i',$src)||strpos($src,'..')!==false){$node->removeChild($child);continue;}
        $child->setAttribute('src',$src);$child->setAttribute('alt',$attributes['alt']??'Ảnh dự án');$child->setAttribute('loading','lazy');
      }
    }
  };
  $clean($root);$result='';foreach($root->childNodes as $child)$result.=$doc->saveHTML($child);return trim($result);
}
function save_project_upload($field){
  if(extension_loaded('gd')||class_exists('Imagick'))return save_upload($field);
  if(empty($_FILES[$field])||$_FILES[$field]['error']===UPLOAD_ERR_NO_FILE)return null;
  $file=$_FILES[$field];
  if($file['error']!==UPLOAD_ERR_OK)throw new RuntimeException('Tải ảnh lên thất bại.');
  if($file['size']>10*1024*1024)throw new RuntimeException('Ảnh không được lớn hơn 10MB.');
  $type=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);$ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$type]??null;
  if(!$ext||!@getimagesize($file['tmp_name']))throw new RuntimeException('Chỉ chấp nhận ảnh JPG, PNG hoặc WebP hợp lệ.');
  $dir=__DIR__.'/../assets/uploads';if(!is_dir($dir)&&!mkdir($dir,0775,true))throw new RuntimeException('Không thể tạo thư mục ảnh.');
  $path='assets/uploads/'.bin2hex(random_bytes(16)).'.'.$ext;
  if(!move_uploaded_file($file['tmp_name'],__DIR__.'/../'.$path))throw new RuntimeException('Không thể lưu ảnh.');
  return $path;
}
