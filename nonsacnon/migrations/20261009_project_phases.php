<?php
// Run from CLI only: php migrations/20261009_project_phases.php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../includes/db.php';
$pdo->exec("CREATE TABLE IF NOT EXISTS project_phases(id TINYINT UNSIGNED PRIMARY KEY,code VARCHAR(8) NOT NULL UNIQUE,title VARCHAR(190) NOT NULL,description VARCHAR(1000) NOT NULL,body MEDIUMTEXT NOT NULL,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
$column=$pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='gallery_items' AND COLUMN_NAME='phase_id'")->fetchColumn();
if(!$column)$pdo->exec('ALTER TABLE gallery_items ADD COLUMN phase_id TINYINT UNSIGNED NULL');
$pdo->beginTransaction();
try{
  $insert=$pdo->prepare("INSERT INTO project_phases(id,code,title,description,body) SELECT ?,?,?,?,'' WHERE NOT EXISTS(SELECT 1 FROM project_phases WHERE code=?)");
  foreach(LIBRARY_PHASES as $code=>$phase)$insert->execute([(int)substr($code,2),$code,$phase['title'],$phase['description'],$code]);
  $rows=$pdo->query('SELECT id,category,image_path FROM gallery_items WHERE phase_id IS NULL')->fetchAll();
  $update=$pdo->prepare('UPDATE gallery_items SET phase_id=? WHERE id=? AND phase_id IS NULL');
  foreach($rows as $row)$update->execute([(int)substr(library_phase_item($row),2),$row['id']]);
  $pdo->commit();
}catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();throw $ex;}
$constraint=$pdo->query("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='gallery_items' AND COLUMN_NAME='phase_id' AND REFERENCED_TABLE_NAME='project_phases'")->fetchColumn();
if(!$constraint)$pdo->exec('ALTER TABLE gallery_items ADD CONSTRAINT fk_gallery_project_phase FOREIGN KEY (phase_id) REFERENCES project_phases(id) ON DELETE SET NULL');
echo "Project phases migration complete. Existing content preserved.\n";
