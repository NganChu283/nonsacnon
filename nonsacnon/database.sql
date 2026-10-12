-- CREATE DATABASE IF NOT EXISTS nonsacnon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE nonsacnon;
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS contact_messages(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,email VARCHAR(190) NOT NULL,phone VARCHAR(30),message TEXT NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS is_read TINYINT(1) NOT NULL DEFAULT 0;
CREATE TABLE IF NOT EXISTS workshops(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(190) NOT NULL,event_date DATETIME NULL,location VARCHAR(190) NOT NULL DEFAULT 'Sẽ cập nhật',capacity INT NOT NULL DEFAULT 30,is_open TINYINT(1) NOT NULL DEFAULT 1);
CREATE TABLE IF NOT EXISTS registrations(id INT AUTO_INCREMENT PRIMARY KEY,workshop_id INT NOT NULL,name VARCHAR(120) NOT NULL,email VARCHAR(190) NOT NULL,phone VARCHAR(30),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,UNIQUE KEY uq(workshop_id,email),FOREIGN KEY(workshop_id) REFERENCES workshops(id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,email VARCHAR(190) NOT NULL UNIQUE,password_hash VARCHAR(255) NOT NULL,role ENUM('admin','editor','organizer') NOT NULL DEFAULT 'editor',is_active TINYINT(1) NOT NULL DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS stories(id INT AUTO_INCREMENT PRIMARY KEY,slug VARCHAR(200) NOT NULL UNIQUE,title VARCHAR(220) NOT NULL,category VARCHAR(40) NOT NULL,excerpt VARCHAR(500) NOT NULL DEFAULT '',body MEDIUMTEXT NOT NULL,cover_image VARCHAR(255) NULL,tone VARCHAR(10) NOT NULL DEFAULT 'red',status ENUM('draft','published') NOT NULL DEFAULT 'draft',author_id INT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS gallery_items(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(190) NOT NULL,category VARCHAR(40) NOT NULL,tone VARCHAR(10) NOT NULL DEFAULT 'red',image_path VARCHAR(255) NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
INSERT INTO workshops(title,capacity) SELECT 'Chạm di sản, nhịp sống trẻ',30 WHERE NOT EXISTS(SELECT 1 FROM workshops);
INSERT INTO stories(slug,title,category,excerpt,body,status,tone) SELECT * FROM (
SELECT 'khi-gia-tri-van-hoa-gap-nhip-song-hien-dai' a,'Khi giá trị văn hóa gặp nhịp sống hiện đại' b,'Văn hóa' c,'Người trẻ đang nhìn lại chiếc nón lá bằng con mắt mới.' d,'Nội dung mẫu, hãy thay bằng bài thật trong trang quản trị.\n\nChiếc nón lá quen thuộc đang được vẽ, chụp và kể lại bằng nhiều cách khác nhau.' e,'published' f,'red' g
UNION ALL SELECT 'chuyen-nghe-sac-non','Chuyện nghề sắc nón','Làng nghề','Một ngày bên những người giữ nghề chằm nón.','Nội dung mẫu, hãy thay bằng bài thật trong trang quản trị.\n\nMỗi chiếc nón đi qua nhiều công đoạn thủ công trước khi đến tay người dùng.','published','gold'
UNION ALL SELECT 'doi-tay-nguoi-tho','Đôi tay người thợ','Nghệ nhân','Những mũi chỉ kiên nhẫn qua nhiều thế hệ.','Nội dung mẫu, hãy thay bằng bài thật trong trang quản trị.\n\nNghề được giữ lại nhờ những người chịu bỏ thời gian để học và truyền lại.','published','leaf') t
WHERE NOT EXISTS(SELECT 1 FROM stories);
INSERT INTO gallery_items(title,category,tone) SELECT * FROM (
SELECT 'Làng nón Chuông' a,'Làng nghề' b,'gold' c UNION ALL SELECT 'Bàn tay chằm nón','Nghệ nhân','red' UNION ALL SELECT 'Nón lá và lớp lá dằn','Nón lá','leaf'
UNION ALL SELECT 'Buổi workshop đầu tiên','Workshop','brown' UNION ALL SELECT 'Nón xưa họa nay','Tác phẩm','red') t
WHERE NOT EXISTS(SELECT 1 FROM gallery_items);
