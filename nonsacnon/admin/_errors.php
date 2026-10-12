<?php
function admin_save_error(Throwable $error): string {
    $code = $error instanceof PDOException ? (int)($error->errorInfo[1] ?? 0) : 0;
    $messages = [
        1044=>'Tài khoản MySQL không có quyền ghi vào database.',
        1142=>'Tài khoản MySQL không được phép thực hiện thao tác này.',
        1146=>'Database trên host đang thiếu bảng. Cần import bản SQL đầy đủ.',
        1054=>'Database trên host đang thiếu cột. Cần cập nhật cấu trúc từ bản SQL đầy đủ.',
        1366=>'MySQL không nhận được nội dung tiếng Việt. Kiểm tra kết nối và cột dữ liệu dùng utf8mb4.',
        1062=>'Dữ liệu bị trùng với một bản ghi đã có.',
        1048=>'Một trường bắt buộc chưa có giá trị.',
        1406=>'Nội dung vượt quá độ dài cột trong database.',
        1114=>'Database đã hết dung lượng.',
        1290=>'Database trên host đang hạn chế thao tác ghi.'
    ];
    $detail = $messages[$code] ?? 'Máy chủ không thể hoàn tất thao tác lưu.';
    if ($error instanceof Error && preg_match('/(?:undefined function|Class)\s+["\x27]?([a-zA-Z0-9_\\\\]+)(?:\(\))?/i', $error->getMessage(), $match)) {
        $detail = 'Máy chủ thiếu hàm hoặc thư viện PHP: '.$match[1].'.';
    }
    $missingColumn = null;
    if ($code === 1054 && preg_match("/Unknown column '([a-zA-Z0-9_.]+)'/i", $error->getMessage(), $match)) {
        $missingColumn = $match[1];
        $detail = 'Database trên host đang thiếu cột: '.$missingColumn.'. Cần bổ sung cột theo cấu trúc hiện tại.';
    }
    $reference = bin2hex(random_bytes(4));
    $record = ['time'=>gmdate('c'),'reference'=>$reference,'type'=>get_class($error),'mysql_code'=>$code,'detail'=>$detail,'missing_column'=>$missingColumn];
    @file_put_contents(__DIR__.'/../includes/admin-errors.log', json_encode($record,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND|LOCK_EX);
    error_log('[NonSacNon '.$reference.'] '.get_class($error).' '.$error->getMessage());
    return $detail.' Mã lỗi: '.($code ?: get_class($error)).' / '.$reference;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && user()) {
    set_exception_handler(function(Throwable $error){
        $message=admin_save_error($error);
        if(!headers_sent()){http_response_code(500);header('Content-Type: text/html; charset=UTF-8');}
        echo '<div role="alert" style="padding:24px"><p>'.e($message).'</p><p><a href="upload_check.php">Mở trang chẩn đoán</a> · <a href="javascript:history.back()">Quay lại</a></p></div>';
    });
}
