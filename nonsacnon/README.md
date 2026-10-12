1. Copy thư mục nonsacnon vào C:\xampp\htdocs\ ; Start Apache và MySQL trong XAMPP
2. phpMyAdmin > Import > database.sql (chạy lại nhiều lần vẫn an toàn, không mất dữ liệu)
3. Mở http://localhost/nonsacnon/admin/setup.php để tạo quản trị viên đầu tiên (chỉ dùng được 1 lần)
4. Đăng nhập tại http://localhost/nonsacnon/admin/ ; thêm tài khoản khác ở mục Người dùng
Vai trò: admin = toàn quyền; editor = bài viết + thư viện ảnh; organizer = workshop, đăng ký, tin nhắn.
Bài viết và ảnh mẫu trong database.sql chỉ để thử, hãy thay bằng nội dung thật.

Google Form: vào Quản trị > Workshop > Biểu mẫu đăng ký workshop, dán link dành cho người trả lời rồi lưu. Link đang để trống. Đăng ký cũ vẫn nằm trong mục Đăng ký. Máy chủ cần quyền ghi includes/workshop-form.json.

Quản lý nhiều dự án (09/10/2026):
- Sau khi import database.sql, chạy lần lượt `php migrations/20261009_project_phases.php` và `php migrations/20261009_multi_projects.php`. Website đã chạy bản cập nhật trên MySQL cục bộ. Các migration chạy lại không ghi đè nội dung đã sửa.
- Quản trị > Dự án > + Thêm dự án: nhập tên, giới thiệu, chọn ảnh đại diện rồi Lưu dự án.
- Chọn dự án > + Thêm giai đoạn: nhập tên, mô tả, nội dung, ảnh và thứ tự rồi Lưu giai đoạn. Có thể sửa lại các giai đoạn hiện có.
- Quản trị > Thư viện ảnh: chọn Dự án và Giai đoạn khi thêm ảnh. Mỗi giai đoạn chỉ thuộc một dự án; ảnh được hiển thị trong đúng dự án đó.
- Trang chủ #du-an và projects.php hiển thị danh sách dự án. Bấm Khám phá dự án để mở trang chi tiết project.php?id=... .
- Hành trình Non Sắc Nón, bốn phase, nội dung chi tiết và ảnh cũ được giữ nguyên trong dự án gốc. gallery.php#ph1…ph4 vẫn hoạt động.
- Chỉ admin và editor có quyền quản lý. Kết nối và đăng nhập giữ nguyên; thao tác lưu kiểm tra CSRF.
- Máy chủ cần quyền ghi assets/uploads. Ảnh JPG/PNG/WebP tối đa 10MB; hỗ trợ cả máy chưa bật GD/Imagick.
- demo-du-an.php là bản thử độc lập. Dữ liệu localStorage của demo không tự đưa vào MySQL.

Gom CSS/JavaScript để upload (09/10/2026):
- CSS: site.css (trang công khai), admin.css (quản trị), login.css (đăng nhập), base.css (thiết lập ban đầu).
- JavaScript: site.js (trang công khai), admin.js (quản trị/editor/workshop/thư viện/xóa dự án), login.js (đăng nhập).
- Giữ nguyên nội dung và thứ tự trong từng bundle; không minify. URL ảnh trong CSS vẫn tương đối với assets/css.
- Khi cập nhật phải upload đồng bộ PHP cùng toàn bộ assets/css và assets/js; không trộn với các file CSS/JS cũ.
- database.sql là schema ban đầu. Khi chuyển host cần bản xuất database hiện tại để giữ đủ dự án, giai đoạn, tài khoản và nội dung.
- Bản sao lưu trước khi gom và gói upload đầy đủ nằm trong .design-review/compact ở workspace, không đưa thư mục này lên hosting.
