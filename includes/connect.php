<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
try {
    if (class_exists('PDO')) {
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4", // Hỗ trợ tiếng Việt đầy đủ
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Đẩy lỗi vào ngoại lệ
        );
        $dns = _DRIVER . ':host=' . _HOST . ';dbname=' . _DB; // Sửa khoảng trắng thừa
        $conn = new PDO($dns, _USER, _PASS, $options);
    } else {
        die('PDO không được hỗ trợ trên server này');
    }
} catch (Exception $ex) {
    die('Lỗi kết nối database: ' . $ex->getMessage());
}