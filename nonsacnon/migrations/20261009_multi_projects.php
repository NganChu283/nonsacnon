<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../includes/db.php';
$pdo->exec("CREATE TABLE IF NOT EXISTS projects(id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,title VARCHAR(190) NOT NULL,introduction TEXT NOT NULL,cover_image VARCHAR(255) NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
$pdo->prepare("INSERT INTO projects(id,title,introduction,cover_image) SELECT 1,?,?,? WHERE NOT EXISTS(SELECT 1 FROM projects WHERE id=1)")->execute(['Hành trình Non Sắc Nón','Từ xưa đến nay, Non Sắc Nón ghi lại những con người, mục tiêu và khoảnh khắc làm nên hành trình của dự án.',photo_asset('A6')['src']]);
$foreign=$pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='gallery_items' AND COLUMN_NAME='phase_id' AND REFERENCED_TABLE_NAME='project_phases'")->fetchAll(PDO::FETCH_COLUMN);
$types=$pdo->query("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='project_phases' AND COLUMN_NAME='id'")->fetchColumn();
$galleryType=$pdo->query("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='gallery_items' AND COLUMN_NAME='phase_id'")->fetchColumn();
if(strpos($types,'tinyint')!==false||strpos($galleryType,'tinyint')!==false){
 foreach($foreign as $name){if(!preg_match('/^[a-zA-Z0-9_]+$/',$name))throw new RuntimeException('Invalid constraint name');$pdo->exec('ALTER TABLE gallery_items DROP FOREIGN KEY `'.$name.'`');}
 $pdo->exec('ALTER TABLE project_phases MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT, MODIFY code VARCHAR(32) NOT NULL');
 $pdo->exec('ALTER TABLE gallery_items MODIFY phase_id INT UNSIGNED NULL');
}
foreach(['project_id'=>'INT UNSIGNED NOT NULL DEFAULT 1','sort_order'=>'INT UNSIGNED NOT NULL DEFAULT 0','image_path'=>'VARCHAR(255) NULL'] as $field=>$definition){
 $q=$pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='project_phases' AND COLUMN_NAME=?");$q->execute([$field]);
 if(!$q->fetchColumn())$pdo->exec('ALTER TABLE project_phases ADD COLUMN '.$field.' '.$definition);
}
$pdo->exec('UPDATE project_phases SET sort_order=id WHERE sort_order=0');
foreach([['gallery_items','phase_id','project_phases','fk_gallery_project_phase'],['project_phases','project_id','projects','fk_phase_project']] as [$table,$column,$parent,$name]){
 $q=$pdo->prepare('SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=? AND REFERENCED_TABLE_NAME=?');$q->execute([$table,$column,$parent]);
 if(!$q->fetchColumn())$pdo->exec("ALTER TABLE $table ADD CONSTRAINT $name FOREIGN KEY ($column) REFERENCES $parent(id) ON DELETE ".($table==='gallery_items'?'SET NULL':'RESTRICT'));
}
echo "Multi-project migration complete. Existing phases and photos retained.\n";
